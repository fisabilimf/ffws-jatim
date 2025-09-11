@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('page-description', 'Kelola pengaturan umum sistem')
@section('breadcrumb', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- General Settings -->
    <x-admin.card title="Pengaturan Umum" subtitle="Pengaturan dasar sistem">
        <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-6" data-confirm-save>
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- App Name -->
                <x-admin.form-input
                    type="text"
                    name="app_name"
                    label="Nama Aplikasi"
                    :value="config('app.name')"
                    placeholder="Nama aplikasi Anda"
                    required="true"
                    :error="$errors->first('app_name')"
                />
                
                <!-- App URL -->
                <x-admin.form-input
                    type="url"
                    name="app_url"
                    label="URL Aplikasi"
                    :value="config('app.url')"
                    placeholder="https://example.com"
                    required="true"
                    :error="$errors->first('app_url')"
                />
                
                <!-- Timezone -->
                <x-admin.form-input
                    type="select"
                    name="timezone"
                    label="Timezone"
                    required="true"
                    :error="$errors->first('timezone')"
                >
                    <option value="Asia/Jakarta" {{ config('app.timezone') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar" {{ config('app.timezone') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura" {{ config('app.timezone') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                    <option value="UTC" {{ config('app.timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                </x-admin.form-input>
                
                <!-- Locale -->
                <x-admin.form-input
                    type="select"
                    name="locale"
                    label="Bahasa Default"
                    required="true"
                    :error="$errors->first('locale')"
                >
                    <option value="id" {{ config('app.locale') === 'id' ? 'selected' : '' }}>Indonesia</option>
                    <option value="en" {{ config('app.locale') === 'en' ? 'selected' : '' }}>English</option>
                </x-admin.form-input>
            </div>
            
            <!-- App Description -->
            <x-admin.form-input
                type="textarea"
                name="app_description"
                label="Deskripsi Aplikasi"
                :value="config('app.description', '')"
                placeholder="Deskripsi singkat tentang aplikasi Anda"
                :error="$errors->first('app_description')"
            />
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                <x-admin.button type="submit" variant="primary">
                    <i class="fas fa-check -ml-1 mr-2"></i>
                    Simpan Pengaturan
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <!-- Email Settings -->
    <x-admin.card title="Pengaturan Email" subtitle="Konfigurasi email sistem">
        <form action="{{ route('admin.settings.email.update') }}" method="POST" class="space-y-6" data-confirm-save>
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mail Driver -->
                <x-admin.form-input
                    type="select"
                    name="mail_driver"
                    label="Driver Email"
                    required="true"
                    :error="$errors->first('mail_driver')"
                >
                    <option value="smtp" {{ config('mail.default') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="mailgun" {{ config('mail.default') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                    <option value="ses" {{ config('mail.default') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    <option value="log" {{ config('mail.default') === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                </x-admin.form-input>
                
                <!-- Mail Host -->
                <x-admin.form-input
                    type="text"
                    name="mail_host"
                    label="Host SMTP"
                    :value="config('mail.mailers.smtp.host')"
                    placeholder="smtp.gmail.com"
                    :error="$errors->first('mail_host')"
                />
                
                <!-- Mail Port -->
                <x-admin.form-input
                    type="number"
                    name="mail_port"
                    label="Port SMTP"
                    :value="config('mail.mailers.smtp.port')"
                    placeholder="587"
                    :error="$errors->first('mail_port')"
                />
                
                <!-- Mail Username -->
                <x-admin.form-input
                    type="text"
                    name="mail_username"
                    label="Username Email"
                    :value="config('mail.mailers.smtp.username')"
                    placeholder="user@example.com"
                    :error="$errors->first('mail_username')"
                />
                
                <!-- Mail Password -->
                <x-admin.form-input
                    type="password"
                    name="mail_password"
                    label="Password Email"
                    placeholder="Password email Anda"
                    :error="$errors->first('mail_password')"
                />
                
                <!-- Mail Encryption -->
                <x-admin.form-input
                    type="select"
                    name="mail_encryption"
                    label="Enkripsi"
                    required="true"
                    :error="$errors->first('mail_encryption')"
                >
                    <option value="tls" {{ config('mail.mailers.smtp.encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ config('mail.mailers.smtp.encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ config('mail.mailers.smtp.encryption') === null ? 'selected' : '' }}>None</option>
                </x-admin.form-input>
            </div>
            
            <!-- From Address -->
            <x-admin.form-input
                type="email"
                name="mail_from_address"
                label="Alamat Email Pengirim"
                :value="config('mail.from.address')"
                placeholder="noreply@example.com"
                required="true"
                :error="$errors->first('mail_from_address')"
            />
            
            <!-- From Name -->
            <x-admin.form-input
                type="text"
                name="mail_from_name"
                label="Nama Pengirim"
                :value="config('mail.from.name')"
                placeholder="Nama Aplikasi"
                required="true"
                :error="$errors->first('mail_from_name')"
            />
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                <x-admin.button type="submit" variant="primary">
                    <i class="fas fa-check -ml-1 mr-2"></i>
                    Simpan Pengaturan Email
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <!-- System Information -->
    <x-admin.card title="Informasi Sistem" subtitle="Informasi teknis sistem">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Versi PHP</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ PHP_VERSION }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Versi Laravel</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ app()->version() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Environment</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('app.env') }}</dd>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Database</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.default') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Cache Driver</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('cache.default') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Session Driver</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('session.driver') }}</dd>
                </div>
            </div>
        </div>
        
        <!-- System Actions -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="inline" data-confirm="Jalankan perintah clear cache?">
                    @csrf
                    <x-admin.button type="submit" variant="secondary">
                        <i class="fas fa-sync-alt -ml-1 mr-2"></i>
                        Clear Cache
                    </x-admin.button>
                </form>
                
                <form action="{{ route('admin.settings.config.clear') }}" method="POST" class="inline" data-confirm="Jalankan perintah clear config cache?">
                    @csrf
                    <x-admin.button type="submit" variant="secondary">
                        <i class="fa-solid fa-gears -ml-1 mr-2"></i>
                        Clear Config Cache
                    </x-admin.button>
                </form>
            </div>
        </div>
    </x-admin.card>
</div>
@endsection


