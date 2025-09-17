@extends('layouts.admin')

@section('title', 'Detail Sensor')
@section('page-title', 'Detail Sensor')
@section('page-description', 'Detail informasi sensor monitoring')
@section('breadcrumb', 'Sensors / Detail')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $sensor->sensor_code }}</h1>
            <p class="text-sm text-gray-500">{{ $sensor->description ?? 'Tidak ada deskripsi' }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.sensors.edit', $sensor) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-edit mr-2"></i>
                Edit Sensor
            </a>
            <a href="{{ route('admin.sensors.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Sensor Details -->
            <x-admin.card title="Informasi Sensor">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kode Sensor</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $sensor->sensor_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Parameter</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $sensor->parameter == 'water_level' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $parameterOptions[$sensor->parameter] }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sensor->unit }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $sensor->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $statusOptions[$sensor->status] }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Seen</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($sensor->last_seen)
                                {{ $sensor->last_seen->format('d/m/Y H:i:s') }}
                                <span class="text-gray-500">({{ $sensor->last_seen->diffForHumans() }})</span>
                            @else
                                <span class="text-gray-400">Belum pernah terlihat</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $sensor->created_at->format('d/m/Y H:i:s') }}
                            <span class="text-gray-500">({{ $sensor->created_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $sensor->updated_at->format('d/m/Y H:i:s') }}
                            <span class="text-gray-500">({{ $sensor->updated_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Threshold Information -->
            <x-admin.card title="Threshold Settings">
                @if($sensor->threshold_safe || $sensor->threshold_warning || $sensor->threshold_danger)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $sensor->threshold_safe ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500">Safe Threshold</div>
                            <div class="text-xs text-gray-400">{{ $sensor->unit }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $sensor->threshold_warning ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500">Warning Threshold</div>
                            <div class="text-xs text-gray-400">{{ $sensor->unit }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">
                                {{ $sensor->threshold_danger ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500">Danger Threshold</div>
                            <div class="text-xs text-gray-400">{{ $sensor->unit }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Tidak ada threshold yang dikonfigurasi</p>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <!-- Sidebar Information -->
        <div class="space-y-6">
            <!-- Device Information -->
            <x-admin.card title="Device">
                @if($sensor->device)
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Device</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sensor->device->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kode Device</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $sensor->device->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Device</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $sensor->device->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sensor->device->status == 'active' ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($sensor->device->latitude && $sensor->device->longitude)
                                    {{ $sensor->device->latitude }}, {{ $sensor->device->longitude }}
                                @else
                                    <span class="text-gray-400">Tidak tersedia</span>
                                @endif
                            </dd>
                        </div>
                        <div class="pt-4 border-t">
                            <a href="{{ route('admin.devices.index') }}" 
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Lihat Daftar Device
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-gray-400 text-2xl mb-2"></i>
                        <p class="text-gray-500 text-sm">Device tidak ditemukan</p>
                    </div>
                @endif
            </x-admin.card>

            <!-- Model Information -->
            <x-admin.card title="Model Sensor">
                @if($sensor->masModel)
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Model</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sensor->masModel->model_name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Manufacturer</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sensor->masModel->manufacturer ?? 'N/A' }}</dd>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                        <p class="text-gray-500 text-sm">Tidak ada model yang dipilih</p>
                    </div>
                @endif
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    <a href="{{ route('admin.sensors.edit', $sensor) }}" 
                       class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Sensor
                    </a>
                    <form action="{{ route('admin.sensors.destroy', $sensor) }}" method="POST" class="w-full" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus sensor ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Sensor
                        </button>
                    </form>
                </div>
            </x-admin.card>
        </div>
    </div>
</div>
@endsection
