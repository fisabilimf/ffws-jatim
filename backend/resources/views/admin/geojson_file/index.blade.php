@extends('layouts.admin')

@section('title', 'Manajemen GeoJSON Files')
@section('page-title', 'Manajemen GeoJSON Files')
@section('page-description', 'Kelola file GeoJSON untuk sistem peringatan banjir')
@section('breadcrumb', 'GeoJSON Files')

@section('content')
<div class="space-y-6">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari File',
                'placeholder' => 'Cari berdasarkan nama file atau label...'
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
        title="Filter & Pencarian GeoJSON Files"
        :filters="$filterConfig"
        :action="route('admin.geojson-files.index')"
        gridCols="md:grid-cols-2"
    />

    <!-- GeoJSON Files Table -->
    <x-table
        title="Daftar GeoJSON Files"
        :headers="$tableHeaders"
        :rows="$files"
        searchable
        :pagination="$files->links()"
    >
        <x-slot:actions>
            <x-admin.button href="{{ route('admin.geojson-files.create') }}" variant="primary">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Upload GeoJSON
            </x-admin.button>
        </x-slot:actions>
    </x-table>
</div>
@endsection
