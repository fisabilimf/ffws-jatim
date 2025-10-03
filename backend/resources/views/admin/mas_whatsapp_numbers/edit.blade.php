@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit WhatsApp Number</h1>
        <a href="{{ route('admin.mas-whatsapp-numbers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">WhatsApp Number Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.mas-whatsapp-numbers.update', $masWhatsappNumber) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Contact Name *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $masWhatsappNumber->name) }}" 
                                   placeholder="e.g., Admin FFWS"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number">WhatsApp Number *</label>
                            <input type="text" 
                                   class="form-control @error('number') is-invalid @enderror" 
                                   id="number" 
                                   name="number" 
                                   value="{{ old('number', $masWhatsappNumber->number) }}" 
                                   placeholder="e.g., 08123456789 or +628123456789"
                                   required>
                            <small class="form-text text-muted">
                                Enter Indonesian phone number (08xx-xxxx-xxxx or +628xx-xxxx-xxxx)
                            </small>
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Current Format:</strong> {{ $masWhatsappNumber->formatted_number }}
                    <br>
                    <strong>WhatsApp Link:</strong> <a href="{{ $masWhatsappNumber->whatsapp_url }}" target="_blank">{{ $masWhatsappNumber->whatsapp_url }}</a>
                </div>

                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle"></i>
                    <strong>Format Guidelines:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Indonesian mobile numbers only (08xx format)</li>
                        <li>You can use formats like: 08123456789, +628123456789, or 628123456789</li>
                        <li>Number will be automatically formatted for WhatsApp compatibility</li>
                        <li>Make sure the number is active on WhatsApp</li>
                    </ul>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update WhatsApp Number
                    </button>
                    <a href="{{ route('admin.mas-whatsapp-numbers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('number').addEventListener('input', function(e) {
    let value = e.target.value;
    
    // Remove non-numeric characters for preview
    let numbers = value.replace(/[^0-9]/g, '');
    
    // Show format preview
    if (numbers.length > 0) {
        let formatted = '';
        if (numbers.startsWith('62')) {
            formatted = '+' + numbers;
        } else if (numbers.startsWith('0')) {
            formatted = '+62' + numbers.substring(1);
        } else {
            formatted = '+62' + numbers;
        }
        
        // Update placeholder or show preview
        console.log('Formatted preview:', formatted);
    }
});
</script>
@endpush
@endsection