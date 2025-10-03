@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Predicted Calculated Discharges</h1>
        <div>
            <a href="{{ route('admin.predicted-calculated-discharges.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.predicted-calculated-discharges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New
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

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.predicted-calculated-discharges.index') }}" id="searchForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by sensor code, name, or rating curve">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sensor_code">Sensor</label>
                            <select class="form-control" id="sensor_code" name="sensor_code">
                                <option value="">All Sensors</option>
                                @foreach($sensors as $sensor)
                                    <option value="{{ $sensor->sensor_code }}" 
                                        {{ request('sensor_code') == $sensor->sensor_code ? 'selected' : '' }}>
                                        {{ $sensor->sensor_name }} ({{ $sensor->sensor_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="rating_curve_id">Rating Curve</label>
                            <select class="form-control" id="rating_curve_id" name="rating_curve_id">
                                <option value="">All Rating Curves</option>
                                @foreach($ratingCurves as $curve)
                                    <option value="{{ $curve->id }}" 
                                        {{ request('rating_curve_id') == $curve->id ? 'selected' : '' }}>
                                        {{ $curve->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'sensor_code', 'rating_curve_id', 'date_from', 'date_to']))
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('admin.predicted-calculated-discharges.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Total: {{ $predictedDischarges->total() }} predicted discharges</span>
        <div class="d-flex align-items-center">
            <label for="per_page" class="mr-2 mb-0">Show:</label>
            <select id="per_page" class="form-control form-control-sm" style="width: auto;">
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Predicted Discharges List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sensor Code</th>
                            <th>Sensor Name</th>
                            <th>Predicted Value</th>
                            <th>Predicted Discharge</th>
                            <th>Rating Curve</th>
                            <th>Calculated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictedDischarges as $discharge)
                            <tr>
                                <td>{{ $loop->iteration + ($predictedDischarges->currentPage() - 1) * $predictedDischarges->perPage() }}</td>
                                <td>
                                    <code>{{ $discharge->mas_sensor_code }}</code>
                                </td>
                                <td>{{ $discharge->sensor->sensor_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ number_format($discharge->predicted_value, 3) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ number_format($discharge->predicted_discharge, 3) }}</span>
                                </td>
                                <td>{{ $discharge->ratingCurve->name ?? 'N/A' }}</td>
                                <td>{{ $discharge->calculated_at?->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.predicted-calculated-discharges.show', $discharge) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.predicted-calculated-discharges.edit', $discharge) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.predicted-calculated-discharges.destroy', $discharge) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this predicted discharge?');">
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
                                <td colspan="8" class="text-center">No predicted discharges found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($predictedDischarges->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $predictedDischarges->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('per_page').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('per_page', this.value);
    url.searchParams.set('page', '1');
    window.location.href = url;
});
</script>
@endsection