@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Calculated Discharge</h1>
        <a href="{{ route('admin.calculated-discharges.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Calculated Discharge Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.calculated-discharges.update', $calculatedDischarge) }}">
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
                                        {{ old('mas_sensor_code', $calculatedDischarge->mas_sensor_code) == $sensor->sensor_code ? 'selected' : '' }}>
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
                                        {{ old('rating_curve_id', $calculatedDischarge->rating_curve_id) == $curve->id ? 'selected' : '' }}>
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
                            <label for="sensor_value">Sensor Value *</label>
                            <input type="number" 
                                   class="form-control @error('sensor_value') is-invalid @enderror" 
                                   id="sensor_value" 
                                   name="sensor_value" 
                                   value="{{ old('sensor_value', $calculatedDischarge->sensor_value) }}" 
                                   step="0.001"
                                   min="0"
                                   placeholder="Enter sensor value"
                                   required>
                            @error('sensor_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                The measured sensor value (e.g., water level)
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_discharge">Sensor Discharge *</label>
                            <input type="number" 
                                   class="form-control @error('sensor_discharge') is-invalid @enderror" 
                                   id="sensor_discharge" 
                                   name="sensor_discharge" 
                                   value="{{ old('sensor_discharge', $calculatedDischarge->sensor_discharge) }}" 
                                   step="0.001"
                                   min="0"
                                   placeholder="Enter calculated discharge"
                                   required>
                            @error('sensor_discharge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                The calculated discharge value based on the rating curve
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
                                   value="{{ old('calculated_at', $calculatedDischarge->calculated_at?->format('Y-m-d\TH:i')) }}" 
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
                            <p><strong>Current Sensor:</strong> {{ $calculatedDischarge->sensor->sensor_name ?? 'N/A' }} ({{ $calculatedDischarge->mas_sensor_code }})</p>
                            <p><strong>Current Rating Curve:</strong> {{ $calculatedDischarge->ratingCurve->name ?? 'N/A' }}</p>
                            <p><strong>Created:</strong> {{ $calculatedDischarge->created_at?->format('Y-m-d H:i:s') }}</p>
                            <p class="mb-0"><strong>Last Updated:</strong> {{ $calculatedDischarge->updated_at?->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Calculated Discharge
                    </button>
                    <a href="{{ route('admin.calculated-discharges.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection