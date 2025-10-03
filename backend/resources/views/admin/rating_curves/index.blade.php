@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rating Curves</h1>
        <a href="{{ route('admin.rating-curves.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Rating Curve
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
            <h6 class="m-0 font-weight-bold text-primary">Rating Curves List</h6>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <form method="GET" action="{{ route('admin.rating-curves.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search curves..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                </div>
                <div class="col-md-3">
                    <select name="formula_type" class="form-control" onchange="document.getElementById('searchForm').submit();">
                        <option value="">All Formula Types</option>
                        @foreach($formulaTypes as $key => $value)
                            <option value="{{ $key }}" {{ request('formula_type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sensor_code" class="form-control" onchange="document.getElementById('searchForm').submit();">
                        <option value="">All Sensors</option>
                        @foreach($sensors as $sensor)
                            <option value="{{ $sensor->sensor_code }}" {{ request('sensor_code') == $sensor->sensor_code ? 'selected' : '' }}>
                                {{ $sensor->sensor_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    @if(request()->hasAny(['search', 'formula_type', 'sensor_code']))
                        <a href="{{ route('admin.rating-curves.index') }}" class="btn btn-secondary">
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
                            <th width="20%">Sensor</th>
                            <th width="15%">Formula Type</th>
                            <th width="25%">Formula</th>
                            <th width="12%">Effective Date</th>
                            <th width="8%">Calculations</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratingCurves as $curve)
                            <tr>
                                <td>{{ $loop->iteration + ($ratingCurves->currentPage() - 1) * $ratingCurves->perPage() }}</td>
                                <td>
                                    {{ $curve->sensor->sensor_name ?? $curve->mas_sensor_code }}
                                    <br><small class="text-muted">{{ $curve->mas_sensor_code }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($curve->formula_type) }}</span>
                                </td>
                                <td>
                                    <code class="small">
                                        @if($curve->formula_type === 'power')
                                            Q = {{ $curve->a }} × H^{{ $curve->b }}
                                        @elseif($curve->formula_type === 'polynomial')
                                            Q = {{ $curve->a }} + {{ $curve->b }}×H + {{ $curve->c }}×H²
                                        @elseif($curve->formula_type === 'exponential')
                                            Q = {{ $curve->a }} × e^({{ $curve->b }}×H)
                                        @else
                                            Q = {{ $curve->a }} × H
                                        @endif
                                    </code>
                                </td>
                                <td>{{ $curve->effective_date->format('d M Y') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">{{ $curve->calculatedDischarges()->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.rating-curves.show', $curve) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.rating-curves.edit', $curve) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.rating-curves.destroy', $curve) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this rating curve?');">
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
                                <td colspan="7" class="text-center">No rating curves found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($ratingCurves->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $ratingCurves->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection