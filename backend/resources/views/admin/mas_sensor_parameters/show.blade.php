@extends('layouts.admin')

@section('title', 'Sensor Parameter Details')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Sensor Parameter Details</h1>
    <nav class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.mas-sensor-parameters.index') }}" class="hover:text-gray-900 dark:hover:text-white">Sensor Parameters</a>
        <span class="mx-2">/</span>
        <span>Details</span>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Sensor Parameter Information</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.mas-sensor-parameters.edit', $masSensorParameter) }}" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Edit
                    </a>
                    <form action="{{ route('admin.mas-sensor-parameters.destroy', $masSensorParameter) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this sensor parameter?')">
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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parameter Code</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $masSensorParameter->code }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parameter Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $masSensorParameter->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $masSensorParameter->created_at ? $masSensorParameter->created_at->format('d M Y, H:i') : 'N/A' }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $masSensorParameter->updated_at ? $masSensorParameter->updated_at->format('d M Y, H:i') : 'N/A' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.mas-sensor-parameters.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to Sensor Parameters
            </a>
        </div>
    </div>
@endsection