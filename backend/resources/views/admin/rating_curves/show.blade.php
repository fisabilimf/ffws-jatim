@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rating Curve Details</h1>
        <div>
            <a href="{{ route('admin.rating-curves.edit', $ratingCurve) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.rating-curves.index') }}" class="btn btn-secondary">
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
                                <label class="font-weight-bold text-gray-600">Sensor:</label>
                                <p class="text-gray-800">
                                    {{ $ratingCurve->sensor->sensor_name ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $ratingCurve->mas_sensor_code }}</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Effective Date:</label>
                                <p class="text-gray-800">{{ $ratingCurve->effective_date->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Formula Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Formula Type:</label>
                                <p>
                                    <span class="badge badge-info badge-lg">
                                        {{ ucfirst($ratingCurve->formula_type) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border">
                        <h5 class="font-weight-bold">Current Formula:</h5>
                        <div class="h4 text-primary font-monospace">
                            @if($ratingCurve->formula_type === 'power')
                                Q = {{ $ratingCurve->a }} × H<sup>{{ $ratingCurve->b }}</sup>
                            @elseif($ratingCurve->formula_type === 'polynomial')
                                Q = {{ $ratingCurve->a }} + {{ $ratingCurve->b }}×H + {{ $ratingCurve->c }}×H²
                            @elseif($ratingCurve->formula_type === 'exponential')
                                Q = {{ $ratingCurve->a }} × e<sup>({{ $ratingCurve->b }}×H)</sup>
                            @else
                                Q = {{ $ratingCurve->a }} × H
                            @endif
                        </div>
                        <small class="text-muted">Where Q = Discharge (m³/s), H = Sensor Value</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Parameter A:</label>
                                <p class="text-gray-800 h5">{{ $ratingCurve->a }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Parameter B:</label>
                                <p class="text-gray-800 h5">{{ $ratingCurve->b ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Parameter C:</label>
                                <p class="text-gray-800 h5">{{ $ratingCurve->c ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calculator Tool -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Discharge Calculator</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="calc_sensor_value">Sensor Value (H):</label>
                                <input type="number" step="any" class="form-control" id="calc_sensor_value" placeholder="Enter sensor reading">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-success btn-block" onclick="calculateDischarge()">
                                    <i class="fas fa-calculator"></i> Calculate Discharge
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="calc-result" class="alert alert-info" style="display: none;">
                        <h5><strong>Calculated Discharge:</strong> <span id="discharge-value" class="text-primary"></span></h5>
                    </div>
                </div>
            </div>

            <!-- Sample Calculations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sample Calculations</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="number" step="any" class="form-control" id="sample_min" placeholder="Min value" value="0">
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="any" class="form-control" id="sample_max" placeholder="Max value" value="10">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" id="sample_steps" placeholder="Steps" value="10" min="5" max="20">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary btn-block" onclick="generateSamples()">
                                <i class="fas fa-chart-line"></i> Generate
                            </button>
                        </div>
                    </div>
                    <div id="sample-table" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Sensor Value (H)</th>
                                        <th>Discharge (Q)</th>
                                    </tr>
                                </thead>
                                <tbody id="sample-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Usage Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-0 font-weight-bold text-primary">{{ $ratingCurve->calculatedDischarges()->count() }}</div>
                        <div class="text-xs font-weight-bold text-gray-500 text-uppercase mb-3">
                            Calculated Discharges
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="h2 mb-0 font-weight-bold text-info">{{ $ratingCurve->predictedCalculatedDischarges()->count() }}</div>
                        <div class="text-xs font-weight-bold text-gray-500 text-uppercase mb-3">
                            Predicted Discharges
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.rating-curves.edit', $ratingCurve) }}" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Rating Curve
                        </a>
                        
                        @if($ratingCurve->calculatedDischarges()->count() == 0 && $ratingCurve->predictedCalculatedDischarges()->count() == 0)
                            <form method="POST" 
                                  action="{{ route('admin.rating-curves.destroy', $ratingCurve) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this rating curve?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Delete Rating Curve
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-danger btn-block" disabled title="Cannot delete - has related discharge calculations">
                                <i class="fas fa-trash"></i> Delete Rating Curve
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timestamps</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Created:</label>
                        <p class="text-gray-800">{{ $ratingCurve->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Last Updated:</label>
                        <p class="text-gray-800">{{ $ratingCurve->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function calculateDischarge() {
    const sensorValue = parseFloat(document.getElementById('calc_sensor_value').value);
    
    if (!sensorValue) {
        alert('Please enter a sensor value.');
        return;
    }
    
    const formulaType = '{{ $ratingCurve->formula_type }}';
    const a = {{ $ratingCurve->a }};
    const b = {{ $ratingCurve->b ?? 0 }};
    const c = {{ $ratingCurve->c ?? 0 }};
    
    let discharge = 0;
    
    switch(formulaType) {
        case 'power':
            discharge = a * Math.pow(sensorValue, b || 1);
            break;
        case 'polynomial':
            discharge = a + (b * sensorValue) + (c * Math.pow(sensorValue, 2));
            break;
        case 'exponential':
            discharge = a * Math.exp((b || 1) * sensorValue);
            break;
        case 'linear':
            discharge = a * sensorValue;
            break;
    }
    
    document.getElementById('discharge-value').textContent = discharge.toFixed(3) + ' m³/s';
    document.getElementById('calc-result').style.display = 'block';
}

function generateSamples() {
    const minValue = parseFloat(document.getElementById('sample_min').value) || 0;
    const maxValue = parseFloat(document.getElementById('sample_max').value) || 10;
    const steps = parseInt(document.getElementById('sample_steps').value) || 10;
    
    if (minValue >= maxValue) {
        alert('Max value must be greater than min value.');
        return;
    }
    
    const formulaType = '{{ $ratingCurve->formula_type }}';
    const a = {{ $ratingCurve->a }};
    const b = {{ $ratingCurve->b ?? 0 }};
    const c = {{ $ratingCurve->c ?? 0 }};
    
    const stepSize = (maxValue - minValue) / (steps - 1);
    const tbody = document.getElementById('sample-tbody');
    tbody.innerHTML = '';
    
    for (let i = 0; i < steps; i++) {
        const sensorValue = minValue + (i * stepSize);
        let discharge = 0;
        
        switch(formulaType) {
            case 'power':
                discharge = a * Math.pow(sensorValue, b || 1);
                break;
            case 'polynomial':
                discharge = a + (b * sensorValue) + (c * Math.pow(sensorValue, 2));
                break;
            case 'exponential':
                discharge = a * Math.exp((b || 1) * sensorValue);
                break;
            case 'linear':
                discharge = a * sensorValue;
                break;
        }
        
        const row = tbody.insertRow();
        row.insertCell(0).textContent = sensorValue.toFixed(3);
        row.insertCell(1).textContent = discharge.toFixed(3) + ' m³/s';
    }
    
    document.getElementById('sample-table').style.display = 'block';
}
</script>
@endpush
@endsection