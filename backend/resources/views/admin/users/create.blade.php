@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
@section('page-description', 'Buat user baru untuk sistem')
@section('breadcrumb', 'Tambah User')

@section('content')
<div class="max-w-auto mx-auto">
    <x-admin.card title="Form User Baru" subtitle="Isi informasi user yang akan dibuat">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <x-admin.form-input
                    type="text"
                    name="name"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    required="true"
                    :error="$errors->first('name')"
                />
                
                <!-- Email -->
                <x-admin.form-input
                    type="email"
                    name="email"
                    label="Email"
                    placeholder="user@example.com"
                    required="true"
                    :error="$errors->first('email')"
                />
                
                <!-- Password -->
                <x-admin.form-input
                    type="password"
                    name="password"
                    label="Password"
                    placeholder="Minimal 8 karakter"
                    required="true"
                    :error="$errors->first('password')"
                />
                
                <!-- Password Confirmation -->
                <x-admin.form-input
                    type="password"
                    name="password_confirmation"
                    label="Konfirmasi Password"
                    placeholder="Ulangi password"
                    required="true"
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
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderator</option>
                </x-admin.form-input>
                
                <!-- Status -->
                <x-admin.form-input
                    type="select"
                    name="status"
                    label="Status"
                    required="true"
                    :error="$errors->first('status')"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </x-admin.form-input>
            </div>
            
            <!-- Bio -->
            <x-admin.form-input
                type="textarea"
                name="bio"
                label="Bio"
                placeholder="Ceritakan sedikit tentang user ini..."
                :error="$errors->first('bio')"
            />
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <x-admin.button href="{{ route('admin.users.index') }}" variant="outline">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit" variant="primary">
                    <i class="fas fa-check -ml-1 mr-2 "></i>
                    Simpan User
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection


