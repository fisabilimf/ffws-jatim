@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('page-header')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Profil Saya</h1>
    <p class="mt-2 text-sm text-gray-600">Kelola informasi profil dan akun Anda</p>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Profil Information -->
    <x-admin.card title="Informasi Profil" subtitle="Update informasi profil Anda">
        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-admin.form-input
                    label="Nama Lengkap"
                    name="name"
                    type="text"
                    :value="old('name', $user->name)"
                    required
                />
                
                <x-admin.form-input
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email', $user->email)"
                    required
                />
            </div>
            
            <x-admin.form-input
                label="Bio"
                name="bio"
                type="textarea"
                :value="old('bio', $user->bio ?? '')"
                placeholder="Ceritakan sedikit tentang diri Anda..."
                rows="3"
            />
            
            <div class="flex justify-end">
                <x-admin.button type="submit" variant="primary">
                    Update Profil
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <!-- Change Password -->
    <x-admin.card title="Ubah Password" subtitle="Update password akun Anda">
        <form action="{{ route('admin.profile.password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <x-admin.form-input
                label="Password Saat Ini"
                name="current_password"
                type="password"
                required
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-admin.form-input
                    label="Password Baru"
                    name="password"
                    type="password"
                    required
                />
                
                <x-admin.form-input
                    label="Konfirmasi Password Baru"
                    name="password_confirmation"
                    type="password"
                    required
                />
            </div>
            
            <div class="flex justify-end">
                <x-admin.button type="submit" variant="primary">
                    Update Password
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <!-- Account Information -->
    <x-admin.card title="Informasi Akun" subtitle="Detail akun Anda">
        <div class="space-y-4">
            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-500">Role</span>
                <span class="text-sm text-gray-900">{{ ucfirst($user->role ?? 'user') }}</span>
            </div>
            
            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-500">Status</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ ($user->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($user->status ?? 'active') }}
                </span>
            </div>
            
            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-500">Bergabung Sejak</span>
                <span class="text-sm text-gray-900">{{ $user->created_at ? $user->created_at->format('d M Y H:i') : 'N/A' }}</span>
            </div>
            
            <div class="flex justify-between items-center py-3">
                <span class="text-sm font-medium text-gray-500">Terakhir Update</span>
                <span class="text-sm text-gray-900">{{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : 'N/A' }}</span>
            </div>
        </div>
    </x-admin.card>
</div>
@endsection


