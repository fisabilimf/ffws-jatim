@extends('layouts.admin')

@section('content')
<div class="px-6 py-4">
    <h1 class="text-2xl font-semibold text-gray-900 mb-4">Master - Desa</h1>
    <x-datatable
        title="Daftar Desa"
        :headers="[
            ['key' => 'no', 'label' => 'No'],
            ['key' => 'kode', 'label' => 'Kode'],
            ['key' => 'nama', 'label' => 'Nama Desa'],
            ['key' => 'kecamatan', 'label' => 'Kecamatan'],
            ['key' => 'status', 'label' => 'Status', 'format' => 'status'],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions'],
        ]"
        :rows="[
            ['no' => 1, 'kode' => '351001', 'nama' => 'Desa Ketegan', 'kecamatan' => 'Taman', 'status' => 'active', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
            ['no' => 2, 'kode' => '351002', 'nama' => 'Desa Geluran', 'kecamatan' => 'Taman', 'status' => 'active', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
            ['no' => 3, 'kode' => '351003', 'nama' => 'Desa Buncitan', 'kecamatan' => 'Sedati', 'status' => 'inactive', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
        ]"
        searchable
        search-placeholder="Cari desa..."
        pagination-text="Menampilkan 1–3 dari 3 data"
    >
        <x-slot:filters>
            <select class="py-2 px-3 border rounded focus:outline-none focus:ring focus:border-blue-300">
                <option value="">Semua Kecamatan</option>
                <option>Taman</option>
                <option>Sedati</option>
            </select>
        </x-slot:filters>
        <x-slot:actions>
            <button class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                <i class="fa-solid fa-plus"></i>
                Tambah Desa
            </button>
        </x-slot:actions>
    </x-datatable>
@endsection


