@extends('layouts.admin')

@section('title', 'Villages')
@section('page-title', 'Villages')
@section('page-description', 'Kelola data desa')
@section('breadcrumb', 'Villages')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Desa',
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
            'title' => 'Daftar Desa',
            'create_url' => route('admin.mas-villages.create'),
            'create_label' => 'Tambah Desa',
            'headers' => [
                ['label' => 'ID', 'key' => 'id', 'sortable' => true],
                ['label' => 'Kode Desa', 'key' => 'villages_code', 'sortable' => true],
                ['label' => 'Nama Desa', 'key' => 'villages_name', 'sortable' => true],
                ['label' => 'Dibuat', 'key' => 'created_at', 'sortable' => true, 'type' => 'datetime'],
                ['label' => 'Aksi', 'key' => 'actions', 'sortable' => false]
            ],
            'actions' => [
                [
                    'type' => 'show',
                    'route' => 'admin.mas-villages.show',
                    'label' => 'Detail',
                    'class' => 'btn-info'
                ],
                [
                    'type' => 'edit',
                    'route' => 'admin.mas-villages.edit',
                    'label' => 'Edit',
                    'class' => 'btn-warning'
                ],
                [
                    'type' => 'delete',
                    'route' => 'admin.mas-villages.destroy',
                    'label' => 'Hapus',
                    'class' => 'btn-danger',
                    'confirm' => 'Apakah Anda yakin ingin menghapus desa ini?'
                ]
            ]
        ];
    @endphp

    @include('components.filter-bar', ['config' => $filterConfig])
    @include('components.table', ['config' => $tableConfig, 'data' => $villages])
</div>
@endsection