@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">WhatsApp Numbers</h1>
        <div>
            <a href="{{ route('admin.mas-whatsapp-numbers.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.mas-whatsapp-numbers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Number
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">WhatsApp Numbers List</h6>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.mas-whatsapp-numbers.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search by name or number..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.mas-whatsapp-numbers.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <span class="text-muted">Total: {{ $whatsappNumbers->total() }} numbers</span>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Name</th>
                            <th width="20%">Number</th>
                            <th width="20%">Formatted</th>
                            <th width="15%">Added</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($whatsappNumbers as $number)
                            <tr>
                                <td>{{ $loop->iteration + ($whatsappNumbers->currentPage() - 1) * $whatsappNumbers->perPage() }}</td>
                                <td>{{ $number->name }}</td>
                                <td>
                                    <span class="font-monospace">{{ $number->number }}</span>
                                </td>
                                <td>
                                    <span class="font-monospace text-success">{{ $number->formatted_number }}</span>
                                </td>
                                <td>{{ $number->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.mas-whatsapp-numbers.show', $number) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.mas-whatsapp-numbers.edit', $number) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ $number->whatsapp_url }}" 
                                           target="_blank"
                                           class="btn btn-sm btn-success" title="Open WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.mas-whatsapp-numbers.destroy', $number) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this WhatsApp number?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No WhatsApp numbers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($whatsappNumbers->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $whatsappNumbers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection