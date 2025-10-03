@extends('layouts.admin')

@section('title', 'Provinces')
@section('page-title', 'Provinces')
@section('page-description', 'Kelola data provinsi')
@section('breadcrumb', 'Provinces')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Provinsi',
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
            'title' => 'Daftar Provinsi',
            'create_url' => route('admin.mas-provinces.create'),
            'create_label' => 'Tambah Provinsi',
            'headers' => [
                ['label' => 'ID', 'key' => 'id', 'sortable' => true],
                ['label' => 'Kode Provinsi', 'key' => 'provinces_code', 'sortable' => true],
                ['label' => 'Nama Provinsi', 'key' => 'provinces_name', 'sortable' => true],
                ['label' => 'Dibuat', 'key' => 'created_at', 'sortable' => true, 'type' => 'datetime'],
                ['label' => 'Aksi', 'key' => 'actions', 'sortable' => false]
            ],
            'actions' => [
                [
                    'type' => 'show',
                    'route' => 'admin.mas-provinces.show',
                    'label' => 'Detail',
                    'class' => 'btn-info'
                ],
                [
                    'type' => 'edit',
                    'route' => 'admin.mas-provinces.edit',
                    'label' => 'Edit',
                    'class' => 'btn-warning'
                ],
                [
                    'type' => 'delete',
                    'route' => 'admin.mas-provinces.destroy',
                    'label' => 'Hapus',
                    'class' => 'btn-danger',
                    'confirm' => 'Apakah Anda yakin ingin menghapus provinsi ini?'
                ]
            ]
        ];
    @endphp

    @include('components.filter-bar', ['config' => $filterConfig])
    @include('components.table', ['config' => $tableConfig, 'data' => $provinces])
</div>
@endsection