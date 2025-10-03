@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sensor Threshold Details</h1>
        <div>
            <a href="{{ route('admin.mas-sensor-thresholds.edit', $masSensorThreshold) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.mas-sensor-thresholds.index') }}" class="btn btn-secondary">
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
                                <label class="font-weight-bold text-gray-600">Threshold Code:</label>
                                <p class="text-gray-800">{{ $masSensorThreshold->sensor_thresholds_code }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Threshold Name:</label>
                                <p class="text-gray-800">{{ $masSensorThreshold->sensor_thresholds_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Threshold Values & Colors</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Value 1 -->
                        <div class="col-md-6">
                            <div class="card border-left-success shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Threshold Value 1
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($masSensorThreshold->sensor_thresholds_value_1)
                                                    <span class="badge badge-lg" style="background-color: {{ $masSensorThreshold->sensor_thresholds_value_1_color ?? '#28a745' }}; color: white; font-size: 1rem;">
                                                        {{ $masSensorThreshold->sensor_thresholds_value_1 }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @if($masSensorThreshold->sensor_thresholds_value_1_color)
                                                <div class="rounded-circle" style="width: 40px; height: 40px; background-color: {{ $masSensorThreshold->sensor_thresholds_value_1_color }};"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Value 2 -->
                        <div class="col-md-6">
                            <div class="card border-left-warning shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Threshold Value 2
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($masSensorThreshold->sensor_thresholds_value_2)
                                                    <span class="badge badge-lg" style="background-color: {{ $masSensorThreshold->sensor_thresholds_value_2_color ?? '#ffc107' }}; color: white; font-size: 1rem;">
                                                        {{ $masSensorThreshold->sensor_thresholds_value_2 }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @if($masSensorThreshold->sensor_thresholds_value_2_color)
                                                <div class="rounded-circle" style="width: 40px; height: 40px; background-color: {{ $masSensorThreshold->sensor_thresholds_value_2_color }};"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Value 3 -->
                        <div class="col-md-6">
                            <div class="card border-left-info shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Threshold Value 3
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($masSensorThreshold->sensor_thresholds_value_3)
                                                    <span class="badge badge-lg" style="background-color: {{ $masSensorThreshold->sensor_thresholds_value_3_color ?? '#fd7e14' }}; color: white; font-size: 1rem;">
                                                        {{ $masSensorThreshold->sensor_thresholds_value_3 }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @if($masSensorThreshold->sensor_thresholds_value_3_color)
                                                <div class="rounded-circle" style="width: 40px; height: 40px; background-color: {{ $masSensorThreshold->sensor_thresholds_value_3_color }};"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Value 4 -->
                        <div class="col-md-6">
                            <div class="card border-left-danger shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Threshold Value 4
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($masSensorThreshold->sensor_thresholds_value_4)
                                                    <span class="badge badge-lg" style="background-color: {{ $masSensorThreshold->sensor_thresholds_value_4_color ?? '#dc3545' }}; color: white; font-size: 1rem;">
                                                        {{ $masSensorThreshold->sensor_thresholds_value_4 }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @if($masSensorThreshold->sensor_thresholds_value_4_color)
                                                <div class="rounded-circle" style="width: 40px; height: 40px; background-color: {{ $masSensorThreshold->sensor_thresholds_value_4_color }};"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Related Sensor Values</h6>
                </div>
                <div class="card-body">
                    @php
                        $sensorValuesCount = $masSensorThreshold->sensorValues()->count();
                    @endphp
                    
                    <div class="text-center">
                        <div class="h2 mb-0 font-weight-bold text-primary">{{ $sensorValuesCount }}</div>
                        <div class="text-xs font-weight-bold text-gray-500 text-uppercase mb-1">
                            Sensor Values Using This Threshold
                        </div>
                    </div>
                    
                    @if($sensorValuesCount > 0)
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                This threshold is currently being used by {{ $sensorValuesCount }} sensor value(s).
                            </small>
                        </div>
                    @else
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                This threshold is not currently being used by any sensor values.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.mas-sensor-thresholds.edit', $masSensorThreshold) }}" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Threshold
                        </a>
                        
                        @if($sensorValuesCount == 0)
                            <form method="POST" 
                                  action="{{ route('admin.mas-sensor-thresholds.destroy', $masSensorThreshold) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this threshold?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Delete Threshold
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-danger btn-block" disabled title="Cannot delete - has related sensor values">
                                <i class="fas fa-trash"></i> Delete Threshold
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection