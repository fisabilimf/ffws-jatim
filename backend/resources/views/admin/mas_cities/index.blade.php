@extends('layouts.admin')

@section('title', 'Cities')
@section('page-title', 'Cities')
@section('page-description', 'Kelola data kota')
@section('breadcrumb', 'Cities')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Kota',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
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
            'title' => 'Daftar Kota',
            'create_url' => route('admin.mas-cities.create'),
            'create_label' => 'Tambah Kota',
            'headers' => [
                ['label' => 'ID', 'key' => 'id', 'sortable' => true],
                ['label' => 'Kode Kota', 'key' => 'cities_code', 'sortable' => true],
                ['label' => 'Nama Kota', 'key' => 'cities_name', 'sortable' => true],
                ['label' => 'Dibuat', 'key' => 'created_at', 'sortable' => true, 'type' => 'datetime'],
                ['label' => 'Aksi', 'key' => 'actions', 'sortable' => false]
            ],
            'actions' => [
                [
                    'type' => 'show',
                    'route' => 'admin.mas-cities.show',
                    'label' => 'Detail',
                    'class' => 'btn-info'
                ],
                [
                    'type' => 'edit',
                    'route' => 'admin.mas-cities.edit',
                    'label' => 'Edit',
                    'class' => 'btn-warning'
                ],
                [
                    'type' => 'delete',
                    'route' => 'admin.mas-cities.destroy',
                    'label' => 'Hapus',
                    'class' => 'btn-danger',
                    'confirm' => 'Apakah Anda yakin ingin menghapus kota ini?'
                ]
            ]
        ];
    @endphp

    @include('components.filter-bar', ['config' => $filterConfig])
    @include('components.table', ['config' => $tableConfig, 'data' => $cities])
</div>
@endsection