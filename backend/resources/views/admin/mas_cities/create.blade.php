@extends('layouts.admin')

@section('title', 'Tambah Kota')
@section('page-title', 'Tambah Kota')
@section('page-description', 'Menambahkan data kota baru')
@section('breadcrumb', 'Cities')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.mas-cities.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Kode Kota -->
                <div>
                    <label for="cities_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Kota <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="cities_code" 
                           name="cities_code" 
                           value="{{ old('cities_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('cities_code') border-red-500 @enderror"
                           placeholder="Contoh: MLG001"
                           maxlength="10"
                           required>
                    @error('cities_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Kota -->
                <div>
                    <label for="cities_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kota <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="cities_name" 
                           name="cities_name" 
                           value="{{ old('cities_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('cities_name') border-red-500 @enderror"
                           placeholder="Contoh: Kota Malang"
                           maxlength="255"
                           required>
                    @error('cities_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.mas-cities.index') }}" 
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