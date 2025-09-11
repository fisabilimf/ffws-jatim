@extends('layouts.admin')

@section('title', 'Sensors')
@section('page-title', 'Sensors')
@section('page-description', 'Kelola data sensor monitoring')
@section('breadcrumb', 'Sensors')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <x-admin.card title="Filter & Pencarian">
        <form method="GET" action="{{ route('admin.sensors.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Sensor</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Cari berdasarkan kode atau deskripsi..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="parameter" class="block text-sm font-medium text-gray-700">Parameter</label>
                    <select name="parameter" id="parameter" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Parameter</option>
                        @foreach($parameterOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('parameter') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="per_page" class="block text-sm font-medium text-gray-700">Per Halaman</label>
                    <select name="per_page" id="per_page" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == '15' || !request('per_page') ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Table Section -->
    <x-table
        title="Daftar Sensor"
        :headers="$tableHeaders"
        :rows="$sensors"
        searchable
        searchPlaceholder="Cari sensor..."
    >
        <x-slot:actions>
            <a href="{{ route('admin.sensors.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Sensor
            </a>
        </x-slot:actions>
    </x-table>
</div>
@endsection
