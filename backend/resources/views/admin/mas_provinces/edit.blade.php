@extends('layouts.admin')

@section('title', 'Edit Provinsi')
@section('page-title', 'Edit Provinsi')
@section('page-description', 'Mengubah data provinsi')
@section('breadcrumb', 'Provinces')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.mas-provinces.update', $masProvince) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Kode Provinsi -->
                <div>
                    <label for="provinces_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Provinsi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="provinces_code" 
                           name="provinces_code" 
                           value="{{ old('provinces_code', $masProvince->provinces_code) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('provinces_code') border-red-500 @enderror"
                           placeholder="Contoh: JT001"
                           maxlength="10"
                           required>
                    @error('provinces_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Provinsi -->
                <div>
                    <label for="provinces_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Provinsi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="provinces_name" 
                           name="provinces_name" 
                           value="{{ old('provinces_name', $masProvince->provinces_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('provinces_name') border-red-500 @enderror"
                           placeholder="Contoh: Jawa Timur"
                           maxlength="255"
                           required>
                    @error('provinces_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.mas-provinces.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection