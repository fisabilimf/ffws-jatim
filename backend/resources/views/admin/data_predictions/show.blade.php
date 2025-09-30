@extends('layouts.admin')

@section('title', 'Detail Data Prediksi')
@section('page-title', 'Detail Data Prediksi')
@section('page-description', 'Lihat detail data prediksi')
@section('breadcrumb', 'Data Prediksi / Detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detail Data Prediksi #{{ $dataPrediction->id }}</h1>
            <p class="mt-1 text-sm text-gray-600">Informasi lengkap data prediksi</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.data_predictions.edit', $dataPrediction) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-edit -ml-1 mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.data_predictions.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-arrow-left -ml-1 mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Information Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Prediksi</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Detail lengkap data prediksi sistem peringatan dini banjir</p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">ID Prediksi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">#{{ $dataPrediction->id }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Sensor</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div>
                            <div class="font-medium">{{ $dataPrediction->masSensor->description ?? 'N/A' }}</div>
                            <div class="text-gray-500">Kode: {{ $dataPrediction->mas_sensor_code }}</div>
                        </div>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Model</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $dataPrediction->masModel->name ?? 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Waktu Prediksi Dijalankan</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $dataPrediction->prediction_run_at->format('d F Y, H:i:s') }}
                        <span class="text-gray-500">({{ $dataPrediction->prediction_run_at->diffForHumans() }})</span>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Target Waktu Prediksi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $dataPrediction->prediction_for_ts->format('d F Y, H:i:s') }}
                        <span class="text-gray-500">({{ $dataPrediction->prediction_for_ts->diffForHumans() }})</span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nilai Prediksi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="font-mono text-lg font-semibold">{{ number_format($dataPrediction->predicted_value, 2) }}</span>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Skor Kepercayaan</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($dataPrediction->confidence_score)
                            <div class="flex items-center">
                                <span class="font-mono text-lg font-semibold">{{ number_format($dataPrediction->confidence_score * 100, 1) }}%</span>
                                <div class="ml-4 flex-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" data-width="{{ round($dataPrediction->confidence_score * 100) }}"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400">Tidak tersedia</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status Threshold</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($dataPrediction->threshold_prediction_status)
                            @php
                                $statusClasses = [
                                    'safe' => 'bg-green-100 text-green-800',
                                    'warning' => 'bg-yellow-100 text-yellow-800',
                                    'danger' => 'bg-red-100 text-red-800'
                                ];
                                $statusLabels = [
                                    'safe' => 'Aman',
                                    'warning' => 'Peringatan',
                                    'danger' => 'Bahaya'
                                ];
                                $statusIcons = [
                                    'safe' => 'fa-check-circle',
                                    'warning' => 'fa-exclamation-triangle',
                                    'danger' => 'fa-exclamation-circle'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$dataPrediction->threshold_prediction_status] }}">
                                <i class="fas {{ $statusIcons[$dataPrediction->threshold_prediction_status] }} -ml-1 mr-2"></i>
                                {{ $statusLabels[$dataPrediction->threshold_prediction_status] }}
                            </span>
                        @else
                            <span class="text-gray-400">Tidak tersedia</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $dataPrediction->created_at->format('d F Y, H:i:s') }}
                        <span class="text-gray-500">({{ $dataPrediction->created_at->diffForHumans() }})</span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $dataPrediction->updated_at->format('d F Y, H:i:s') }}
                        <span class="text-gray-500">({{ $dataPrediction->updated_at->diffForHumans() }})</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between">
        <form action="{{ route('admin.data_predictions.destroy', $dataPrediction) }}" 
              method="POST" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data prediksi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="fa-solid fa-trash -ml-1 mr-2"></i>
                Hapus Data Prediksi
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set progress bar width
    const progressBar = document.querySelector('[data-width]');
    if (progressBar) {
        const width = progressBar.getAttribute('data-width');
        progressBar.style.width = width + '%';
    }
});
</script>
@endpush
