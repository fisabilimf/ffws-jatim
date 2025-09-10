"""
Confidence scoring module for FFWS forecasting predictions.

This module calculates confidence scores based on multiple factors:
1. Data quality and completeness
2. Model prediction consistency 
3. Historical accuracy patterns
4. Input feature stability
5. Prediction variance analysis
"""

import numpy as np
from typing import Dict, List, Tuple, Optional
from datetime import datetime, timedelta
from sqlalchemy.orm import Session
from sqlalchemy import select, func, desc
from .models import DataActual, DataPrediction


class ConfidenceScorer:
    """Calculate confidence scores for forecasting predictions."""
    
    def __init__(self):
        self.weights = {
            'data_quality': 0.25,      # How complete/clean is input data
            'model_consistency': 0.20,  # How consistent are model predictions
            'historical_accuracy': 0.25, # Past model performance
            'input_stability': 0.15,    # Stability of recent input features
            'prediction_variance': 0.15  # Variance in prediction outputs
        }
    
    def calculate_confidence(
        self,
        session: Session,
        sensor_code: str,
        model_code: str,
        input_data: np.ndarray,
        predictions: np.ndarray,
        feature_names: Optional[List[str]] = None
    ) -> float:
        """
        Calculate overall confidence score (0.0 to 1.0) for predictions.
        
        Args:
            session: Database session
            sensor_code: Sensor identifier
            model_code: Model identifier
            input_data: Input features used for prediction (shape: time_steps, features)
            predictions: Model prediction outputs
            feature_names: Names of input features
            
        Returns:
            Confidence score between 0.0 and 1.0
        """
        scores = {}
        
        # 1. Data Quality Score
        scores['data_quality'] = self._calculate_data_quality_score(input_data)
        
        # 2. Model Consistency Score (requires multiple predictions)
        scores['model_consistency'] = self._calculate_model_consistency_score(predictions)
        
        # 3. Historical Accuracy Score
        scores['historical_accuracy'] = self._calculate_historical_accuracy_score(
            session, sensor_code, model_code
        )
        
        # 4. Input Stability Score
        scores['input_stability'] = self._calculate_input_stability_score(input_data)
        
        # 5. Prediction Variance Score
        scores['prediction_variance'] = self._calculate_prediction_variance_score(predictions)
        
        # Calculate weighted average
        confidence = sum(
            scores[component] * self.weights[component] 
            for component in self.weights.keys()
        )
        
        # Ensure confidence is between 0 and 1
        confidence = max(0.0, min(1.0, confidence))
        
        return confidence
    
    def _calculate_data_quality_score(self, input_data: np.ndarray) -> float:
        """
        Calculate data quality score based on completeness and validity.
        
        Args:
            input_data: Input features (time_steps, features)
            
        Returns:
            Score between 0.0 and 1.0
        """
        if input_data.size == 0:
            return 0.0
        
        # Check for missing values
        missing_ratio = np.isnan(input_data).sum() / input_data.size
        completeness_score = 1.0 - missing_ratio
        
        # Check for extreme outliers (values beyond 3 standard deviations)
        outlier_count = 0
        total_values = 0
        
        for feature_idx in range(input_data.shape[1]):
            feature_data = input_data[:, feature_idx]
            valid_data = feature_data[~np.isnan(feature_data)]
            
            if len(valid_data) > 1:
                mean = np.mean(valid_data)
                std = np.std(valid_data)
                if std > 0:
                    z_scores = np.abs((valid_data - mean) / std)
                    outlier_count += np.sum(z_scores > 3)
                total_values += len(valid_data)
        
        outlier_ratio = outlier_count / max(total_values, 1)
        outlier_score = 1.0 - min(outlier_ratio, 1.0)
        
        # Combine completeness and outlier scores
        quality_score = (completeness_score * 0.7 + outlier_score * 0.3)
        
        return quality_score
    
    def _calculate_model_consistency_score(self, predictions: np.ndarray) -> float:
        """
        Calculate model consistency based on prediction smoothness.
        
        Args:
            predictions: Model prediction outputs
            
        Returns:
            Score between 0.0 and 1.0
        """
        if len(predictions) < 2:
            return 0.8  # Neutral score for single predictions
        
        # Calculate differences between consecutive predictions
        diffs = np.diff(predictions)
        
        # Measure relative variation
        mean_pred = np.mean(np.abs(predictions))
        if mean_pred == 0:
            return 0.5  # Neutral score for zero predictions
        
        # Normalize differences by mean prediction
        relative_variation = np.std(diffs) / mean_pred
        
        # Convert to consistency score (lower variation = higher consistency)
        consistency_score = np.exp(-relative_variation * 2)  # Exponential decay
        
        return min(consistency_score, 1.0)
    
    def _calculate_historical_accuracy_score(
        self,
        session: Session,
        sensor_code: str,
        model_code: str,
        lookback_days: int = 7
    ) -> float:
        """
        Calculate historical accuracy based on past prediction performance.
        
        Args:
            session: Database session
            sensor_code: Sensor identifier
            model_code: Model identifier
            lookback_days: Days to look back for historical data
            
        Returns:
            Score between 0.0 and 1.0
        """
        try:
            # Get recent predictions and actual values
            cutoff_date = datetime.utcnow() - timedelta(days=lookback_days)
            
            # Query recent predictions
            pred_query = select(DataPrediction).where(
                DataPrediction.mas_sensor_code == sensor_code,
                DataPrediction.mas_model_code == model_code,
                DataPrediction.prediction_run_at >= cutoff_date
            ).order_by(desc(DataPrediction.prediction_run_at)).limit(50)
            
            recent_predictions = session.execute(pred_query).scalars().all()
            
            if len(recent_predictions) < 5:
                return 0.6  # Neutral score for insufficient historical data
            
            # Calculate accuracy metrics
            errors = []
            for pred in recent_predictions:
                # Find actual value close to prediction time
                actual_query = select(DataActual.value).where(
                    DataActual.mas_sensor_code == sensor_code,
                    DataActual.received_at >= pred.prediction_for_ts - timedelta(minutes=5),
                    DataActual.received_at <= pred.prediction_for_ts + timedelta(minutes=5)
                ).limit(1)
                
                actual_result = session.execute(actual_query).scalar()
                
                if actual_result is not None:
                    error = abs(pred.predicted_value - actual_result)
                    # Normalize error by actual value (percentage error)
                    if actual_result != 0:
                        relative_error = error / abs(actual_result)
                        errors.append(relative_error)
                    else:
                        errors.append(error)  # Absolute error for zero values
            
            if len(errors) == 0:
                return 0.6  # Neutral score
            
            # Calculate Mean Absolute Percentage Error (MAPE)
            mape = np.mean(errors)
            
            # Convert MAPE to accuracy score (lower MAPE = higher accuracy)
            accuracy_score = np.exp(-mape * 2)  # Exponential decay
            
            return min(accuracy_score, 1.0)
            
        except Exception:
            return 0.6  # Neutral score on error
    
    def _calculate_input_stability_score(self, input_data: np.ndarray) -> float:
        """
        Calculate input stability based on feature variance over time.
        
        Args:
            input_data: Input features (time_steps, features)
            
        Returns:
            Score between 0.0 and 1.0
        """
        if input_data.shape[0] < 2:
            return 0.7  # Neutral score for insufficient time steps
        
        stability_scores = []
        
        for feature_idx in range(input_data.shape[1]):
            feature_data = input_data[:, feature_idx]
            valid_data = feature_data[~np.isnan(feature_data)]
            
            if len(valid_data) < 2:
                stability_scores.append(0.5)  # Neutral for insufficient data
                continue
            
            # Calculate coefficient of variation
            mean_val = np.mean(valid_data)
            std_val = np.std(valid_data)
            
            if mean_val == 0:
                if std_val == 0:
                    stability_scores.append(1.0)  # Perfect stability (all zeros)
                else:
                    stability_scores.append(0.0)  # High variation around zero
            else:
                cv = std_val / abs(mean_val)  # Coefficient of variation
                stability_score = np.exp(-cv)  # Higher stability for lower CV
                stability_scores.append(min(stability_score, 1.0))
        
        return np.mean(stability_scores)
    
    def _calculate_prediction_variance_score(self, predictions: np.ndarray) -> float:
        """
        Calculate prediction variance score (lower variance = higher confidence).
        
        Args:
            predictions: Model prediction outputs
            
        Returns:
            Score between 0.0 and 1.0
        """
        if len(predictions) < 2:
            return 0.8  # Neutral score for single predictions
        
        # Calculate relative standard deviation
        mean_pred = np.mean(predictions)
        std_pred = np.std(predictions)
        
        if mean_pred == 0:
            if std_pred == 0:
                return 1.0  # Perfect consistency (all zeros)
            else:
                return 0.1  # High variance around zero
        
        relative_std = std_pred / abs(mean_pred)
        
        # Convert to confidence score (lower relative std = higher confidence)
        variance_score = np.exp(-relative_std * 3)
        
        return min(variance_score, 1.0)
    
    def get_confidence_breakdown(
        self,
        session: Session,
        sensor_code: str,
        model_code: str,
        input_data: np.ndarray,
        predictions: np.ndarray,
        feature_names: Optional[List[str]] = None
    ) -> Dict[str, float]:
        """
        Get detailed breakdown of confidence score components.
        
        Returns:
            Dictionary with individual component scores
        """
        breakdown = {}
        
        breakdown['data_quality'] = self._calculate_data_quality_score(input_data)
        breakdown['model_consistency'] = self._calculate_model_consistency_score(predictions)
        breakdown['historical_accuracy'] = self._calculate_historical_accuracy_score(
            session, sensor_code, model_code
        )
        breakdown['input_stability'] = self._calculate_input_stability_score(input_data)
        breakdown['prediction_variance'] = self._calculate_prediction_variance_score(predictions)
        
        # Calculate overall score
        breakdown['overall'] = sum(
            breakdown[component] * self.weights[component] 
            for component in self.weights.keys()
        )
        
        return breakdown


def calculate_confidence_score(
    session: Session,
    sensor_code: str,
    model_code: str,
    input_data: np.ndarray,
    predictions: np.ndarray,
    feature_names: Optional[List[str]] = None
) -> float:
    """
    Convenience function to calculate confidence score.
    
    Returns:
        Confidence score between 0.0 and 1.0
    """
    scorer = ConfidenceScorer()
    return scorer.calculate_confidence(
        session, sensor_code, model_code, input_data, predictions, feature_names
    )
