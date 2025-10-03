@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calculated Discharge Details</h1>
        <div>
            <a href="{{ route('admin.calculated-discharges.edit', $calculatedDischarge) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.calculated-discharges.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Discharge Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Sensor Code:</label>
                                <p class="text-gray-800"><code>{{ $calculatedDischarge->mas_sensor_code }}</code></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Sensor Name:</label>
                                <p class="text-gray-800">{{ $calculatedDischarge->sensor->sensor_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Sensor Value:</label>
                                <p class="text-primary h5">{{ number_format($calculatedDischarge->sensor_value, 3) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Calculated Discharge:</label>
                                <p class="text-success h5">{{ number_format($calculatedDischarge->sensor_discharge, 3) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Rating Curve:</label>
                                <p class="text-gray-800">
                                    {{ $calculatedDischarge->ratingCurve->name ?? 'N/A' }}
                                    @if($calculatedDischarge->ratingCurve)
                                        <span class="badge badge-secondary ml-2">{{ $calculatedDischarge->ratingCurve->formula_type }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Calculated At:</label>
                                <p class="text-gray-800">{{ $calculatedDischarge->calculated_at?->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Rating Curve Details Card -->
            @if($calculatedDischarge->ratingCurve)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rating Curve Details</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Name:</label>
                        <p class="text-gray-800">{{ $calculatedDischarge->ratingCurve->name }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Formula Type:</label>
                        <p class="text-gray-800">
                            <span class="badge badge-info">{{ $calculatedDischarge->ratingCurve->formula_type }}</span>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Parameters:</label>
                        <div class="bg-light p-2 rounded">
                            <code>{{ $calculatedDischarge->ratingCurve->parameters }}</code>
                        </div>
                    </div>

                    @if($calculatedDischarge->ratingCurve->description)
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Description:</label>
                        <p class="text-gray-800 small">{{ $calculatedDischarge->ratingCurve->description }}</p>
                    </div>
                    @endif

                    <div class="text-center">
                        <a href="{{ route('admin.rating-curves.show', $calculatedDischarge->ratingCurve) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chart-line"></i> View Rating Curve
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sensor Details Card -->
            @if($calculatedDischarge->sensor)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sensor Details</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Sensor Name:</label>
                        <p class="text-gray-800">{{ $calculatedDischarge->sensor->sensor_name }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Sensor Type:</label>
                        <p class="text-gray-800">{{ $calculatedDischarge->sensor->sensor_type ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Location:</label>
                        <p class="text-gray-800">{{ $calculatedDischarge->sensor->location ?? 'N/A' }}</p>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('admin.mas-sensors.show', $calculatedDischarge->sensor->sensor_code) }}" 
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
                        <p class="text-gray-800">{{ $calculatedDischarge->id }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Created:</label>
                        <p class="text-gray-800">{{ $calculatedDischarge->created_at?->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Last Updated:</label>
                        <p class="text-gray-800">{{ $calculatedDischarge->updated_at?->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection