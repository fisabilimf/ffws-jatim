@extends('admin.layouts.admin')

@section('title', 'UPTs Management')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">UPTs Management</h1>
    <nav class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Dashboard</a>
        <span class="mx-2">/</span>
        <span>UPTs</span>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Filter Section -->
        <x-filter-bar>
            <form method="GET" action="{{ route('admin.mas-upts.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by UPT name, code, river basin, or city..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <!-- River Basin Filter -->
                <div class="w-full md:w-48">
                    <select name="river_basin_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All River Basins</option>
                        @foreach($riverBasins as $riverBasin)
                            <option value="{{ $riverBasin->river_basins_code }}" {{ request('river_basin_code') == $riverBasin->river_basins_code ? 'selected' : '' }}>
                                {{ $riverBasin->river_basin_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- City Filter -->
                <div class="w-full md:w-48">
                    <select name="cities_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->cities_code }}" {{ request('cities_code') == $city->cities_code ? 'selected' : '' }}>
                                {{ $city->cities_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Search Button -->
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Search
                </button>
                
                <!-- Reset Button -->
                <a href="{{ route('admin.mas-upts.index') }}" class="w-full md:w-auto px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                    Reset
                </a>
            </form>
        </x-filter-bar>

        <!-- Table Section -->
        <x-table>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        UPTs List ({{ $upts->total() }} total)
                    </h2>
                    <a href="{{ route('admin.mas-upts.create') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add New UPT
                    </a>
                </div>
            </x-slot>

            <x-slot name="body">
                @if($upts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">UPT Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">UPT Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">River Basin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">UPTDs Count</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($upts as $upt)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $upt->upts_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $upt->upts_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $upt->riverBasin->river_basin_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $upt->city->cities_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $upt->uptds_count ?? 0 }} UPTDs
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('admin.mas-upts.show', $upt) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</a>
                                                <a href="{{ route('admin.mas-upts.edit', $upt) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</a>
                                                <form action="{{ route('admin.mas-upts.destroy', $upt) }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this UPT?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $upts->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-500 dark:text-gray-400">
                            <i class="fas fa-building text-4xl mb-4"></i>
                            <p class="text-lg font-medium">No UPTs found</p>
                            <p class="mt-2">Get started by creating a new UPT.</p>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('admin.mas-upts.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i>
                                Add New UPT
                            </a>
                        </div>
                    </div>
                @endif
            </x-slot>
        </x-table>
    </div>
@endsection