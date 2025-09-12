@extends('layouts.admin')

@section('title', 'Detail Data Actual')
@section('page-title', 'Dashboard Detail Data Actual')

@section('content')

<div class="space-y-6">

    <!-- Data Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Data Actual Info -->
        <x-admin.card title="Informasi Data">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID Data</dt>
                    <dd class="mt-1 text-sm text-gray-900">#{{ $dataActual->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Sensor Code</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->mas_sensor_code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nilai</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono text-lg">{{ number_format($dataActual->value, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Unit</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->unit ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Threshold</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dataActual->getThresholdStatusBadgeClass() }}">
                            {{ $dataActual->getThresholdStatusLabel() }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Diterima Pada</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->received_at->format('d F Y, H:i:s') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Waktu Relatif</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->received_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </x-admin.card>

        <!-- Sensor Information -->
        <x-admin.card title="Informasi Sensor">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Parameter</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->parameter ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Sensor</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $dataActual->sensor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($dataActual->sensor->status ?? 'N/A') }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Threshold Aman</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($dataActual->sensor->threshold_safe ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Threshold Waspada</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($dataActual->sensor->threshold_warning ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Threshold Bahaya</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($dataActual->sensor->threshold_danger ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Terlihat</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $dataActual->sensor->last_seen ? $dataActual->sensor->last_seen->format('d/m/Y H:i') : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </x-admin.card>
    </div>

    <!-- Device & Location Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Device Information -->
        <x-admin.card title="Informasi Device">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Device</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->device->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kode Device</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->device->code ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Device</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $dataActual->sensor->device->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($dataActual->sensor->device->status ?? 'N/A') }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Elevasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->device->elevation_m ?? 'N/A' }} m</dd>
                </div>
            </dl>
        </x-admin.card>

        <!-- Location Information -->
        <x-admin.card title="Informasi Lokasi">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Daerah Aliran Sungai</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->device->riverBasin->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kode DAS</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataActual->sensor->device->riverBasin->code ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Latitude</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $dataActual->sensor->device->latitude ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Longitude</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $dataActual->sensor->device->longitude ?? 'N/A' }}</dd>
                </div>
            </dl>
            
            @if($dataActual->sensor->device->latitude && $dataActual->sensor->device->longitude)
                <div class="mt-4">
                    <x-admin.button variant="info" size="sm" 
                        href="https://www.google.com/maps?q={{ $dataActual->sensor->device->latitude }},{{ $dataActual->sensor->device->longitude }}"
                        target="_blank">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Lihat di Google Maps
                    </x-admin.button>
                </div>
            @endif
        </x-admin.card>
    </div>

    <!-- Related Data -->
    @if($relatedData->count() > 0)
        <x-admin.card title="Data Terkait dari Sensor yang Sama">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diterima</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($relatedData as $related)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $related->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-mono">{{ number_format($related->value, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $related->getThresholdStatusBadgeClass() }}">
                                        {{ $related->getThresholdStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $related->received_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.data-actuals.show', $related) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    @endif
</div>
@endsection
