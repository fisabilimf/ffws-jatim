@extends('layouts.admin')

@section('content')
<div class="px-6 py-4">
    <h1 class="text-2xl font-semibold text-gray-900 mb-4">Master - Kabupaten</h1>
    <x-datatable
        title="Daftar Kabupaten"
        :headers="[
            ['key' => 'no', 'label' => 'No'],
            ['key' => 'kode', 'label' => 'Kode'],
            ['key' => 'nama', 'label' => 'Nama Kabupaten'],
            ['key' => 'provinsi', 'label' => 'Provinsi'],
            ['key' => 'status', 'label' => 'Status', 'format' => 'status'],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions'],
        ]"
        :rows="[
            ['no' => 1, 'kode' => '3510', 'nama' => 'Kabupaten Sidoarjo', 'provinsi' => 'Jawa Timur', 'status' => 'active', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
            ['no' => 2, 'kode' => '3578', 'nama' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur', 'status' => 'active', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
            ['no' => 3, 'kode' => '3309', 'nama' => 'Kabupaten Semarang', 'provinsi' => 'Jawa Tengah', 'status' => 'inactive', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
        ]"
        searchable
        search-placeholder="Cari kabupaten..."
        pagination-text="Menampilkan 1–3 dari 3 data"
    >
        <x-slot:filters>
            <select class="py-2 px-3 border rounded focus:outline-none focus:ring focus:border-blue-300">
                <option value="">Semua Provinsi</option>
                <option>Jawa Timur</option>
                <option>Jawa Tengah</option>
                <option>Jawa Barat</option>
            </select>
        </x-slot:filters>
        <x-slot:actions>
            <button class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                <i class="fa-solid fa-plus"></i>
                Tambah Kabupaten
            </button>
            <button class="inline-flex items-center gap-2 border text-gray-700 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded">
                <i class="fa-solid fa-table"></i>
                Ekspor
            </button>
        </x-slot:actions>
    </x-datatable>
</div>
@endsection


