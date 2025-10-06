@extends('layouts.admin')

@section('title', 'Watersheds')
@section('page-title', 'Watersheds')
@section('page-description', 'Kelola data daerah tangkapan air')
@section('breadcrumb', 'Watersheds')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Watershed',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],
            [
                'type' => 'select',
                'name' => 'river_basin_code',
                'label' => 'DAS',
                'empty_option' => 'Semua DAS',
                'options' => $riverBasins->toArray()
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

        $tableConfig = [
            'title' => 'Daftar Watersheds',
            'create_url' => route('admin.mas-watersheds.create'),
            'create_label' => 'Tambah Watershed',
            'headers' => [
                ['label' => 'ID', 'key' => 'id', 'sortable' => true],
                ['label' => 'Kode Watershed', 'key' => 'watersheds_code', 'sortable' => true],
                ['label' => 'Nama Watershed', 'key' => 'watersheds_name', 'sortable' => true],
                ['label' => 'DAS', 'key' => 'river_basin.river_basins_name', 'sortable' => false],
                ['label' => 'Dibuat', 'key' => 'created_at', 'sortable' => true, 'type' => 'datetime'],
                ['label' => 'Aksi', 'key' => 'actions', 'sortable' => false]
            ],
            'actions' => [
                [
                    'type' => 'show',
                    'route' => 'admin.mas-watersheds.show',
                    'label' => 'Detail',
                    'class' => 'btn-info'
                ],
                [
                    'type' => 'edit',
                    'route' => 'admin.mas-watersheds.edit',
                    'label' => 'Edit',
                    'class' => 'btn-warning'
                ],
                [
                    'type' => 'delete',
                    'route' => 'admin.mas-watersheds.destroy',
                    'label' => 'Hapus',
                    'class' => 'btn-danger',
                    'confirm' => 'Apakah Anda yakin ingin menghapus watershed ini?'
                ]
            ]
        ];
    @endphp

    @include('components.filter-bar', ['config' => $filterConfig])
    @include('components.table', ['config' => $tableConfig, 'data' => $watersheds])
</div>
@endsection