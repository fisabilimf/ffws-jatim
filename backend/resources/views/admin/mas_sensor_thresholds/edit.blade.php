@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Sensor Threshold</h1>
        <a href="{{ route('admin.mas-sensor-thresholds.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sensor Threshold Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.mas-sensor-thresholds.update', $masSensorThreshold) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_code">Threshold Code *</label>
                            <input type="text" 
                                   class="form-control @error('sensor_thresholds_code') is-invalid @enderror" 
                                   id="sensor_thresholds_code" 
                                   name="sensor_thresholds_code" 
                                   value="{{ old('sensor_thresholds_code', $masSensorThreshold->sensor_thresholds_code) }}" 
                                   required>
                            @error('sensor_thresholds_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_name">Threshold Name *</label>
                            <input type="text" 
                                   class="form-control @error('sensor_thresholds_name') is-invalid @enderror" 
                                   id="sensor_thresholds_name" 
                                   name="sensor_thresholds_name" 
                                   value="{{ old('sensor_thresholds_name', $masSensorThreshold->sensor_thresholds_name) }}" 
                                   required>
                            @error('sensor_thresholds_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="text-primary mb-3">Threshold Values & Colors</h5>

                <!-- Threshold Value 1 -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_1">Threshold Value 1</label>
                            <input type="number" 
                                   step="0.01"
                                   class="form-control @error('sensor_thresholds_value_1') is-invalid @enderror" 
                                   id="sensor_thresholds_value_1" 
                                   name="sensor_thresholds_value_1" 
                                   value="{{ old('sensor_thresholds_value_1', $masSensorThreshold->sensor_thresholds_value_1) }}">
                            @error('sensor_thresholds_value_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_1_color">Value 1 Color</label>
                            <input type="color" 
                                   class="form-control @error('sensor_thresholds_value_1_color') is-invalid @enderror" 
                                   id="sensor_thresholds_value_1_color" 
                                   name="sensor_thresholds_value_1_color" 
                                   value="{{ old('sensor_thresholds_value_1_color', $masSensorThreshold->sensor_thresholds_value_1_color ?? '#28a745') }}">
                            @error('sensor_thresholds_value_1_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Threshold Value 2 -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_2">Threshold Value 2</label>
                            <input type="number" 
                                   step="0.01"
                                   class="form-control @error('sensor_thresholds_value_2') is-invalid @enderror" 
                                   id="sensor_thresholds_value_2" 
                                   name="sensor_thresholds_value_2" 
                                   value="{{ old('sensor_thresholds_value_2', $masSensorThreshold->sensor_thresholds_value_2) }}">
                            @error('sensor_thresholds_value_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_2_color">Value 2 Color</label>
                            <input type="color" 
                                   class="form-control @error('sensor_thresholds_value_2_color') is-invalid @enderror" 
                                   id="sensor_thresholds_value_2_color" 
                                   name="sensor_thresholds_value_2_color" 
                                   value="{{ old('sensor_thresholds_value_2_color', $masSensorThreshold->sensor_thresholds_value_2_color ?? '#ffc107') }}">
                            @error('sensor_thresholds_value_2_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Threshold Value 3 -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_3">Threshold Value 3</label>
                            <input type="number" 
                                   step="0.01"
                                   class="form-control @error('sensor_thresholds_value_3') is-invalid @enderror" 
                                   id="sensor_thresholds_value_3" 
                                   name="sensor_thresholds_value_3" 
                                   value="{{ old('sensor_thresholds_value_3', $masSensorThreshold->sensor_thresholds_value_3) }}">
                            @error('sensor_thresholds_value_3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_3_color">Value 3 Color</label>
                            <input type="color" 
                                   class="form-control @error('sensor_thresholds_value_3_color') is-invalid @enderror" 
                                   id="sensor_thresholds_value_3_color" 
                                   name="sensor_thresholds_value_3_color" 
                                   value="{{ old('sensor_thresholds_value_3_color', $masSensorThreshold->sensor_thresholds_value_3_color ?? '#fd7e14') }}">
                            @error('sensor_thresholds_value_3_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Threshold Value 4 -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_4">Threshold Value 4</label>
                            <input type="number" 
                                   step="0.01"
                                   class="form-control @error('sensor_thresholds_value_4') is-invalid @enderror" 
                                   id="sensor_thresholds_value_4" 
                                   name="sensor_thresholds_value_4" 
                                   value="{{ old('sensor_thresholds_value_4', $masSensorThreshold->sensor_thresholds_value_4) }}">
                            @error('sensor_thresholds_value_4')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sensor_thresholds_value_4_color">Value 4 Color</label>
                            <input type="color" 
                                   class="form-control @error('sensor_thresholds_value_4_color') is-invalid @enderror" 
                                   id="sensor_thresholds_value_4_color" 
                                   name="sensor_thresholds_value_4_color" 
                                   value="{{ old('sensor_thresholds_value_4_color', $masSensorThreshold->sensor_thresholds_value_4_color ?? '#dc3545') }}">
                            @error('sensor_thresholds_value_4_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Threshold
                    </button>
                    <a href="{{ route('admin.mas-sensor-thresholds.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection