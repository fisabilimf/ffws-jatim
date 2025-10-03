@extends('layouts.admin')

@section('title', 'Detail Kota')
@section('page-title', 'Detail Kota')
@section('page-description', 'Informasi detail kota')
@section('breadcrumb', 'Cities')

@section('content')
<div class="space-y-6">
    <!-- City Information -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">Informasi Kota</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">ID</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masCity->id }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Kode Kota</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masCity->cities_code }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Nama Kota</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masCity->cities_name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Dibuat Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masCity->created_at ? $masCity->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Diperbarui Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masCity->updated_at ? $masCity->updated_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.mas-cities.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
            <a href="{{ route('admin.mas-cities.edit', $masCity) }}" 
               class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit
            </a>
        </div>
    </div>

    <!-- Related Data -->
    @if($masCity->riverBasins && $masCity->riverBasins->count() > 0)
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">DAS Terkait ({{ $masCity->riverBasins->count() }})</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama DAS</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($masCity->riverBasins as $riverBasin)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $riverBasin->river_basins_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $riverBasin->river_basins_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($masCity->upts && $masCity->upts->count() > 0)
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">UPT Terkait ({{ $masCity->upts->count() }})</h3>
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
                    @foreach($masCity->upts as $upt)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $upt->upt_code ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $upt->upt_name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($masCity->deviceValues && $masCity->deviceValues->count() > 0)
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">Device Values Terkait ({{ $masCity->deviceValues->count() }})</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($masCity->deviceValues->take(10) as $deviceValue)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $deviceValue->device->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $deviceValue->value }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $deviceValue->timestamp ? \Carbon\Carbon::parse($deviceValue->timestamp)->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($masCity->deviceValues->count() > 10)
            <p class="text-sm text-gray-500 mt-2">Menampilkan 10 dari {{ $masCity->deviceValues->count() }} device values</p>
        @endif
    </div>
    @endif
</div>
@endsection