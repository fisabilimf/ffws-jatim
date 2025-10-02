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
    <div x-data x-init="
        const errorMessage = '{{ addslashes(session('error')) }}';
        if (errorMessage.length > 80) {
            // For longer error messages, use error detail format
            const title = errorMessage.includes('foreign key constraint') || errorMessage.includes('tidak dapat dihapus') 
                ? 'Tidak Dapat Menghapus Data' 
                : 'Terjadi Kesalahan';
            window.AdminUtils?.toastErrorDetail(title, errorMessage);
        } else {
            window.AdminUtils?.toastError(errorMessage);
        }
    "></div>
@endif

@if(session('warning'))
    <div x-data x-init="window.AdminUtils?.toastWarning('{{ addslashes(session('warning')) }}')"></div>
@endif

@if(session('info'))
    <div x-data x-init="window.AdminUtils?.toastInfo('{{ addslashes(session('info')) }}')"></div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div x-data x-init="
        const errorCount = {{ $errors->count() }};
        const firstError = '{{ addslashes($errors->first()) }}';
        if (errorCount === 1) {
            window.AdminUtils?.toastError(firstError);
        } else {
            window.AdminUtils?.toastErrorDetail('Validasi Gagal', `Ada ${errorCount} kesalahan pada input. Periksa kembali form.`);
        }
    "></div>
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
</script>