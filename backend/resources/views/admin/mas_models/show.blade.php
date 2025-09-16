@extends('layouts.admin')

@section('title', 'Detail Model')
@section('page-title', 'Detail Model')
@section('page-description', 'Detail informasi model prediksi')
@section('breadcrumb', 'Model / Detail')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $model->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Detail informasi model prediksi</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.mas-models.form.edit', $model->id) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-edit -ml-1 mr-2"></i>
                Edit Model
            </a>
            <a href="{{ route('admin.mas-models.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left -ml-1 mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Model Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Informasi Model</h3>
        </div>
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Model</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $model->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tipe Model</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($model->model_type === 'lstm') bg-blue-100 text-blue-800
                            @elseif($model->model_type === 'gru') bg-green-100 text-green-800
                            @elseif($model->model_type === 'transformer') bg-purple-100 text-purple-800
                            @elseif($model->model_type === 'cnn') bg-yellow-100 text-yellow-800
                            @elseif($model->model_type === 'rnn') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ strtoupper($model->model_type) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Versi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $model->version ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $model->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $model->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Path File</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($model->file_path)
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $model->file_path }}</code>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $model->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Diupdate</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $model->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Model Configuration -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Konfigurasi Model</h3>
        </div>
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jumlah Step Input</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($model->n_steps_in)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $model->n_steps_in }} steps
                            </span>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jumlah Step Output</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($model->n_steps_out)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ $model->n_steps_out }} steps
                            </span>
                        @else
                            -
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Description -->
    @if($model->description)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Deskripsi</h3>
        </div>
        <div class="px-6 py-4">
            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $model->description }}</p>
        </div>
    </div>
    @endif

    <!-- Related Sensors -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sensor Terkait</h3>
            <p class="mt-1 text-sm text-gray-500">Sensor yang menggunakan model ini</p>
        </div>
        <div class="px-6 py-4">
            @if($model->sensors->count() > 0)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($model->sensors as $sensor)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $sensor->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $sensor->code }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $sensor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sensor->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-microchip text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada sensor yang menggunakan model ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
