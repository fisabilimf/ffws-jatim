@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Predicted Calculated Discharge</h1>
        <a href="{{ route('admin.predicted-calculated-discharges.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Predicted Discharge Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.predicted-calculated-discharges.update', $predictedCalculatedDischarge) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mas_sensor_code">Sensor *</label>
                            <select class="form-control @error('mas_sensor_code') is-invalid @enderror" 
                                    id="mas_sensor_code" name="mas_sensor_code" required>
                                <option value="">Select Sensor</option>
                                @foreach($sensors as $sensor)
                                    <option value="{{ $sensor->sensor_code }}" 
                                        {{ old('mas_sensor_code', $predictedCalculatedDischarge->mas_sensor_code) == $sensor->sensor_code ? 'selected' : '' }}>
                                        {{ $sensor->sensor_name }} ({{ $sensor->sensor_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mas_sensor_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rating_curve_id">Rating Curve *</label>
                            <select class="form-control @error('rating_curve_id') is-invalid @enderror" 
                                    id="rating_curve_id" name="rating_curve_id" required>
                                <option value="">Select Rating Curve</option>
                                @foreach($ratingCurves as $curve)
                                    <option value="{{ $curve->id }}" 
                                        {{ old('rating_curve_id', $predictedCalculatedDischarge->rating_curve_id) == $curve->id ? 'selected' : '' }}>
                                        {{ $curve->name }} ({{ $curve->formula_type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rating_curve_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="predicted_value">Predicted Value *</label>
                            <input type="number" 
                                   class="form-control @error('predicted_value') is-invalid @enderror" 
                                   id="predicted_value" 
                                   name="predicted_value" 
                                   value="{{ old('predicted_value', $predictedCalculatedDischarge->predicted_value) }}" 
                                   step="0.001"
                                   min="0"
                                   placeholder="Enter predicted value"
                                   required>
                            @error('predicted_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                The predicted sensor value (e.g., forecasted water level)
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="predicted_discharge">Predicted Discharge *</label>
                            <input type="number" 
                                   class="form-control @error('predicted_discharge') is-invalid @enderror" 
                                   id="predicted_discharge" 
                                   name="predicted_discharge" 
                                   value="{{ old('predicted_discharge', $predictedCalculatedDischarge->predicted_discharge) }}" 
                                   step="0.001"
                                   min="0"
                                   placeholder="Enter predicted discharge"
                                   required>
                            @error('predicted_discharge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                The predicted discharge value based on the rating curve
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="calculated_at">Calculated At *</label>
                            <input type="datetime-local" 
                                   class="form-control @error('calculated_at') is-invalid @enderror" 
                                   id="calculated_at" 
                                   name="calculated_at" 
                                   value="{{ old('calculated_at', $predictedCalculatedDischarge->calculated_at?->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('calculated_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Current Information:</h6>
                            <p><strong>Current Sensor:</strong> {{ $predictedCalculatedDischarge->sensor->sensor_name ?? 'N/A' }} ({{ $predictedCalculatedDischarge->mas_sensor_code }})</p>
                            <p><strong>Current Rating Curve:</strong> {{ $predictedCalculatedDischarge->ratingCurve->name ?? 'N/A' }}</p>
                            <p><strong>Created:</strong> {{ $predictedCalculatedDischarge->created_at?->format('Y-m-d H:i:s') }}</p>
                            <p class="mb-0"><strong>Last Updated:</strong> {{ $predictedCalculatedDischarge->updated_at?->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Predicted Discharge
                    </button>
                    <a href="{{ route('admin.predicted-calculated-discharges.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection