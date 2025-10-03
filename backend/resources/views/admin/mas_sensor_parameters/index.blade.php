@extends('admin.layouts.admin')

@section('title', 'Sensor Parameters Management')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Sensor Parameters Management</h1>
    <nav class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Dashboard</a>
        <span class="mx-2">/</span>
        <span>Sensor Parameters</span>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Filter Section -->
        <x-filter-bar>
            <form method="GET" action="{{ route('admin.mas-sensor-parameters.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by parameter name or code..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <!-- Search Button -->
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Search
                </button>
                
                <!-- Reset Button -->
                <a href="{{ route('admin.mas-sensor-parameters.index') }}" class="w-full md:w-auto px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                    Reset
                </a>
            </form>
        </x-filter-bar>

        <!-- Table Section -->
        <x-table>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Sensor Parameters List ({{ $sensorParameters->total() }} total)
                    </h2>
                    <a href="{{ route('admin.mas-sensor-parameters.create') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add New Parameter
                    </a>
                </div>
            </x-slot>

            <x-slot name="body">
                @if($sensorParameters->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Parameter Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Parameter Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($sensorParameters as $parameter)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $parameter->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $parameter->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $parameter->created_at ? $parameter->created_at->format('d M Y, H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('admin.mas-sensor-parameters.show', $parameter) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</a>
                                                <a href="{{ route('admin.mas-sensor-parameters.edit', $parameter) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</a>
                                                <form action="{{ route('admin.mas-sensor-parameters.destroy', $parameter) }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this sensor parameter?')">
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
                        {{ $sensorParameters->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-500 dark:text-gray-400">
                            <i class="fas fa-sliders-h text-4xl mb-4"></i>
                            <p class="text-lg font-medium">No sensor parameters found</p>
                            <p class="mt-2">Get started by creating a new sensor parameter.</p>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('admin.mas-sensor-parameters.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i>
                                Add New Parameter
                            </a>
                        </div>
                    </div>
                @endif
            </x-slot>
        </x-table>
    </div>
@endsection