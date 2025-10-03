@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Sensor Thresholds</h1>
        <a href="{{ route('admin.mas-sensor-thresholds.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Threshold
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
            <h6 class="m-0 font-weight-bold text-primary">Sensor Thresholds List</h6>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.mas-sensor-thresholds.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search by name or code..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.mas-sensor-thresholds.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Code</th>
                            <th width="20%">Name</th>
                            <th width="12%">Value 1</th>
                            <th width="12%">Value 2</th>
                            <th width="12%">Value 3</th>
                            <th width="12%">Value 4</th>
                            <th width="12%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sensorThresholds as $threshold)
                            <tr>
                                <td>{{ $loop->iteration + ($sensorThresholds->currentPage() - 1) * $sensorThresholds->perPage() }}</td>
                                <td>{{ $threshold->sensor_thresholds_code }}</td>
                                <td>{{ $threshold->sensor_thresholds_name }}</td>
                                <td class="text-center">
                                    @if($threshold->sensor_thresholds_value_1)
                                        <span class="badge" style="background-color: {{ $threshold->sensor_thresholds_value_1_color ?? '#6c757d' }}; color: white;">
                                            {{ $threshold->sensor_thresholds_value_1 }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($threshold->sensor_thresholds_value_2)
                                        <span class="badge" style="background-color: {{ $threshold->sensor_thresholds_value_2_color ?? '#6c757d' }}; color: white;">
                                            {{ $threshold->sensor_thresholds_value_2 }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($threshold->sensor_thresholds_value_3)
                                        <span class="badge" style="background-color: {{ $threshold->sensor_thresholds_value_3_color ?? '#6c757d' }}; color: white;">
                                            {{ $threshold->sensor_thresholds_value_3 }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($threshold->sensor_thresholds_value_4)
                                        <span class="badge" style="background-color: {{ $threshold->sensor_thresholds_value_4_color ?? '#6c757d' }}; color: white;">
                                            {{ $threshold->sensor_thresholds_value_4 }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.mas-sensor-thresholds.show', $threshold) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.mas-sensor-thresholds.edit', $threshold) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.mas-sensor-thresholds.destroy', $threshold) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this threshold?');">
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
                                <td colspan="8" class="text-center">No sensor thresholds found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sensorThresholds->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $sensorThresholds->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection