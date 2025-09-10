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
    </div>

    <!-- Users Table -->
    <x-table
        title="Daftar Users"
        :headers="$tableHeaders"
        :rows="$users"
        searchable
        :pagination="$users->links()"
    >
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah User
            </a>
        </x-slot:actions>
    </x-table>
</div>
@endsection


