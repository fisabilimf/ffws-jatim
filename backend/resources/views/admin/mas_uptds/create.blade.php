@extends('admin.layouts.admin')

@section('title', 'Create New UPTD')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create New UPTD</h1>
    <nav class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.mas-uptds.index') }}" class="hover:text-gray-900 dark:hover:text-white">UPTDs</a>
        <span class="mx-2">/</span>
        <span>Create</span>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">UPTD Information</h2>
            </div>
            
            <form action="{{ route('admin.mas-uptds.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- UPTD Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        UPTD Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('code') border-red-500 @enderror"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- UPTD Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        UPTD Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- UPT -->
                <div>
                    <label for="upt_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        UPT <span class="text-red-500">*</span>
                    </label>
                    <select id="upt_code" 
                            name="upt_code" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('upt_code') border-red-500 @enderror"
                            required>
                        <option value="">Select UPT</option>
                        @foreach($upts as $upt)
                            <option value="{{ $upt->upts_code }}" {{ old('upt_code') == $upt->upts_code ? 'selected' : '' }}>
                                {{ $upt->upts_name }} ({{ $upt->upts_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('upt_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.mas-uptds.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create UPTD
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection