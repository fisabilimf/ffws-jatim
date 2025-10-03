@extends('layouts.admin')

@section('title', 'Data Prediksi')
@section('page-title', 'Data Prediksi')
@section('page-description', 'Kelola data prediksi sistem peringatan dini banjir')
@section('breadcrumb', 'Data Prediksi')

@section('content')
<div class="space-y-4">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Data Prediksi',
                'placeholder' => 'Cari berdasarkan sensor, model, atau status...'
            ],
            [
                'type' => 'select',
                'name' => 'sensor_id',
                'label' => 'Sensor',
                'empty_option' => 'Semua Sensor',
                'options' => $sensors->map(function($sensor) {
                    return [
                        'value' => $sensor->id,
                        'label' => $sensor->description . ' (' . $sensor->sensor_code . ')'
                    ];
                })->toArray()
            ],
            [
                'type' => 'select',
                'name' => 'model_id',
                'label' => 'Model',
                'empty_option' => 'Semua Model',
                'options' => $models->map(function($model) {
                    return [
                        'value' => $model->id,
                        'label' => $model->name
                    ];
                })->toArray()
            ],
            [
                'type' => 'select',
                'name' => 'status',
                'label' => 'Status Threshold',
                'empty_option' => 'Semua Status',
                'options' => [
                    ['value' => 'safe', 'label' => 'Aman'],
                    ['value' => 'warning', 'label' => 'Peringatan'],
                    ['value' => 'danger', 'label' => 'Bahaya']
                ]
            ],
            [
                'type' => 'date',
                'name' => 'date_from',
                'label' => 'Tanggal Mulai'
            ],
            [
                'type' => 'date',
                'name' => 'date_to',
                'label' => 'Tanggal Akhir'
            ]
        ];
    @endphp

    <div class="mb-6">
        <x-filter-bar 
            title="Filter & Pencarian Data Prediksi"
            :filters="$filterConfig"
            :action="route('admin.data_predictions.index')"
            gridCols="md:grid-cols-3"
        />
    </div>

    <!-- Table Section -->
    @php
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'sensor', 'label' => 'Sensor', 'format' => 'sensor'],
            ['key' => 'model', 'label' => 'Model', 'format' => 'text'],
            ['key' => 'prediction_run_at', 'label' => 'Waktu Prediksi', 'format' => 'date'],
            ['key' => 'prediction_for_ts', 'label' => 'Target Waktu', 'format' => 'date'],
            ['key' => 'predicted_value', 'label' => 'Nilai Prediksi', 'format' => 'value'],
            ['key' => 'confidence_score', 'label' => 'Skor Kepercayaan', 'format' => 'percentage'],
            ['key' => 'threshold_prediction_status', 'label' => 'Status', 'format' => 'status'],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions']
        ];

        // Format data untuk table component
        $formattedPredictions = $predictions->map(function($prediction) {
            $prediction->formatted_sensor = $prediction->masSensor->description ?? 'N/A';
            $prediction->formatted_sensor_device = $prediction->mas_sensor_code;
            $prediction->formatted_model = $prediction->masModel->name ?? 'N/A';
            $prediction->formatted_value = number_format($prediction->predicted_value, 2);
            $prediction->formatted_confidence = $prediction->confidence_score ? number_format($prediction->confidence_score * 100, 1) . '%' : '-';
            $prediction->formatted_threshold_status = $prediction->threshold_prediction_status;
            
            $prediction->actions = [
                [
                    'type' => 'view',
                    'label' => 'Lihat',
                    'icon' => 'eye',
                    'url' => route('admin.data_predictions.show', $prediction),
                    'color' => 'blue'
                ],
                [
                    'type' => 'edit',
                    'label' => 'Edit',
                    'icon' => 'edit',
                    'url' => route('admin.data_predictions.edit', $prediction),
                    'color' => 'indigo'
                ],
                [
                    'type' => 'delete',
                    'label' => 'Hapus',
                    'icon' => 'trash',
                    'url' => route('admin.data_predictions.destroy', $prediction),
                    'method' => 'DELETE',
                    'color' => 'red',
                    'confirm' => 'Apakah Anda yakin ingin menghapus data prediksi ini?'
                ]
            ];
            
            return $prediction;
        });
    @endphp

    <x-table
        title="Daftar Data Prediksi"
        :headers="$tableHeaders"
        :rows="$formattedPredictions"
        searchable
        searchPlaceholder="Cari data prediksi..."
    >
        <x-slot:actions>
            <x-admin.button href="{{ route('admin.data_predictions.create') }}" variant="primary">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Prediksi
            </x-admin.button>
        </x-slot:actions>
    </x-table>
</div>
@endsection
