@extends('layouts.admin')

@section('title', 'Edit UPT')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit UPT</h1>
    <nav class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.mas-upts.index') }}" class="hover:text-gray-900 dark:hover:text-white">UPTs</a>
        <span class="mx-2">/</span>
        <span>Edit</span>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Edit UPT Information</h2>
            </div>
            
            <form action="{{ route('admin.mas-upts.update', $masUpt) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <!-- UPT Code -->
                <div>
                    <label for="upts_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        UPT Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="upts_code" 
                           name="upts_code" 
                           value="{{ old('upts_code', $masUpt->upts_code) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('upts_code') border-red-500 @enderror"
                           required>
                    @error('upts_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- UPT Name -->
                <div>
                    <label for="upts_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        UPT Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="upts_name" 
                           name="upts_name" 
                           value="{{ old('upts_name', $masUpt->upts_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('upts_name') border-red-500 @enderror"
                           required>
                    @error('upts_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- River Basin -->
                <div>
                    <label for="river_basin_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        River Basin <span class="text-red-500">*</span>
                    </label>
                    <select id="river_basin_code" 
                            name="river_basin_code" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('river_basin_code') border-red-500 @enderror"
                            required>
                        <option value="">Select River Basin</option>
                        @foreach($riverBasins as $riverBasin)
                            <option value="{{ $riverBasin->river_basins_code }}" 
                                    {{ old('river_basin_code', $masUpt->river_basin_code) == $riverBasin->river_basins_code ? 'selected' : '' }}>
                                {{ $riverBasin->river_basin_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('river_basin_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="cities_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <select id="cities_code" 
                            name="cities_code" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('cities_code') border-red-500 @enderror"
                            required>
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->cities_code }}" 
                                    {{ old('cities_code', $masUpt->cities_code) == $city->cities_code ? 'selected' : '' }}>
                                {{ $city->cities_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('cities_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.mas-upts.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update UPT
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection