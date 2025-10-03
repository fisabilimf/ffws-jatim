@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Scaler Details</h1>
        <div>
            <a href="{{ route('admin.mas-scalers.edit', $masScaler) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.mas-scalers.download', $masScaler) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download
            </a>
            <a href="{{ route('admin.mas-scalers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Scaler Code:</label>
                                <p class="text-gray-800">{{ $masScaler->scaler_code }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Scaler Name:</label>
                                <p class="text-gray-800">{{ $masScaler->name }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Model:</label>
                                <p class="text-gray-800">
                                    {{ $masScaler->masModel->model_name ?? 'N/A' }}
                                    @if($masScaler->masModel)
                                        <small class="text-muted">({{ $masScaler->masModel->model_code }})</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Sensor:</label>
                                <p class="text-gray-800">{{ $masScaler->masSensor->sensor_name ?? 'Not Specified' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">IO Axis:</label>
                                <p>
                                    <span class="badge badge-{{ $masScaler->io_axis === 'x' ? 'info' : 'warning' }} badge-lg">
                                        {{ $masScaler->io_axis === 'x' ? 'X Axis (Input)' : 'Y Axis (Output)' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Technique:</label>
                                <p>
                                    <span class="badge badge-secondary badge-lg">
                                        {{ $masScaler::getTechniqueOptions()[$masScaler->technique] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Version:</label>
                                <p class="text-gray-800">{{ $masScaler->version ?? 'Not Specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">File Path:</label>
                                <p class="text-gray-800">{{ $masScaler->file_path }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">File Name:</label>
                                <p class="text-gray-800">{{ basename($masScaler->file_path) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">File Hash (SHA256):</label>
                                <p class="text-gray-800 font-monospace">{{ $masScaler->file_hash_sha256 ?? 'Not Available' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status & Actions</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h3 mb-0">
                            <span class="badge badge-{{ $masScaler->is_active ? 'success' : 'secondary' }} badge-lg">
                                <i class="fas fa-{{ $masScaler->is_active ? 'check' : 'times' }}"></i>
                                {{ $masScaler->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="text-xs font-weight-bold text-gray-500 text-uppercase mb-1">
                            Current Status
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('admin.mas-scalers.toggle-status', $masScaler) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-{{ $masScaler->is_active ? 'secondary' : 'success' }} btn-block">
                                <i class="fas fa-{{ $masScaler->is_active ? 'pause' : 'play' }}"></i>
                                {{ $masScaler->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.mas-scalers.edit', $masScaler) }}" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Scaler
                        </a>
                        
                        <a href="{{ route('admin.mas-scalers.download', $masScaler) }}" 
                           class="btn btn-success btn-block">
                            <i class="fas fa-download"></i> Download File
                        </a>
                        
                        <form method="POST" 
                              action="{{ route('admin.mas-scalers.destroy', $masScaler) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this scaler?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Delete Scaler
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timestamps</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Created At:</label>
                        <p class="text-gray-800">{{ $masScaler->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Last Updated:</label>
                        <p class="text-gray-800">{{ $masScaler->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection