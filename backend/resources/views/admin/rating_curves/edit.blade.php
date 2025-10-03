@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Rating Curve</h1>
        <a href="{{ route('admin.rating-curves.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rating Curve Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.rating-curves.update', $ratingCurve) }}" id="ratingCurveForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mas_sensor_code">Sensor *</label>
                            <select class="form-control @error('mas_sensor_code') is-invalid @enderror" 
                                    id="mas_sensor_code" 
                                    name="mas_sensor_code" 
                                    required>
                                <option value="">Select Sensor</option>
                                @foreach($sensors as $sensor)
                                    <option value="{{ $sensor->sensor_code }}" {{ old('mas_sensor_code', $ratingCurve->mas_sensor_code) == $sensor->sensor_code ? 'selected' : '' }}>
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
                            <label for="effective_date">Effective Date *</label>
                            <input type="date" 
                                   class="form-control @error('effective_date') is-invalid @enderror" 
                                   id="effective_date" 
                                   name="effective_date" 
                                   value="{{ old('effective_date', $ratingCurve->effective_date->format('Y-m-d')) }}" 
                                   required>
                            @error('effective_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="formula_type">Formula Type *</label>
                            <select class="form-control @error('formula_type') is-invalid @enderror" 
                                    id="formula_type" 
                                    name="formula_type" 
                                    required>
                                <option value="">Select Formula Type</option>
                                @foreach($formulaTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('formula_type', $ratingCurve->formula_type) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formula_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Formula Parameters -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Formula Parameters</h6>
                    </div>
                    <div class="card-body">
                        <div id="formula-preview" class="alert alert-light mb-3">
                            <strong>Formula Preview:</strong> <span id="formula-display">Select a formula type</span>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="a">Parameter A *</label>
                                    <input type="number" 
                                           step="any"
                                           class="form-control @error('a') is-invalid @enderror" 
                                           id="a" 
                                           name="a" 
                                           value="{{ old('a', $ratingCurve->a) }}" 
                                           required>
                                    <small class="form-text text-muted" id="param-a-help">Base coefficient</small>
                                    @error('a')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="b">Parameter B</label>
                                    <input type="number" 
                                           step="any"
                                           class="form-control @error('b') is-invalid @enderror" 
                                           id="b" 
                                           name="b" 
                                           value="{{ old('b', $ratingCurve->b) }}">
                                    <small class="form-text text-muted" id="param-b-help">Second coefficient</small>
                                    @error('b')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="c">Parameter C</label>
                                    <input type="number" 
                                           step="any"
                                           class="form-control @error('c') is-invalid @enderror" 
                                           id="c" 
                                           name="c" 
                                           value="{{ old('c', $ratingCurve->c) }}">
                                    <small class="form-text text-muted" id="param-c-help">Third coefficient</small>
                                    @error('c')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Calculator -->
                <div class="card border-success mt-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Test Calculator</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="test_value">Test Sensor Value</label>
                                    <input type="number" step="any" class="form-control" id="test_value" placeholder="Enter sensor value">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-success btn-block" onclick="calculateTest()">
                                        <i class="fas fa-calculator"></i> Calculate Discharge
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="test-result" class="alert alert-info" style="display: none;">
                            <strong>Result:</strong> <span id="result-value"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Rating Curve
                    </button>
                    <a href="{{ route('admin.rating-curves.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('formula_type').addEventListener('change', function() {
    updateFormulaDisplay();
    updateParameterHelp();
});

// Update formula parameters when they change
['a', 'b', 'c'].forEach(param => {
    document.getElementById(param).addEventListener('input', updateFormulaDisplay);
});

function updateFormulaDisplay() {
    const formulaType = document.getElementById('formula_type').value;
    const a = document.getElementById('a').value || 'a';
    const b = document.getElementById('b').value || 'b';
    const c = document.getElementById('c').value || 'c';
    
    let formula = '';
    
    switch(formulaType) {
        case 'power':
            formula = `Q = ${a} × H^${b}`;
            break;
        case 'polynomial':
            formula = `Q = ${a} + ${b}×H + ${c}×H²`;
            break;
        case 'exponential':
            formula = `Q = ${a} × e^(${b}×H)`;
            break;
        case 'linear':
            formula = `Q = ${a} × H`;
            break;
        default:
            formula = 'Select a formula type';
    }
    
    document.getElementById('formula-display').textContent = formula;
}

function updateParameterHelp() {
    const formulaType = document.getElementById('formula_type').value;
    
    const helpTexts = {
        'power': {
            'a': 'Coefficient (multiplier)',
            'b': 'Exponent power',
            'c': 'Not used in power formula'
        },
        'polynomial': {
            'a': 'Constant term',
            'b': 'Linear coefficient',
            'c': 'Quadratic coefficient'
        },
        'exponential': {
            'a': 'Base multiplier',
            'b': 'Exponential coefficient',
            'c': 'Not used in exponential formula'
        },
        'linear': {
            'a': 'Slope coefficient',
            'b': 'Not used in linear formula',
            'c': 'Not used in linear formula'
        }
    };
    
    if (helpTexts[formulaType]) {
        document.getElementById('param-a-help').textContent = helpTexts[formulaType].a;
        document.getElementById('param-b-help').textContent = helpTexts[formulaType].b;
        document.getElementById('param-c-help').textContent = helpTexts[formulaType].c;
        
        // Enable/disable fields based on formula type
        document.getElementById('b').disabled = formulaType === 'linear';
        document.getElementById('c').disabled = formulaType !== 'polynomial';
    }
}

function calculateTest() {
    const formulaType = document.getElementById('formula_type').value;
    const a = parseFloat(document.getElementById('a').value) || 0;
    const b = parseFloat(document.getElementById('b').value) || 0;
    const c = parseFloat(document.getElementById('c').value) || 0;
    const testValue = parseFloat(document.getElementById('test_value').value);
    
    if (!testValue || !formulaType || !a) {
        alert('Please fill in the formula type, parameter A, and test value.');
        return;
    }
    
    let result = 0;
    
    switch(formulaType) {
        case 'power':
            result = a * Math.pow(testValue, b || 1);
            break;
        case 'polynomial':
            result = a + (b * testValue) + (c * Math.pow(testValue, 2));
            break;
        case 'exponential':
            result = a * Math.exp((b || 1) * testValue);
            break;
        case 'linear':
            result = a * testValue;
            break;
    }
    
    document.getElementById('result-value').textContent = result.toFixed(3) + ' m³/s';
    document.getElementById('test-result').style.display = 'block';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateParameterHelp();
    updateFormulaDisplay();
});
</script>
@endpush
@endsection