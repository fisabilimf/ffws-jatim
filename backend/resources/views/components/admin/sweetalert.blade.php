@props([
    // Opsi default tombol dan teks; bisa di-override via slot/script jika perlu
    'bind' => true, // sisakan true agar auto-binding aktif dari admin.js
])

@once
    @push('styles')
        <style>[x-cloak]{ display:none !important; }</style>
    @endpush
@endonce

{{-- Flash Messages - Now using SweetAlert2 Toast --}}
@if(session('success'))
    <div x-data x-init="window.AdminUtils?.toastSuccess('{{ addslashes(session('success')) }}')"></div>
@endif

@if(session('error'))
    <div x-data x-init="window.AdminUtils?.toastError('{{ addslashes(session('error')) }}')"></div>
@endif

@if(session('warning'))
    <div x-data x-init="window.AdminUtils?.toastWarning('{{ addslashes(session('warning')) }}')"></div>
@endif

@if(session('info'))
    <div x-data x-init="window.AdminUtils?.toastInfo('{{ addslashes(session('info')) }}')"></div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div x-data x-init="window.AdminUtils?.toastError('Ada kesalahan pada input. Periksa kembali.')"></div>
@endif

{{-- SweetAlert2 Toast Functions --}}
<script>
// Pastikan AdminUtils tersedia untuk toast functions
document.addEventListener('DOMContentLoaded', function() {
    // Tunggu hingga AdminUtils tersedia
    const checkAdminUtils = () => {
        if (window.AdminUtils && window.AdminUtils.toastSuccess) {
            // AdminUtils sudah tersedia, tidak perlu melakukan apa-apa
            return;
        }
        
        // Jika belum tersedia, tunggu sebentar dan coba lagi
        setTimeout(checkAdminUtils, 50);
    };
    
    checkAdminUtils();
});

// Helper functions untuk toast yang bisa dipanggil dari mana saja
window.showToast = {
    success: function(message, options = {}) {
        if (window.AdminUtils?.toastSuccess) {
            return window.AdminUtils.toastSuccess(message, options);
        } else {
            console.warn('AdminUtils.toastSuccess not available');
        }
    },
    
    error: function(message, options = {}) {
        if (window.AdminUtils?.toastError) {
            return window.AdminUtils.toastError(message, options);
        } else {
            console.warn('AdminUtils.toastError not available');
        }
    },
    
    warning: function(message, options = {}) {
        if (window.AdminUtils?.toastWarning) {
            return window.AdminUtils.toastWarning(message, options);
        } else {
            console.warn('AdminUtils.toastWarning not available');
        }
    },
    
    info: function(message, options = {}) {
        if (window.AdminUtils?.toastInfo) {
            return window.AdminUtils.toastInfo(message, options);
        } else {
            console.warn('AdminUtils.toastInfo not available');
        }
    }
};
</script>

{{-- Komponen ini tidak perlu output HTML khusus. --}}
{{-- Cukup pastikan admin.js (yang memuat SweetAlert2 dan autobind) sudah diload. --}}
{{-- Cara pakai di view:
    <x-admin.sweetalert />
    <a href="..." data-confirm>Hapus</a>
    <button data-confirm-delete>Hapus</button>
    <form method="POST" ... data-confirm-save>...</form>
    
    // Untuk toast:
    <button onclick="showToast.success('Data berhasil disimpan!')">Test Success</button>
    <button onclick="showToast.error('Terjadi kesalahan!')">Test Error</button>
    <button onclick="showToast.warning('Perhatian!')">Test Warning</button>
    <button onclick="showToast.info('Informasi penting')">Test Info</button>
--}}


