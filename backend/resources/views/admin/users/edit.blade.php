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
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" data-confirm-delete="Apakah Anda yakin ingin menghapus user ini?">
                        @csrf
                        @method('DELETE')
                        <x-admin.button type="submit" variant="danger">
                            <i class="fas fa-trash -ml-1 mr-2 h-5 w-5"></i>
                            Hapus User
                        </x-admin.button>
                    </form>
                </div>
                
                <div class="flex items-center space-x-3">
                    <x-admin.button href="{{ route('admin.users.index') }}" variant="outline">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" variant="primary">
                        <i class="fas fa-check -ml-1 mr-2 h-5 w-5"></i>
                        Update User
                    </x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection


