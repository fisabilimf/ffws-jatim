@extends('layouts.admin')

@section('title', 'Detail Data Actual')
@section('page-title', 'Dashboard Detail Data Actual')

@section('content')

<div class="space-y-6">
    <!-- Action Buttons -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <x-admin.button href="{{ route('admin.data-actuals.index') }}" variant="outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </x-admin.button>
        </div>
        <div class="flex items-center space-x-2">
            <x-admin.button type="button" variant="primary" onclick="openEditModal({{ $dataActual->id }})">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </x-admin.button>
            <form action="{{ route('admin.data-actuals.destroy', $dataActual) }}" method="POST" class="inline delete-form" 
                  data-confirm-delete="Data actual ini akan dihapus. Lanjutkan?">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" variant="danger">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus
                </x-admin.button>
                </button>
            </form>
        </div>
    </div>

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
        <x-table
            title="Data Terkait dari Sensor yang Sama"
            :headers="[
                ['key' => 'id', 'label' => 'ID'],
                ['key' => 'value', 'label' => 'Nilai', 'format' => 'value'],
                ['key' => 'threshold_status', 'label' => 'Status', 'format' => 'status'],
                ['key' => 'received_at', 'label' => 'Diterima', 'format' => 'date'],
                ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions']
            ]"
            :rows="$relatedData->map(function($related) {
                return [
                    'id' => '#' . $related->id,
                    'value' => $related->value,
                    'threshold_status' => $related->threshold_status,
                    'received_at' => $related->received_at,
                    'actions' => [
                        [
                            'type' => 'view',
                            'label' => 'Detail',
                            'url' => route('admin.data-actuals.show', $related),
                            'icon' => 'eye'
                        ]
                    ]
                ];
            })"
            class="mt-4"
        />
    @endif
</div>

<!-- Modal Edit Data Actual -->
<x-admin.modal name="dataActualModal" size="lg" title="Data Actual">
    <form id="dataActualForm" class="space-y-4">
        @csrf
        <input type="hidden" id="dataActualId" name="id">
        <input type="hidden" id="formMethod" name="_method" value="POST">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="mas_sensor_id" class="block text-sm font-medium text-gray-700 mb-1">Sensor</label>
                <select id="mas_sensor_id" name="mas_sensor_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Sensor</option>
                </select>
                <div id="sensor_info" class="mt-1 text-sm text-gray-500"></div>
            </div>
            
            <div>
                <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                <input type="number" id="value" name="value" step="0.01" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Masukkan nilai">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="received_at" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu</label>
                <input type="datetime-local" id="received_at" name="received_at" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="threshold_status" class="block text-sm font-medium text-gray-700 mb-1">Status Threshold</label>
                <select id="threshold_status" name="threshold_status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Status</option>
                    <option value="safe">Aman</option>
                    <option value="warning">Waspada</option>
                    <option value="danger">Bahaya</option>
                </select>
            </div>
        </div>
    </form>
    
    <x-slot name="footer">
        <x-admin.button variant="outline" onclick="closeModal()">Batal</x-admin.button>
        <x-admin.button variant="primary" onclick="submitForm()" id="submitBtn">
            <span id="submitText">Update</span>
        </x-admin.button>
    </x-slot>
</x-admin.modal>

@endsection

@push('scripts')
@include('admin.data_actuals.script')
<script>
// Override untuk show page - set edit mode dan current ID
document.addEventListener('DOMContentLoaded', function() {
    if (window.dataActualsManager) {
        window.dataActualsManager.isEditMode = true;
        window.dataActualsManager.currentDataActualId = {{ $dataActual->id ?? 0 }};
    }
});
</script>
@endpush
