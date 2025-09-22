@extends('layouts.admin')

@section('title', 'Edit Data Prediksi')
@section('page-title', 'Edit Data Prediksi')
@section('page-description', 'Edit data prediksi yang sudah ada')
@section('breadcrumb', 'Data Prediksi / Edit')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.data_predictions.update', $dataPrediction) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sensor Selection -->
                    <div>
                        <label for="mas_sensor_id" class="block text-sm font-medium text-gray-700">
                            Sensor <span class="text-red-500">*</span>
                        </label>
                        <select name="mas_sensor_id" id="mas_sensor_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Sensor</option>
                            @foreach($sensors as $sensor)
                            <option value="{{ $sensor->id }}" {{ old('mas_sensor_id', $dataPrediction->mas_sensor_id) == $sensor->id ? 'selected' : '' }}>
                                {{ $sensor->description }} ({{ $sensor->sensor_code }})
                            </option>
                            @endforeach
                        </select>
                        @error('mas_sensor_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sensor Code -->
                    <div>
                        <label for="mas_sensor_code" class="block text-sm font-medium text-gray-700">
                            Kode Sensor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="mas_sensor_code" 
                               id="mas_sensor_code"
                               value="{{ old('mas_sensor_code', $dataPrediction->mas_sensor_code) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('mas_sensor_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Model Selection -->
                    <div>
                        <label for="mas_model_id" class="block text-sm font-medium text-gray-700">
                            Model <span class="text-red-500">*</span>
                        </label>
                        <select name="mas_model_id" id="mas_model_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Model</option>
                            @foreach($models as $model)
                                <option value="{{ $model->id }}" {{ old('mas_model_id', $dataPrediction->mas_model_id) == $model->id ? 'selected' : '' }}>
                                    {{ $model->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('mas_model_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Predicted Value -->
                    <div>
                        <label for="predicted_value" class="block text-sm font-medium text-gray-700">
                            Nilai Prediksi <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="predicted_value" 
                               id="predicted_value"
                               step="0.01"
                               value="{{ old('predicted_value', $dataPrediction->predicted_value) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('predicted_value')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prediction Run At -->
                    <div>
                        <label for="prediction_run_at" class="block text-sm font-medium text-gray-700">
                            Waktu Prediksi Dijalankan <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="prediction_run_at" 
                               id="prediction_run_at"
                               value="{{ old('prediction_run_at', $dataPrediction->prediction_run_at->format('Y-m-d\TH:i')) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('prediction_run_at')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prediction For Timestamp -->
                    <div>
                        <label for="prediction_for_ts" class="block text-sm font-medium text-gray-700">
                            Target Waktu Prediksi <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="prediction_for_ts" 
                               id="prediction_for_ts"
                               value="{{ old('prediction_for_ts', $dataPrediction->prediction_for_ts->format('Y-m-d\TH:i')) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('prediction_for_ts')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confidence Score -->
                    <div>
                        <label for="confidence_score" class="block text-sm font-medium text-gray-700">
                            Skor Kepercayaan (0-1)
                        </label>
                        <input type="number" 
                               name="confidence_score" 
                               id="confidence_score"
                               step="0.01"
                               min="0"
                               max="1"
                               value="{{ old('confidence_score', $dataPrediction->confidence_score) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('confidence_score')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Threshold Prediction Status -->
                    <div>
                        <label for="threshold_prediction_status" class="block text-sm font-medium text-gray-700">
                            Status Threshold
                        </label>
                        <select name="threshold_prediction_status" id="threshold_prediction_status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Status</option>
                            <option value="safe" {{ old('threshold_prediction_status', $dataPrediction->threshold_prediction_status) == 'safe' ? 'selected' : '' }}>Aman</option>
                            <option value="warning" {{ old('threshold_prediction_status', $dataPrediction->threshold_prediction_status) == 'warning' ? 'selected' : '' }}>Peringatan</option>
                            <option value="danger" {{ old('threshold_prediction_status', $dataPrediction->threshold_prediction_status) == 'danger' ? 'selected' : '' }}>Bahaya</option>
                        </select>
                        @error('threshold_prediction_status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.data_predictions.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fa-solid fa-save -ml-1 mr-2"></i>
                        Update Data Prediksi
                    </button>
                </div>
            </form>
        </div>
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
