@extends('layouts.admin')

@section('title', 'Edit Data Prediksi')
@section('page-title', 'Edit Data Prediksi')
@section('page-description', 'Edit data prediksi yang sudah ada')
@section('breadcrumb', 'Data Prediksi / Edit')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-admin.card 
        title="Form Edit Data Prediksi" 
        subtitle="Edit data prediksi yang sudah ada">
        
        <form action="{{ route('admin.data_predictions.update', $dataPrediction) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sensor Selection -->
                    <x-admin.form-input 
                        type="select"
                        name="mas_sensor_id" 
                        label="Sensor" 
                        required="true" 
                        value="{{ old('mas_sensor_id', $dataPrediction->mas_sensor_id) }}"
                        :error="$errors->first('mas_sensor_id')"
                        :options="collect($sensors)->map(function($sensor) {
                            return ['value' => $sensor->id, 'label' => $sensor->description . ' (' . $sensor->sensor_code . ')'];
                        })->prepend(['value' => '', 'label' => 'Pilih Sensor'])->toArray()"
                    />

                    <!-- Sensor Code -->
                    <x-admin.form-input 
                        type="text" 
                        name="mas_sensor_code" 
                        label="Kode Sensor" 
                        value="{{ old('mas_sensor_code', $dataPrediction->mas_sensor_code) }}"
                        required="true" 
                        :error="$errors->first('mas_sensor_code')"
                    />

                    <!-- Model Selection -->
                    <x-admin.form-input 
                        type="select"
                        name="mas_model_id" 
                        label="Model" 
                        required="true" 
                        value="{{ old('mas_model_id', $dataPrediction->mas_model_id) }}"
                        :error="$errors->first('mas_model_id')"
                        :options="collect($models)->map(function($model) {
                            return ['value' => $model->id, 'label' => $model->name];
                        })->prepend(['value' => '', 'label' => 'Pilih Model'])->toArray()"
                    />

                    <!-- Predicted Value -->
                    <x-admin.form-input 
                        type="number" 
                        name="predicted_value" 
                        label="Nilai Prediksi" 
                        step="0.01"
                        value="{{ old('predicted_value', $dataPrediction->predicted_value) }}"
                        required="true" 
                        :error="$errors->first('predicted_value')"
                    />

                    <!-- Prediction Run At -->
                    <x-admin.form-input 
                        type="datetime-local" 
                        name="prediction_run_at" 
                        label="Waktu Prediksi Dijalankan" 
                        value="{{ old('prediction_run_at', $dataPrediction->prediction_run_at->format('Y-m-d\TH:i')) }}"
                        required="true" 
                        :error="$errors->first('prediction_run_at')"
                    />

                    <!-- Prediction For Timestamp -->
                    <x-admin.form-input 
                        type="datetime-local" 
                        name="prediction_for_ts" 
                        label="Target Waktu Prediksi" 
                        value="{{ old('prediction_for_ts', $dataPrediction->prediction_for_ts->format('Y-m-d\TH:i')) }}"
                        required="true" 
                        :error="$errors->first('prediction_for_ts')"
                    />

                    <!-- Confidence Score -->
                    <x-admin.form-input 
                        type="number" 
                        name="confidence_score" 
                        label="Skor Kepercayaan (0-1)" 
                        step="0.01"
                        min="0"
                        max="1"
                        value="{{ old('confidence_score', $dataPrediction->confidence_score) }}"
                        :error="$errors->first('confidence_score')"
                    />

                    <!-- Threshold Prediction Status -->
                    <x-admin.form-input 
                        type="select"
                        name="threshold_prediction_status" 
                        label="Status Threshold" 
                        value="{{ old('threshold_prediction_status', $dataPrediction->threshold_prediction_status) }}"
                        :error="$errors->first('threshold_prediction_status')"
                        :options="[
                            ['value' => '', 'label' => 'Pilih Status'],
                            ['value' => 'safe', 'label' => 'Aman'],
                            ['value' => 'warning', 'label' => 'Peringatan'],
                            ['value' => 'danger', 'label' => 'Bahaya']
                        ]"
                    />
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <x-admin.button href="{{ route('admin.data_predictions.index') }}" variant="outline">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" variant="primary">
                        <i class="fa-solid fa-save -ml-1 mr-2"></i>
                        Update Data Prediksi
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
</div>

@push('scripts')
<script>
    // Auto-fill sensor code when sensor is selected
    document.getElementById('mas_sensor_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const sensorCode = selectedOption.textContent.match(/\(([^)]+)\)/);
            if (sensorCode) {
                document.getElementById('mas_sensor_code').value = sensorCode[1];
            }
        }
    });
</script>
@endpush
@endsection
