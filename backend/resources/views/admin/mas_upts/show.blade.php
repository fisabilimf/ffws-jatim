@extends('admin.layouts.admin')

@section('title', 'UPT Details')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">UPT Details</h1>
    <nav class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.mas-upts.index') }}" class="hover:text-gray-900 dark:hover:text-white">UPTs</a>
        <span class="mx-2">/</span>
        <span>Details</span>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- UPT Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">UPT Information</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.mas-upts.edit', $masUpt) }}" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Edit
                    </a>
                    <form action="{{ route('admin.mas-upts.destroy', $masUpt) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this UPT?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">UPT Code</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $masUpt->upts_code }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">UPT Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $masUpt->upts_name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">River Basin</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            <span class="inline-flex items-center">
                                {{ $masUpt->riverBasin->river_basin_name ?? 'N/A' }}
                                @if($masUpt->riverBasin)
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $masUpt->riverBasin->river_basins_code }})</span>
                                @endif
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">City</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            <span class="inline-flex items-center">
                                {{ $masUpt->city->cities_name ?? 'N/A' }}
                                @if($masUpt->city)
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $masUpt->city->cities_code }})</span>
                                @endif
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $masUpt->created_at ? $masUpt->created_at->format('d M Y, H:i') : 'N/A' }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $masUpt->updated_at ? $masUpt->updated_at->format('d M Y, H:i') : 'N/A' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Related UPTDs -->
        @if($masUpt->uptds && $masUpt->uptds->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Related UPTDs ({{ $masUpt->uptds->count() }})</h3>
                </div>
                
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">UPTD Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">UPTD Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($masUpt->uptds as $uptd)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $uptd->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $uptd->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $uptd->created_at ? $uptd->created_at->format('d M Y, H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <div class="flex justify-between">
            <a href="{{ route('admin.mas-upts.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to UPTs
            </a>
        </div>
    </div>
@endsection