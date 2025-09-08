@extends('layouts.auth')

@section('title', 'Register - PUSDA JATIM')

@push('styles')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .input-focus {
        transition: all 0.3s ease;
    }

    .input-focus:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .btn-hover {
        transition: all 0.3s ease;
    }

    .btn-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(71, 85, 105, 0.3);
    }
</style>
@endpush

@section('content')
<div class="font-sans antialiased gradient-bg min-h-screen">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px); background-size: 20px 20px;"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo Section -->
            <div class="text-center mb-4">
                <div class="mx-auto h-20 w-auto mb-4 flex items-center justify-center">
                    <img src="{{ asset('assets/images/PUSDAJATIM.png') }}" alt="PUSDA JATIM"
                        class="h-20 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Dinas PU Sumber Daya Air<br>Provinsi Jawa Timur</h1>
            </div>

            <!-- Register Card -->
            <div class="glass-effect rounded-2xl shadow-2xl p-6">
                <form class="space-y-3" action="{{ route('register') }}" method="POST">
                    @csrf

                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <input id="name" name="name" type="text" autocomplete="name" required
                                class="input-focus appearance-none relative block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent sm:text-sm @error('name') border-red-300 focus:ring-red-500 @enderror"
                                placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="input-focus appearance-none relative block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent sm:text-sm @error('email') border-red-300 focus:ring-red-500 @enderror"
                                placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                required
                                class="input-focus appearance-none relative block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent sm:text-sm @error('password') border-red-300 focus:ring-red-500 @enderror"
                                placeholder="Masukkan password Anda">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Password Confirmation Input -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                required
                                class="input-focus appearance-none relative block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent sm:text-sm @error('password_confirmation') border-red-300 focus:ring-red-500 @enderror"
                                placeholder="Konfirmasi password Anda">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 border border-red-200 p-3">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                                <div class="text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Register Button -->
                    <div>
                        <button type="submit" class="bg-gray-600 text-white rounded px-6 py-2 w-full transition duration-200 hover:bg-gray-700 hover:shadow-lg">
                            Daftar
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-medium text-gray-600 hover:text-gray-500">
                            Login sekarang
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-gray-500 text-xs">
                    © 2024 PUSDA JATIM. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection


