@extends('layouts.admin')

@section('title', 'Data Actuals')
@section('page-title', 'Dashboard Data Actuals')
@section('page-description', 'Monitoring data aktual dari sensor-sensor FFWS')

@section('content')


<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 overflow-hidden shadow-lg sm:rounded-lg">
        <div class="p-6 flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-white text-4xl mr-6"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">Data Actuals</h2>
                    <p class="text-white opacity-90">Monitoring data aktual dari sensor-sensor FFWS</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <x-admin.button variant="outline" size="md" 
                    href="{{ route('admin.data-actuals.export', request()->query()) }}"
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white border-white">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </x-admin.button>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-database text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Data</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_data']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Status Aman</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['safe_count']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Status Waspada</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['warning_count']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Status Bahaya</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['danger_count']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Data Hari Ini</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['today_data']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <x-admin.card title="Filter Data">
        <form method="GET" action="{{ route('admin.data-actuals.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-admin.form-input 
                type="select"
                name="sensor_id" 
                label="Sensor" 
                placeholder="Semua Sensor"
                :value="request('sensor_id')"
                :options="$sensors->map(function($sensor) { return ['value' => $sensor->id, 'label' => $sensor->sensor_code . ' - ' . $sensor->parameter]; })->toArray()"
            />

            <x-admin.form-input 
                type="select"
                name="threshold_status" 
                label="Status Threshold" 
                placeholder="Semua Status"
                :value="request('threshold_status')"
                :options="[
                    ['value' => 'safe', 'label' => 'Aman'],
                    ['value' => 'warning', 'label' => 'Waspada'],
                    ['value' => 'danger', 'label' => 'Bahaya']
                ]"
            />

            <x-admin.form-input 
                type="select"
                name="river_basin_id" 
                label="Daerah Aliran Sungai" 
                placeholder="Semua DAS"
                :value="request('river_basin_id')"
                :options="$riverBasins->map(function($basin) { return ['value' => $basin->id, 'label' => $basin->name]; })->toArray()"
            />

            <x-admin.form-input 
                type="date" 
                name="date_from" 
                label="Dari Tanggal" 
                :value="request('date_from')"
            />

            <x-admin.form-input 
                type="date" 
                name="date_to" 
                label="Sampai Tanggal" 
                :value="request('date_to')"
            />

            <div class="lg:col-span-5 flex justify-end space-x-3">
                <x-admin.button variant="outline" size="md" href="{{ route('admin.data-actuals.index') }}">
                    Reset Filter
                </x-admin.button>
                <x-admin.button variant="primary" size="md" type="submit">
                    Terapkan Filter
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <!-- Data Table -->
    <x-table 
        title="Data Actuals"
        :headers="$tableHeaders"
        :rows="$dataActuals"
        :pagination="$dataActuals->appends(request()->query())->links()"
        :pagination-text="'Menampilkan ' . ($dataActuals->firstItem() ?? 0) . ' - ' . ($dataActuals->lastItem() ?? 0) . ' dari ' . $dataActuals->total() . ' data'"
    />
</div>
@endsection
