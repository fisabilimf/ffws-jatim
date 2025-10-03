@extends('layouts.admin')

@section('title', 'Tambah Kabupaten')
@section('page-title', 'Tambah Kabupaten')
@section('page-description', 'Menambahkan data kabupaten baru')
@section('breadcrumb', 'Regencies')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.mas-regencies.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Kode Kabupaten -->
                <div>
                    <label for="regencies_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Kabupaten <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="regencies_code" 
                           name="regencies_code" 
                           value="{{ old('regencies_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('regencies_code') border-red-500 @enderror"
                           placeholder="Contoh: KAB001"
                           maxlength="10"
                           required>
                    @error('regencies_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Kabupaten -->
                <div>
                    <label for="regencies_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kabupaten <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="regencies_name" 
                           name="regencies_name" 
                           value="{{ old('regencies_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('regencies_name') border-red-500 @enderror"
                           placeholder="Contoh: Kabupaten Malang"
                           maxlength="255"
                           required>
                    @error('regencies_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.mas-regencies.index') }}" 
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