@extends('layouts.admin')

@section('title', 'Model')
@section('page-title', 'Model')
@section('page-description', 'Kelola data model prediksi')
@section('breadcrumb', 'Model')

@section('content')
<div class="space-y-6" x-data="modelsPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Model',
                'placeholder' => 'Cari berdasarkan nama model...'
            ],
            [
                'type' => 'select',
                'name' => 'per_page',
                'label' => 'Per Halaman',
                'options' => [
                    ['value' => '10', 'label' => '10'],
                    ['value' => '15', 'label' => '15'],
                    ['value' => '25', 'label' => '25'],
                    ['value' => '50', 'label' => '50'],
                    ['value' => '100', 'label' => '100']
                ]
            ]
        ];
    @endphp

    <x-filter-bar 
        title="Filter & Pencarian Model"
        :filters="$filterConfig"
        :action="route('admin.mas-models.index')"
        gridCols="md:grid-cols-2"
        compact="true"
    />

    <x-table
        title="Daftar Model"
        :headers="$tableHeaders"
        :rows="$models"
        searchable
        searchPlaceholder="Cari model..."
    >
        <x-slot:actions>
            <a href="{{ route('admin.mas-models.form') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Model
            </a>
        </x-slot:actions>
    </x-table>

</div>

@push('scripts')
@include('admin.mas_models.script')
<script>
function modelsPage() {
    return {
        init() {
            // Inisialisasi halaman index
            console.log('Models page initialized');
        }
    }
}
</script>
@endpush
@endsection
