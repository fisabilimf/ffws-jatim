@extends('layouts.admin')

@section('title', 'Manajemen Users')
@section('page-title', 'Manajemen Users')
@section('page-description', 'Kelola semua user dalam sistem')
@section('breadcrumb', 'Users')

@section('content')
<div class="space-y-6">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari User',
                'placeholder' => 'Cari berdasarkan nama atau email...'
            ],
            [
                'type' => 'select',
                'name' => 'role',
                'label' => 'Role',
                'empty_option' => 'Semua Role',
                'options' => [
                    ['value' => 'admin', 'label' => 'Admin'],
                    ['value' => 'user', 'label' => 'User'],
                    ['value' => 'operator', 'label' => 'Operator']
                ]
            ],
            [
                'type' => 'select',
                'name' => 'status',
                'label' => 'Status',
                'empty_option' => 'Semua Status',
                'options' => [
                    ['value' => 'active', 'label' => 'Aktif'],
                    ['value' => 'inactive', 'label' => 'Non-aktif']
                ]
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
        title="Filter & Pencarian Users"
        :filters="$filterConfig"
        :action="route('admin.users.index')"
        gridCols="md:grid-cols-4"
    />

    <!-- Users Table -->
    <x-table
        title="Daftar Users"
        :headers="$tableHeaders"
        :rows="$users"
        searchable
        :pagination="$users->links()"
    >
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah User
            </a>
        </x-slot:actions>
    </x-table>
</div>
@endsection


