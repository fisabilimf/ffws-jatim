@extends('layouts.admin')

@section('title', 'Edit Sensor')
@section('page-title', 'Edit Sensor')
@section('page-description', 'Edit data sensor monitoring')
@section('breadcrumb', 'Sensors / Edit')

@section('content')
<div class="space-y-6">
    <x-admin.card title="Form Edit Sensor" subtitle="Kode Sensor: {{ $sensor->sensor_code }}">
        <form action="{{ route('admin.sensors.update', $sensor) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Device Selection -->
                <x-admin.form-input 
                    type="select"
                    name="device_id" 
                    label="Device" 
                    required="true" 
                    :error="$errors->first('device_id')"
                    :value="old('device_id', $sensor->device_id)"
                    :options="collect($devices)->map(function($device) {
                        return ['value' => $device->id, 'label' => $device->name . ' (' . $device->code . ')'];
                    })->toArray()"
                    class="md:col-span-2"
                />

                <!-- Sensor Code -->
                <x-admin.form-input 
                    type="text" 
                    name="sensor_code" 
                    label="Kode Sensor" 
                    placeholder="Contoh: SENSOR-WL-001" 
                    required="true" 
                    :value="old('sensor_code', $sensor->sensor_code)"
                    :error="$errors->first('sensor_code')"
                />

                <!-- Parameter -->
                <x-admin.form-input 
                    type="select"
                    name="parameter" 
                    label="Parameter" 
                    required="true" 
                    :value="old('parameter', $sensor->parameter)"
                    :error="$errors->first('parameter')"
                    :options="collect($parameterOptions)->map(function($label, $value) {
                        return ['value' => $value, 'label' => $label];
                    })->values()->toArray()"
                />

                <!-- Unit -->
                <x-admin.form-input 
                    type="text" 
                    name="unit" 
                    label="Unit" 
                    placeholder="Contoh: cm, mm, m" 
                    required="true" 
                    :value="old('unit', $sensor->unit)"
                    :error="$errors->first('unit')"
                />

                <!-- Status -->
                <x-admin.form-input 
                    type="select"
                    name="status" 
                    label="Status" 
                    required="true" 
                    :value="old('status', $sensor->status)"
                    :error="$errors->first('status')"
                    :options="collect($statusOptions)->map(function($label, $value) {
                        return ['value' => $value, 'label' => $label];
                    })->values()->toArray()"
                />

                <!-- Model Selection -->
                <x-admin.form-input 
                    type="select"
                    name="mas_model_id" 
                    label="Model Sensor" 
                    :value="old('mas_model_id', $sensor->mas_model_id)"
                    :error="$errors->first('mas_model_id')"
                    :options="collect($models)->map(function($model) {
                        return ['value' => $model->id, 'label' => $model->model_name ?? $model->id];
                    })->prepend(['value' => '', 'label' => 'Pilih Model (Opsional)'])->toArray()"
                />

                <!-- Description -->
                <x-admin.form-input 
                    type="textarea" 
                    name="description" 
                    label="Deskripsi" 
                    placeholder="Deskripsi sensor (opsional)"
                    :value="old('description', $sensor->description)"
                    :error="$errors->first('description')"
                    class="md:col-span-2"
                />
            </div>

            <!-- Threshold Section -->
            <div class="border-t pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Threshold Settings</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Safe Threshold -->
                    <x-admin.form-input 
                        type="number" 
                        name="threshold_safe" 
                        label="Threshold Safe" 
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        :value="old('threshold_safe', $sensor->threshold_safe)"
                        :error="$errors->first('threshold_safe')"
                    />

                    <!-- Warning Threshold -->
                    <x-admin.form-input 
                        type="number" 
                        name="threshold_warning" 
                        label="Threshold Warning" 
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        :value="old('threshold_warning', $sensor->threshold_warning)"
                        :error="$errors->first('threshold_warning')"
                    />

                    <!-- Danger Threshold -->
                    <x-admin.form-input 
                        type="number" 
                        name="threshold_danger" 
                        label="Threshold Danger" 
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        :value="old('threshold_danger', $sensor->threshold_danger)"
                        :error="$errors->first('threshold_danger')"
                    />
                </div>
            </div>

            <!-- Last Seen -->
            <x-admin.form-input 
                type="datetime-local" 
                name="last_seen" 
                label="Last Seen" 
                :value="old('last_seen', $sensor->last_seen ? $sensor->last_seen->format('Y-m-d\TH:i') : '')"
                :error="$errors->first('last_seen')"
            />

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.sensors.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <a href="{{ route('admin.sensors.show', $sensor) }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <button type="submit" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-save mr-2"></i>
                    Update Sensor
                </button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
