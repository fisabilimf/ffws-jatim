@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Predicted Discharge Details</h1>
        <div>
            <a href="{{ route('admin.predicted-calculated-discharges.edit', $predictedCalculatedDischarge) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.predicted-calculated-discharges.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Predicted Discharge Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Sensor Code:</label>
                                <p class="text-gray-800"><code>{{ $predictedCalculatedDischarge->mas_sensor_code }}</code></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Sensor Name:</label>
                                <p class="text-gray-800">{{ $predictedCalculatedDischarge->sensor->sensor_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Predicted Value:</label>
                                <p class="text-warning h5">{{ number_format($predictedCalculatedDischarge->predicted_value, 3) }}</p>
                                <small class="text-muted">Forecasted sensor reading</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Predicted Discharge:</label>
                                <p class="text-primary h5">{{ number_format($predictedCalculatedDischarge->predicted_discharge, 3) }}</p>
                                <small class="text-muted">Calculated using rating curve</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Rating Curve:</label>
                                <p class="text-gray-800">
                                    {{ $predictedCalculatedDischarge->ratingCurve->name ?? 'N/A' }}
                                    @if($predictedCalculatedDischarge->ratingCurve)
                                        <span class="badge badge-secondary ml-2">{{ $predictedCalculatedDischarge->ratingCurve->formula_type }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Calculated At:</label>
                                <p class="text-gray-800">{{ $predictedCalculatedDischarge->calculated_at?->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prediction Analysis Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Prediction Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Prediction Information</h6>
                                <p class="mb-2">
                                    This predicted discharge represents a forecasted value based on predictive modeling 
                                    or machine learning algorithms. The prediction was calculated using:
                                </p>
                                <ul class="mb-0">
                                    <li><strong>Predicted Sensor Value:</strong> {{ number_format($predictedCalculatedDischarge->predicted_value, 3) }}</li>
                                    <li><strong>Rating Curve Formula:</strong> {{ $predictedCalculatedDischarge->ratingCurve->formula_type ?? 'N/A' }}</li>
                                    <li><strong>Resulting Discharge:</strong> {{ number_format($predictedCalculatedDischarge->predicted_discharge, 3) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if($predictedCalculatedDischarge->ratingCurve)
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Formula Details:</h6>
                            <div class="bg-light p-3 rounded">
                                <p><strong>Type:</strong> {{ $predictedCalculatedDischarge->ratingCurve->formula_type }}</p>
                                <p><strong>Parameters:</strong> <code>{{ $predictedCalculatedDischarge->ratingCurve->parameters }}</code></p>
                                @if($predictedCalculatedDischarge->ratingCurve->description)
                                <p class="mb-0"><strong>Description:</strong> {{ $predictedCalculatedDischarge->ratingCurve->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Rating Curve Details Card -->
            @if($predictedCalculatedDischarge->ratingCurve)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rating Curve Details</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Name:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->ratingCurve->name }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Formula Type:</label>
                        <p class="text-gray-800">
                            <span class="badge badge-info">{{ $predictedCalculatedDischarge->ratingCurve->formula_type }}</span>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Parameters:</label>
                        <div class="bg-light p-2 rounded">
                            <code>{{ $predictedCalculatedDischarge->ratingCurve->parameters }}</code>
                        </div>
                    </div>

                    @if($predictedCalculatedDischarge->ratingCurve->description)
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Description:</label>
                        <p class="text-gray-800 small">{{ $predictedCalculatedDischarge->ratingCurve->description }}</p>
                    </div>
                    @endif

                    <div class="text-center">
                        <a href="{{ route('admin.rating-curves.show', $predictedCalculatedDischarge->ratingCurve) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chart-line"></i> View Rating Curve
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sensor Details Card -->
            @if($predictedCalculatedDischarge->sensor)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sensor Details</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Sensor Name:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->sensor->sensor_name }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Sensor Type:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->sensor->sensor_type ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Location:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->sensor->location ?? 'N/A' }}</p>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('admin.mas-sensors.show', $predictedCalculatedDischarge->sensor->sensor_code) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-sensor"></i> View Sensor
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Metadata Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">ID:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->id }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Created:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->created_at?->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Last Updated:</label>
                        <p class="text-gray-800">{{ $predictedCalculatedDischarge->updated_at?->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection