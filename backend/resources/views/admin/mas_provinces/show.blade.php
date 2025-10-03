@extends('layouts.admin')

@section('title', 'Detail Provinsi')
@section('page-title', 'Detail Provinsi')
@section('page-description', 'Informasi detail provinsi')
@section('breadcrumb', 'Provinces')

@section('content')
<div class="space-y-6">
    <!-- Province Information -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">Informasi Provinsi</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">ID</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masProvince->id }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Kode Provinsi</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masProvince->provinces_code }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Nama Provinsi</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masProvince->provinces_name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Dibuat Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masProvince->created_at ? $masProvince->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Diperbarui Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masProvince->updated_at ? $masProvince->updated_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.mas-provinces.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
            <a href="{{ route('admin.mas-provinces.edit', $masProvince) }}" 
               class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit
            </a>
        </div>
    </div>

    <!-- Related Data -->
    @if($masProvince->riverBasins && $masProvince->riverBasins->count() > 0)
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">DAS Terkait ({{ $masProvince->riverBasins->count() }})</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama DAS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($masProvince->riverBasins as $riverBasin)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $riverBasin->river_basins_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $riverBasin->river_basins_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $riverBasin->city->cities_name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($masProvince->upts && $masProvince->upts->count() > 0)
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">UPT Terkait ({{ $masProvince->upts->count() }})</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama UPT</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($masProvince->upts as $upt)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $upt->upt_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $upt->upt_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection