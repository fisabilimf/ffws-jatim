@extends('layouts.admin')

@section('content')
<div class="px-6 py-4">
    <h1 class="text-2xl font-semibold text-gray-900 mb-4">Master - Kabupaten</h1>
    <x-table
        title="Daftar Kabupaten"
        :headers="[
            ['key' => 'no', 'label' => 'No'],
            ['key' => 'kode', 'label' => 'Kode'],
            ['key' => 'nama', 'label' => 'Nama Kabupaten'],
            ['key' => 'provinsi', 'label' => 'Provinsi'],
            ['key' => 'status', 'label' => 'Status', 'format' => 'status'],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions'],
        ]"
        :rows="$kabupatens ?? []"
        searchable
        search-placeholder="Cari kabupaten..."
        :pagination="$pagination ?? null"
    >
        <x-slot:filters>
            <select class="py-2 px-3 border rounded focus:outline-none focus:ring focus:border-blue-300" name="provinsi" id="provinsi-filter">
                <option value="">Semua Provinsi</option>
                {{-- 
                    @foreach($provinsis as $provinsi)
                        <option value="{{ $provinsi->id }}">{{ $provinsi->nama }}</option>
                    @endforeach 
                --}}
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
    </x-table>
</div>
@endsection
