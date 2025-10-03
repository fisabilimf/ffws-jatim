@extends('layouts.admin')

@section('title', 'Regencies')
@section('page-title', 'Regencies')
@section('page-description', 'Kelola data kabupaten')
@section('breadcrumb', 'Regencies')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Kabupaten',
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
            'title' => 'Daftar Kabupaten',
            'create_url' => route('admin.mas-regencies.create'),
            'create_label' => 'Tambah Kabupaten',
            'headers' => [
                ['label' => 'ID', 'key' => 'id', 'sortable' => true],
                ['label' => 'Kode Kabupaten', 'key' => 'regencies_code', 'sortable' => true],
                ['label' => 'Nama Kabupaten', 'key' => 'regencies_name', 'sortable' => true],
                ['label' => 'Dibuat', 'key' => 'created_at', 'sortable' => true, 'type' => 'datetime'],
                ['label' => 'Aksi', 'key' => 'actions', 'sortable' => false]
            ],
            'actions' => [
                [
                    'type' => 'show',
                    'route' => 'admin.mas-regencies.show',
                    'label' => 'Detail',
                    'class' => 'btn-info'
                ],
                [
                    'type' => 'edit',
                    'route' => 'admin.mas-regencies.edit',
                    'label' => 'Edit',
                    'class' => 'btn-warning'
                ],
                [
                    'type' => 'delete',
                    'route' => 'admin.mas-regencies.destroy',
                    'label' => 'Hapus',
                    'class' => 'btn-danger',
                    'confirm' => 'Apakah Anda yakin ingin menghapus kabupaten ini?'
                ]
            ]
        ];
    @endphp

    @include('components.filter-bar', ['config' => $filterConfig])
    @include('components.table', ['config' => $tableConfig, 'data' => $regencies])
</div>
@endsection