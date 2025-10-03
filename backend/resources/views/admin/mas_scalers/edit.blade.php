@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Scaler</h1>
        <a href="{{ route('admin.mas-scalers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Scaler Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.mas-scalers.update', $masScaler) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="scaler_code">Scaler Code *</label>
                            <input type="text" 
                                   class="form-control @error('scaler_code') is-invalid @enderror" 
                                   id="scaler_code" 
                                   name="scaler_code" 
                                   value="{{ old('scaler_code', $masScaler->scaler_code) }}" 
                                   required>
                            @error('scaler_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Scaler Name *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $masScaler->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mas_model_id">Model *</label>
                            <select class="form-control @error('mas_model_id') is-invalid @enderror" 
                                    id="mas_model_id" 
                                    name="mas_model_id" 
                                    required>
                                <option value="">Select Model</option>
                                @foreach($models as $model)
                                    <option value="{{ $model->id }}" {{ old('mas_model_id', $masScaler->mas_model_id) == $model->id ? 'selected' : '' }}>
                                        {{ $model->model_name }} ({{ $model->model_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mas_model_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mas_sensor_id">Sensor</label>
                            <select class="form-control @error('mas_sensor_id') is-invalid @enderror" 
                                    id="mas_sensor_id" 
                                    name="mas_sensor_id">
                                <option value="">Select Sensor (Optional)</option>
                                @foreach($sensors as $sensor)
                                    <option value="{{ $sensor->id }}" {{ old('mas_sensor_id', $masScaler->mas_sensor_id) == $sensor->id ? 'selected' : '' }}>
                                        {{ $sensor->sensor_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mas_sensor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="io_axis">IO Axis *</label>
                            <select class="form-control @error('io_axis') is-invalid @enderror" 
                                    id="io_axis" 
                                    name="io_axis" 
                                    required>
                                <option value="">Select Axis</option>
                                @foreach($axes as $key => $value)
                                    <option value="{{ $key }}" {{ old('io_axis', $masScaler->io_axis) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('io_axis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="technique">Technique *</label>
                            <select class="form-control @error('technique') is-invalid @enderror" 
                                    id="technique" 
                                    name="technique" 
                                    required>
                                <option value="">Select Technique</option>
                                @foreach($techniques as $key => $value)
                                    <option value="{{ $key }}" {{ old('technique', $masScaler->technique) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('technique')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="version">Version</label>
                            <input type="text" 
                                   class="form-control @error('version') is-invalid @enderror" 
                                   id="version" 
                                   name="version" 
                                   value="{{ old('version', $masScaler->version) }}" 
                                   placeholder="e.g., 1.0.0">
                            @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="scaler_file">Scaler File</label>
                            <input type="file" 
                                   class="form-control-file @error('scaler_file') is-invalid @enderror" 
                                   id="scaler_file" 
                                   name="scaler_file" 
                                   accept=".pkl,.joblib,.json">
                            <small class="form-text text-muted">
                                Leave empty to keep current file. Supported formats: .pkl, .joblib, .json (Max size: 10MB)
                            </small>
                            @if($masScaler->file_path)
                                <div class="mt-2">
                                    <span class="badge badge-success">
                                        <i class="fas fa-file"></i> Current file: {{ basename($masScaler->file_path) }}
                                    </span>
                                </div>
                            @endif
                            @error('scaler_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $masScaler->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Is Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Scaler
                    </button>
                    <a href="{{ route('admin.mas-scalers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection