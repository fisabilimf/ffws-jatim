@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Scalers</h1>
        <a href="{{ route('admin.mas-scalers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Scaler
        </a>
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
            <h6 class="m-0 font-weight-bold text-primary">Scalers List</h6>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <form method="GET" action="{{ route('admin.mas-scalers.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search scalers..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                </div>
                <div class="col-md-2">
                    <select name="technique" class="form-control" onchange="document.getElementById('searchForm').submit();">
                        <option value="">All Techniques</option>
                        @foreach($techniques as $key => $value)
                            <option value="{{ $key }}" {{ request('technique') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="axis" class="form-control" onchange="document.getElementById('searchForm').submit();">
                        <option value="">All Axes</option>
                        @foreach($axes as $key => $value)
                            <option value="{{ $key }}" {{ request('axis') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control" onchange="document.getElementById('searchForm').submit();">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    @if(request()->hasAny(['search', 'technique', 'axis', 'status']))
                        <a href="{{ route('admin.mas-scalers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    @endif
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Code</th>
                            <th width="15%">Name</th>
                            <th width="15%">Model</th>
                            <th width="10%">Sensor</th>
                            <th width="8%">Axis</th>
                            <th width="10%">Technique</th>
                            <th width="8%">Version</th>
                            <th width="8%">Status</th>
                            <th width="11%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scalers as $scaler)
                            <tr>
                                <td>{{ $loop->iteration + ($scalers->currentPage() - 1) * $scalers->perPage() }}</td>
                                <td>{{ $scaler->scaler_code }}</td>
                                <td>{{ $scaler->name }}</td>
                                <td>{{ $scaler->masModel->model_name ?? '-' }}</td>
                                <td>{{ $scaler->masSensor->sensor_name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $scaler->io_axis === 'x' ? 'info' : 'warning' }}">
                                        {{ strtoupper($scaler->io_axis) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $techniques[$scaler->technique] }}</span>
                                </td>
                                <td>{{ $scaler->version ?? '-' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.mas-scalers.toggle-status', $scaler) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $scaler->is_active ? 'success' : 'secondary' }}" title="Toggle Status">
                                            <i class="fas fa-{{ $scaler->is_active ? 'check' : 'times' }}"></i>
                                            {{ $scaler->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.mas-scalers.show', $scaler) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.mas-scalers.edit', $scaler) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.mas-scalers.download', $scaler) }}" 
                                           class="btn btn-sm btn-success" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.mas-scalers.destroy', $scaler) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this scaler?');">
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
                                <td colspan="10" class="text-center">No scalers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($scalers->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $scalers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection