@extends('layouts.admin')

@section('content')
<div class="px-6 py-4">
    <h1 class="text-2xl font-semibold text-gray-900 mb-4">Master - Kecamatan</h1>
    <x-table
        title="Daftar Kecamatan"
        :headers="[
            ['key' => 'no', 'label' => 'No'],
            ['key' => 'kode', 'label' => 'Kode'],
            ['key' => 'nama', 'label' => 'Nama Kecamatan'],
            ['key' => 'kabupaten', 'label' => 'Kabupaten/Kota'],
            ['key' => 'status', 'label' => 'Status', 'format' => 'status'],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions'],
        ]"
        :rows="[
            ['no' => 1, 'kode' => '351001', 'nama' => 'Kecamatan Taman', 'kabupaten' => 'Kabupaten Sidoarjo', 'status' => 'active', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
            ['no' => 2, 'kode' => '351002', 'nama' => 'Kecamatan Waru', 'kabupaten' => 'Kabupaten Sidoarjo', 'status' => 'active', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
            ['no' => 3, 'kode' => '351003', 'nama' => 'Kecamatan Sedati', 'kabupaten' => 'Kabupaten Sidoarjo', 'status' => 'inactive', 'actions' => [
                ['label' => 'Detail', 'url' => '#', 'color' => 'gray'],
                ['label' => 'Edit', 'url' => '#', 'color' => 'yellow'],
                ['label' => 'Hapus', 'url' => '#', 'color' => 'red', 'method' => 'DELETE', 'confirm' => 'Hapus data ini?'],
            ]],
        ]"
        searchable
        search-placeholder="Cari kecamatan..."
        pagination-text="Menampilkan 1–3 dari 3 data"
    >
        <x-slot:filters>
            <select class="py-2 px-3 border rounded focus:outline-none focus:ring focus:border-blue-300">
                <option value="">Semua Kabupaten/Kota</option>
                <option>Kabupaten Sidoarjo</option>
                <option>Kota Surabaya</option>
            </select>
        </x-slot:filters>
        <x-slot:actions>
            <button class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                <i class="fa-solid fa-plus"></i>
                Tambah Kecamatan
            </button>
        </x-slot:actions>
    </x-table>
@endsection


