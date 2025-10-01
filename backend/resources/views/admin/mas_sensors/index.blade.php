@extends('layouts.admin')

@section('title', 'Sensors')
@section('page-title', 'Sensors')
@section('page-description', 'Kelola data sensor monitoring')
@section('breadcrumb', 'Sensors')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Sensor',
                'placeholder' => 'Cari berdasarkan kode atau deskripsi...'
            ],
            [
                'type' => 'select',
                'name' => 'parameter',
                'label' => 'Parameter',
                'empty_option' => 'Semua Parameter',
                'options' => collect($parameterOptions)->map(function($label, $value) {
                    return ['value' => $value, 'label' => $label];
                })->values()->toArray()
            ],
            [
                'type' => 'select',
                'name' => 'status',
                'label' => 'Status',
                'empty_option' => 'Semua Status',
                'options' => collect($statusOptions)->map(function($label, $value) {
                    return ['value' => $value, 'label' => $label];
                })->values()->toArray()
            ],
            [
                'type' => 'select',
                'name' => 'per_page',
                'label' => 'Per Halaman',
                'options' => [
                    ['value' => '10', 'label' => '10'],
                    ['value' => '15', 'label' => '15'],
                    ['value' => '25', 'label' => '25'],
                    ['value' => '50', 'label' => '50'],
                    ['value' => '100', 'label' => '100']
                ]
            ]
        ];
    @endphp

    <x-filter-bar 
        title="Filter & Pencarian Sensor"
        :filters="$filterConfig"
        :action="route('admin.sensors.index')"
        gridCols="md:grid-cols-4"
    />

    <!-- Table Section -->
    <x-table
        title="Daftar Sensor"
        :headers="$tableHeaders"
        :rows="$sensors"
        searchable
        searchPlaceholder="Cari sensor..."
    >
        <x-slot:actions>
            <x-admin.button href="{{ route('admin.sensors.create') }}" variant="primary">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Sensor
            </x-admin.button>
        </x-slot:actions>
    </x-table>
</div>
@endsection
