@extends('layouts.admin')

@section('title', 'Tambah Desa')
@section('page-title', 'Tambah Desa')
@section('page-description', 'Menambahkan data desa baru')
@section('breadcrumb', 'Villages')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.mas-villages.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Kode Desa -->
                <div>
                    <label for="villages_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Desa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="villages_code" 
                           name="villages_code" 
                           value="{{ old('villages_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('villages_code') border-red-500 @enderror"
                           placeholder="Contoh: DESA001"
                           maxlength="10"
                           required>
                    @error('villages_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Desa -->
                <div>
                    <label for="villages_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Desa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="villages_name" 
                           name="villages_name" 
                           value="{{ old('villages_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('villages_name') border-red-500 @enderror"
                           placeholder="Contoh: Desa Sumber Sari"
                           maxlength="255"
                           required>
                    @error('villages_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.mas-villages.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection