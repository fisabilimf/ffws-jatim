@extends('layouts.admin')

@section('title', 'Tambah Watershed')
@section('page-title', 'Tambah Watershed')
@section('page-description', 'Menambahkan data watershed baru')
@section('breadcrumb', 'Watersheds')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.mas-watersheds.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Kode Watershed -->
                <div>
                    <label for="watersheds_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Watershed <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="watersheds_code" 
                           name="watersheds_code" 
                           value="{{ old('watersheds_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('watersheds_code') border-red-500 @enderror"
                           placeholder="Contoh: WS001"
                           maxlength="10"
                           required>
                    @error('watersheds_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Watershed -->
                <div>
                    <label for="watersheds_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Watershed <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="watersheds_name" 
                           name="watersheds_name" 
                           value="{{ old('watersheds_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('watersheds_name') border-red-500 @enderror"
                           placeholder="Contoh: Brantas Watershed"
                           maxlength="255"
                           required>
                    @error('watersheds_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DAS -->
                <div>
                    <label for="river_basin_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Daerah Aliran Sungai (DAS) <span class="text-red-500">*</span>  
                    </label>
                    <select id="river_basin_code" 
                            name="river_basin_code" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('river_basin_code') border-red-500 @enderror"
                            required>
                        <option value="">Pilih DAS</option>
                        @foreach($riverBasins as $riverBasin)
                            <option value="{{ $riverBasin->river_basins_code }}" {{ old('river_basin_code') == $riverBasin->river_basins_code ? 'selected' : '' }}>
                                {{ $riverBasin->river_basins_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('river_basin_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.mas-watersheds.index') }}" 
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