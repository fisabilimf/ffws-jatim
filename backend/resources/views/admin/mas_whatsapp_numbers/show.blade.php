@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">WhatsApp Number Details</h1>
        <div>
            <a href="{{ route('admin.mas-whatsapp-numbers.edit', $masWhatsappNumber) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ $masWhatsappNumber->whatsapp_url }}" target="_blank" class="btn btn-success">
                <i class="fab fa-whatsapp"></i> Open WhatsApp
            </a>
            <a href="{{ route('admin.mas-whatsapp-numbers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Contact Name:</label>
                                <p class="text-gray-800 h5">{{ $masWhatsappNumber->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Original Number:</label>
                                <p class="text-gray-800 font-monospace">{{ $masWhatsappNumber->number }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">Formatted Number:</label>
                                <p class="text-success font-monospace h5">{{ $masWhatsappNumber->formatted_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-600">WhatsApp Link:</label>
                                <p>
                                    <a href="{{ $masWhatsappNumber->whatsapp_url }}" target="_blank" class="btn btn-success btn-sm">
                                        <i class="fab fa-whatsapp"></i> Open in WhatsApp
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Send Test Message</h6>
                            <p class="text-muted">Send a test message to verify the number is working.</p>
                            <a href="{{ $masWhatsappNumber->whatsapp_url }}?text={{ urlencode('Hello! This is a test message from FFWS System.') }}" 
                               target="_blank" 
                               class="btn btn-success">
                                <i class="fab fa-whatsapp"></i> Send Test Message
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h6>Emergency Alert</h6>
                            <p class="text-muted">Send emergency flood warning message.</p>
                            <a href="{{ $masWhatsappNumber->whatsapp_url }}?text={{ urlencode('🚨 FLOOD WARNING 🚨\n\nThis is an emergency notification from FFWS (Flood Forecasting and Warning System).\n\nPlease stay alert and follow safety procedures.\n\nFor more information, contact the emergency response team.') }}" 
                               target="_blank" 
                               class="btn btn-danger">
                                <i class="fas fa-exclamation-triangle"></i> Send Emergency Alert
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.mas-whatsapp-numbers.edit', $masWhatsappNumber) }}" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Contact
                        </a>
                        
                        <a href="{{ $masWhatsappNumber->whatsapp_url }}" 
                           target="_blank"
                           class="btn btn-success btn-block">
                            <i class="fab fa-whatsapp"></i> Open WhatsApp
                        </a>
                        
                        <form method="POST" 
                              action="{{ route('admin.mas-whatsapp-numbers.destroy', $masWhatsappNumber) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this WhatsApp number?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Delete Contact
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Details</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Created:</label>
                        <p class="text-gray-800">{{ $masWhatsappNumber->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">Last Updated:</label>
                        <p class="text-gray-800">{{ $masWhatsappNumber->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-600">WhatsApp URL:</label>
                        <p class="text-gray-800 small font-monospace">{{ $masWhatsappNumber->whatsapp_url }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fab fa-whatsapp fa-3x text-success"></i>
                        <h6 class="mt-2">WhatsApp Integration</h6>
                        <p class="text-muted small">
                            This contact can receive automated flood warnings and system notifications via WhatsApp.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection