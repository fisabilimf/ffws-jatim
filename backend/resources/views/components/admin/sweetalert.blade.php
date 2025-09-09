@props([
    // Opsi default tombol dan teks; bisa di-override via slot/script jika perlu
    'bind' => true, // sisakan true agar auto-binding aktif dari admin.js
])

@once
    @push('styles')
        <style>[x-cloak]{ display:none !important; }</style>
    @endpush
@endonce

{{-- Komponen ini tidak perlu output HTML khusus. --}}
{{-- Cukup pastikan admin.js (yang memuat SweetAlert2 dan autobind) sudah diload. --}}
{{-- Cara pakai di view:
    <x-admin.sweetalert />
    <a href="..." data-confirm>Hapus</a>
    <button data-confirm-delete>Hapus</button>
    <form method="POST" ... data-confirm-save>...</form>
--}}


