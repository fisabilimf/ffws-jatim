@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-description', 'Edit informasi user')
@section('breadcrumb', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-admin.card title="Form Edit User" subtitle="Edit informasi user yang dipilih">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <x-admin.form-input
                    type="text"
                    name="name"
                    label="Nama Lengkap"
                    :value="$user->name"
                    placeholder="Masukkan nama lengkap"
                    required="true"
                    :error="$errors->first('name')"
                />
                
                <!-- Email -->
                <x-admin.form-input
                    type="email"
                    name="email"
                    label="Email"
                    :value="$user->email"
                    placeholder="user@example.com"
                    required="true"
                    :error="$errors->first('email')"
                />
                
                <!-- Password -->
                <x-admin.form-input
                    type="password"
                    name="password"
                    label="Password Baru"
                    placeholder="Kosongkan jika tidak ingin mengubah"
                    :error="$errors->first('password')"
                    help="Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password."
                />
                
                <!-- Password Confirmation -->
                <x-admin.form-input
                    type="password"
                    name="password_confirmation"
                    label="Konfirmasi Password Baru"
                    placeholder="Ulangi password baru"
                    :error="$errors->first('password_confirmation')"
                />
                
                <!-- Role -->
                <x-admin.form-input
                    type="select"
                    name="role"
                    label="Role"
                    required="true"
                    :error="$errors->first('role')"
                >
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>Moderator</option>
                </x-admin.form-input>
                
                <!-- Status -->
                <x-admin.form-input
                    type="select"
                    name="status"
                    label="Status"
                    required="true"
                    :error="$errors->first('status')"
                >
                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Pending</option>
                </x-admin.form-input>
            </div>
            
            <!-- Bio -->
            <x-admin.form-input
                type="textarea"
                name="bio"
                label="Bio"
                :value="$user->bio"
                placeholder="Ceritakan sedikit tentang user ini..."
                :error="$errors->first('bio')"
            />
            
            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <x-admin.button type="submit" variant="danger">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus User
                        </x-admin.button>
                    </form>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <x-admin.button type="submit" variant="primary">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update User
                    </x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection


