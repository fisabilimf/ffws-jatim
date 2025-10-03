@extends('layouts.admin')

@section('title', 'Detail Desa')
@section('page-title', 'Detail Desa')
@section('page-description', 'Informasi detail desa')
@section('breadcrumb', 'Villages')

@section('content')
<div class="space-y-6">
    <!-- Village Information -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900">Informasi Desa</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">ID</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masVillage->id }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Kode Desa</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masVillage->villages_code }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Nama Desa</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masVillage->villages_name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Dibuat Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masVillage->created_at ? $masVillage->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500">Diperbarui Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $masVillage->updated_at ? $masVillage->updated_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.mas-villages.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
            <a href="{{ route('admin.mas-villages.edit', $masVillage) }}" 
               class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit
            </a>
        </div>
    </div>
</div>
@endsection