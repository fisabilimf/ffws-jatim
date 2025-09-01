@extends('layouts.admin')

@section('title', 'Manajemen Users')
@section('page-title', 'Manajemen Users')
@section('page-description', 'Kelola semua user dalam sistem')
@section('breadcrumb', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Daftar Users</h2>
            <p class="mt-1 text-sm text-gray-500">Kelola semua user dalam sistem</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah User
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <x-admin.table 
        :headers="$tableHeaders" 
        :rows="$users" 
        :sortable="true"
        :sort-column="$sortColumn"
        :sort-direction="$sortDirection"
        :searchable="true"
        :search-query="$searchQuery"
        :pagination="$users->links()"
    />
</div>
@endsection


