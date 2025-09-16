// Admin Panel JavaScript
import Alpine from 'alpinejs'
import Swal from 'sweetalert2'

// Make Alpine available globally
window.Alpine = Alpine

// Initialize AdminUtils early to prevent race conditions
window.AdminUtils = window.AdminUtils || {}

// SweetAlert2 global helpers
window.Swal = Swal

// Toast helpers - Define early to prevent race conditions
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    iconColor: 'white',
    customClass: {
        popup: 'colored-toast',
    },
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
})

// Initialize toast functions immediately
window.AdminUtils.toast = function(type = 'info', message = '', options = {}) {
    const icon = ['success','error','warning','info','question'].includes(type) ? type : 'info'
    
    // Jika message panjang, gunakan html untuk formatting yang lebih baik
    const toastOptions = {
        icon,
        ...options
    }
    
    if (message && message.length > 50) {
        toastOptions.html = `<div style="font-weight: 600; margin-bottom: 4px;">${this.getDefaultTitle(type)}</div><div style="font-size: 14px; font-weight: 400;">${message}</div>`
    } else {
        toastOptions.title = message || this.getDefaultTitle(type)
    }
    
    return Toast.fire(toastOptions)
}

// Helper function untuk default title
window.AdminUtils.getDefaultTitle = function(type) {
    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Info',
        question: 'Question'
    }
    return titles[type] || 'Info'
}

window.AdminUtils.toastSuccess = function(message, options = {}) {
    return window.AdminUtils.toast('success', message, options)
}

window.AdminUtils.toastError = function(message, options = {}) {
    // Log error ke console untuk debugging
    console.error('Toast Error:', message)
    return window.AdminUtils.toast('error', message, options)
}

window.AdminUtils.toastWarning = function(message, options = {}) {
    return window.AdminUtils.toast('warning', message, options)
}

window.AdminUtils.toastInfo = function(message, options = {}) {
    return window.AdminUtils.toast('info', message, options)
}

// Fungsi khusus untuk error dengan detail
window.AdminUtils.toastErrorDetail = function(title, message, options = {}) {
    console.error('Toast Error Detail:', { title, message })
    return Toast.fire({
        icon: 'error',
        html: `<div style="font-weight: 600; margin-bottom: 4px;">${title}</div><div style="font-size: 14px; font-weight: 400;">${message}</div>`,
        ...options
    })
}

// Fungsi khusus untuk info dengan detail
window.AdminUtils.toastInfoDetail = function(title, message, options = {}) {
    return Toast.fire({
        icon: 'info',
        html: `<div style="font-weight: 600; margin-bottom: 4px;">${title}</div><div style="font-size: 14px; font-weight: 400;">${message}</div>`,
        ...options
    })
}

// Global helper functions (simplified)
window.showToast = {
    success: (message, options = {}) => window.AdminUtils.toastSuccess(message, options),
    error: (message, options = {}) => window.AdminUtils.toastError(message, options),
    warning: (message, options = {}) => window.AdminUtils.toastWarning(message, options),
    info: (message, options = {}) => window.AdminUtils.toastInfo(message, options),
    errorDetail: (title, message, options = {}) => window.AdminUtils.toastErrorDetail(title, message, options),
    infoDetail: (title, message, options = {}) => window.AdminUtils.toastInfoDetail(title, message, options)
};

// Sistem Notifikasi Komprehensif untuk Berbagai Skenario
window.NotificationSystem = {
    // CRUD Operations
    create: {
        success: (itemName = 'Data') => window.AdminUtils.toastSuccess(`${itemName} berhasil ditambahkan!`),
        error: (itemName = 'Data', error = '') => {
            const message = error ? `${itemName} gagal ditambahkan. ${error}` : `${itemName} gagal ditambahkan. Silakan coba lagi.`;
            return window.AdminUtils.toastErrorDetail('Gagal Menambah Data', message);
        },
        validation: (errors = []) => {
            const errorList = Array.isArray(errors) ? errors.join(', ') : errors;
            return window.AdminUtils.toastErrorDetail('Validasi Gagal', `Periksa kembali input Anda: ${errorList}`);
        }
    },
    
    update: {
        success: (itemName = 'Data') => window.AdminUtils.toastSuccess(`${itemName} berhasil diperbarui!`),
        error: (itemName = 'Data', error = '') => {
            const message = error ? `${itemName} gagal diperbarui. ${error}` : `${itemName} gagal diperbarui. Silakan coba lagi.`;
            return window.AdminUtils.toastErrorDetail('Gagal Memperbarui Data', message);
        },
        notFound: (itemName = 'Data') => window.AdminUtils.toastErrorDetail('Data Tidak Ditemukan', `${itemName} yang akan diperbarui tidak ditemukan.`)
    },
    
    delete: {
        success: (itemName = 'Data') => window.AdminUtils.toastSuccess(`${itemName} berhasil dihapus!`),
        error: (itemName = 'Data', error = '') => {
            const message = error ? `${itemName} gagal dihapus. ${error}` : `${itemName} gagal dihapus. Silakan coba lagi.`;
            return window.AdminUtils.toastErrorDetail('Gagal Menghapus Data', message);
        },
        notFound: (itemName = 'Data') => window.AdminUtils.toastErrorDetail('Data Tidak Ditemukan', `${itemName} yang akan dihapus tidak ditemukan.`),
        confirm: (itemName = 'Data') => window.AdminUtils.confirmDelete(`Apakah Anda yakin ingin menghapus ${itemName}? Data yang dihapus tidak dapat dikembalikan.`)
    },
    
    // Authentication & Authorization
    auth: {
        loginSuccess: (userName = '') => {
            const message = userName ? `Selamat datang kembali, ${userName}!` : 'Login berhasil!';
            return window.AdminUtils.toastSuccess(message);
        },
        loginError: () => window.AdminUtils.toastErrorDetail('Login Gagal', 'Email atau password salah. Silakan coba lagi.'),
        logoutSuccess: () => window.AdminUtils.toastInfo('Anda telah berhasil logout.'),
        unauthorized: () => window.AdminUtils.toastErrorDetail('Akses Ditolak', 'Anda tidak memiliki izin untuk melakukan aksi ini.'),
        sessionExpired: () => window.AdminUtils.toastWarning('Sesi Anda telah berakhir. Silakan login kembali.')
    },
    
    // File Operations
    file: {
        uploadSuccess: (fileName = 'File') => window.AdminUtils.toastSuccess(`${fileName} berhasil diunggah!`),
        uploadError: (fileName = 'File', error = '') => {
            const message = error ? `${fileName} gagal diunggah. ${error}` : `${fileName} gagal diunggah.`;
            return window.AdminUtils.toastErrorDetail('Upload Gagal', message);
        },
        downloadSuccess: (fileName = 'File') => window.AdminUtils.toastInfo(`${fileName} berhasil diunduh.`),
        downloadError: (fileName = 'File') => window.AdminUtils.toastErrorDetail('Download Gagal', `${fileName} gagal diunduh.`),
        deleteSuccess: (fileName = 'File') => window.AdminUtils.toastSuccess(`${fileName} berhasil dihapus!`),
        deleteError: (fileName = 'File') => window.AdminUtils.toastErrorDetail('Hapus File Gagal', `${fileName} gagal dihapus.`)
    },
    
    // System & Network
    system: {
        maintenance: (time = '') => {
            const message = time ? `Sistem akan maintenance pada ${time}.` : 'Sistem akan melakukan maintenance.';
            return window.AdminUtils.toastInfoDetail('Maintenance', message);
        },
        networkError: () => window.AdminUtils.toastErrorDetail('Koneksi Gagal', 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.'),
        serverError: () => window.AdminUtils.toastErrorDetail('Server Error', 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'),
        timeout: () => window.AdminUtils.toastErrorDetail('Timeout', 'Permintaan terlalu lama. Silakan coba lagi.')
    },
    
    // Data & Import/Export
    data: {
        importSuccess: (count = 0) => window.AdminUtils.toastSuccess(`${count} data berhasil diimpor!`),
        importError: (error = '') => {
            const message = error || 'Data gagal diimpor. Periksa format file.';
            return window.AdminUtils.toastErrorDetail('Import Gagal', message);
        },
        exportSuccess: (fileName = 'Data') => window.AdminUtils.toastSuccess(`${fileName} berhasil diekspor!`),
        exportError: () => window.AdminUtils.toastErrorDetail('Export Gagal', 'Data gagal diekspor. Silakan coba lagi.'),
        duplicate: (itemName = 'Data') => window.AdminUtils.toastWarning(`${itemName} sudah ada. Silakan gunakan data yang berbeda.`)
    },
    
    // Form & Validation
    form: {
        saveSuccess: () => window.AdminUtils.toastSuccess('Form berhasil disimpan!'),
        saveError: () => window.AdminUtils.toastError('Form gagal disimpan. Silakan coba lagi.'),
        resetSuccess: () => window.AdminUtils.toastInfo('Form telah direset.'),
        required: (fieldName = 'Field') => window.AdminUtils.toastWarning(`${fieldName} wajib diisi.`),
        invalid: (fieldName = 'Field') => window.AdminUtils.toastWarning(`Format ${fieldName} tidak valid.`)
    },
    
    // Custom Messages
    custom: {
        success: (title, message) => window.AdminUtils.toastSuccess(message),
        error: (title, message) => window.AdminUtils.toastErrorDetail(title, message),
        warning: (title, message) => window.AdminUtils.toastWarning(message),
        info: (title, message) => window.AdminUtils.toastInfoDetail(title, message)
    }
};

// Alpine Store for Sidebar
Alpine.store('sidebar', {
    open: window.innerWidth >= 1024,
    
    toggle() {
        this.open = !this.open;
    },
    
    close() {
        this.open = false;
    },
    
    openSidebar() {
        this.open = true;
    },
    
    init() {
        // Set initial state based on screen size
        this.open = window.innerWidth >= 1024;
        
        // Listen for window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                this.open = true;
            }
        });
    }
})

// Admin Panel Components
Alpine.data('adminPanel', () => ({
    // Sidebar state
    sidebarOpen: true,
    
    // Mobile sidebar toggle
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen
    },
    
    // Close sidebar on mobile
    closeSidebar() {
        if (window.innerWidth < 1024) {
            this.sidebarOpen = false
        }
    },
    
    // Initialize
    init() {
        // Listen for window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                this.sidebarOpen = true
            }
        })
        
        // Listen for custom events
        this.$watch('sidebarOpen', (value) => {
            if (value) {
                document.body.classList.remove('sidebar-closed')
            } else {
                document.body.classList.add('sidebar-closed')
            }
        })
    }
}))

// Table component functionality
Alpine.data('adminTable', () => ({
    // Search functionality
    searchQuery: '',
    filteredRows: [],
    
    // Initialize search
    init() {
        this.searchQuery = this.$el.querySelector('input[name="search"]')?.value || ''
        this.filterRows()
    },
    
    // Filter rows based on search
    filterRows() {
        const searchTerm = this.searchQuery.toLowerCase()
        const rows = this.$el.querySelectorAll('tbody tr')
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase()
            if (text.includes(searchTerm)) {
                row.style.display = ''
            } else {
                row.style.display = 'none'
            }
        })
    },
    
    // Handle search input
    handleSearch() {
        this.filterRows()
    }
}))

// Form validation
Alpine.data('adminForm', () => ({
    // Form state
    isSubmitting: false,
    errors: {},
    
    // Submit form
    async submitForm(event) {
        this.isSubmitting = true
        
        try {
            const form = event.target
            const formData = new FormData(form)
            
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            
            if (response.ok) {
                const result = await response.json()
                
                if (result.success) {
                    // Show success message
                    this.showNotification(result.message, 'success')
                    
                    // Redirect if specified
                    if (result.redirect) {
                        window.location.href = result.redirect
                    }
                } else {
                    // Show error message
                    this.showNotification(result.message, 'error')
                    this.errors = result.errors || {}
                }
            } else {
                throw new Error('Network response was not ok')
            }
        } catch (error) {
            console.error('Form submission error:', error)
            this.showNotification('Terjadi kesalahan saat mengirim form', 'error')
        } finally {
            this.isSubmitting = false
        }
    },
    
    // Show notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div')
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`
        notification.textContent = message
        
        // Add to page
        document.body.appendChild(notification)
        
        // Remove after 5 seconds
        setTimeout(() => {
            notification.remove()
        }, 5000)
    }
}))

// Modal component
Alpine.data('adminModal', () => ({
    // Modal state
    isOpen: false,
    
    // Open modal
    open() {
        this.isOpen = true
        document.body.style.overflow = 'hidden'
    },
    
    // Close modal
    close() {
        this.isOpen = false
        document.body.style.overflow = ''
    },
    
    // Close on escape key
    handleEscape(event) {
        if (event.key === 'Escape') {
            this.close()
        }
    },
    
    // Close on backdrop click
    handleBackdropClick(event) {
        if (event.target === event.currentTarget) {
            this.close()
        }
    },
    
    // Initialize
    init() {
        // Listen for escape key
        document.addEventListener('keydown', this.handleEscape.bind(this))
    }
}))

// Dropdown component
Alpine.data('adminDropdown', () => ({
    // Dropdown state
    isOpen: false,
    
    // Toggle dropdown
    toggle() {
        this.isOpen = !this.isOpen
    },
    
    // Close dropdown
    close() {
        this.isOpen = false
    },
    
    // Close on outside click
    handleOutsideClick(event) {
        if (!this.$el.contains(event.target)) {
            this.close()
        }
    },
    
    // Initialize
    init() {
        // Listen for outside clicks
        document.addEventListener('click', this.handleOutsideClick.bind(this))
    }
}))

// Data table functionality
Alpine.data('adminDataTable', () => ({
    // Table state
    sortColumn: null,
    sortDirection: 'asc',
    currentPage: 1,
    itemsPerPage: 10,
    searchQuery: '',
    
    // Sort table
    sort(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
        } else {
            this.sortColumn = column
            this.sortDirection = 'asc'
        }
        
        // Update URL
        const url = new URL(window.location)
        url.searchParams.set('sort', column)
        url.searchParams.set('direction', this.sortDirection)
        window.history.pushState({}, '', url)
    },
    
    // Search table
    search() {
        this.currentPage = 1
        // Implement search logic here
    },
    
    // Change page
    changePage(page) {
        this.currentPage = page
        // Implement pagination logic here
    },
    
    // Change items per page
    changeItemsPerPage(items) {
        this.itemsPerPage = items
        this.currentPage = 1
        // Implement pagination logic here
    }
}))

// Utility functions - Extend AdminUtils
Object.assign(window.AdminUtils, {
    // Format date
    formatDate(date, format = 'DD/MM/YYYY HH:mm') {
        if (!date) return '-'
        
        const d = new Date(date)
        if (isNaN(d.getTime())) return '-'
        
        // Simple date formatting (you can use a library like moment.js for more features)
        const day = String(d.getDate()).padStart(2, '0')
        const month = String(d.getMonth() + 1).padStart(2, '0')
        const year = d.getFullYear()
        const hours = String(d.getHours()).padStart(2, '0')
        const minutes = String(d.getMinutes()).padStart(2, '0')
        
        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year)
            .replace('HH', hours)
            .replace('mm', minutes)
    },
    
    // Format number
    formatNumber(number, decimals = 0) {
        if (isNaN(number)) return '0'
        return Number(number).toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        })
    },
    
    // Format currency
    formatCurrency(amount, currency = 'IDR') {
        if (isNaN(amount)) return 'Rp 0'
        return `${currency} ${this.formatNumber(amount, 0)}`
    },
    
    // Confirm action
    confirm(message = 'Apakah Anda yakin?') {
        return Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(result => result.isConfirmed)
    },

    confirmDelete(message = 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?') {
        return Swal.fire({
            title: 'Hapus Data?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(result => result.isConfirmed)
    },

    confirmSave(message = 'Simpan perubahan?') {
        return Swal.fire({
            title: 'Simpan Perubahan',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(result => result.isConfirmed)
    },

    confirmLogout(message = 'Yakin ingin keluar dari sistem?') {
        return Swal.fire({
            title: 'Keluar?',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(result => result.isConfirmed)
    },
    
    // Show loading state
    showLoading(element) {
        if (element) {
            element.disabled = true
            element.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...'
        }
    },
    
    // Hide loading state
    hideLoading(element, originalText) {
        if (element) {
            element.disabled = false
            element.innerHTML = originalText
        }
    }
})

// Start Alpine
Alpine.start()

// Lepas kelas no-transitions setelah halaman selesai load
window.addEventListener('load', () => {
    setTimeout(() => {
        if (document && document.body) {
            document.body.classList.remove('no-transitions')
        }
    }, 0)
})

// Export for use in other modules
export default Alpine

// Auto-bind confirm handlers for elements using data attributes
//
// Usage examples:
// <a href="..." data-confirm>...</a>
// <button type="submit" form="formId" data-confirm-delete>Hapus</button>
// <form ... data-confirm-save>...</form>
;
(function initSweetAlertAutoBinding() {
    if (window.__sweetalertAutoBound) return
    window.__sweetalertAutoBound = true

    // Click handler for elements with data-confirm* attributes
    document.addEventListener('click', async (event) => {
        const target = event.target.closest('[data-confirm],[data-confirm-delete],[data-confirm-save],[data-confirm-logout]')
        if (!target) return

        // Determine action type and message
        let confirmed = false
        if (target.hasAttribute('data-confirm-delete')) {
            const msg = target.getAttribute('data-confirm-delete') || 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?'
            confirmed = await window.AdminUtils.confirmDelete(msg)
        } else if (target.hasAttribute('data-confirm-save')) {
            const msg = target.getAttribute('data-confirm-save') || 'Simpan perubahan?'
            confirmed = await window.AdminUtils.confirmSave(msg)
        } else if (target.hasAttribute('data-confirm-logout')) {
            const msg = target.getAttribute('data-confirm-logout') || 'Yakin ingin keluar dari sistem?'
            confirmed = await window.AdminUtils.confirmLogout(msg)
        } else if (target.hasAttribute('data-confirm')) {
            const msg = target.getAttribute('data-confirm') || 'Apakah Anda yakin melakukan tindakan ini?'
            confirmed = await window.AdminUtils.confirm(msg)
        }

        if (!confirmed) {
            event.preventDefault()
            event.stopPropagation()
            return
        }

        // If target is a button without type or with type button, and attached form exists, submit it
        // Otherwise allow default behavior (e.g., link navigation, submit button submit)
        const isButton = target.tagName === 'BUTTON'
        const typeAttr = (target.getAttribute('type') || '').toLowerCase()
        if (isButton && (typeAttr === 'button' || typeAttr === '')) {
            const formId = target.getAttribute('form')
            const form = formId ? document.getElementById(formId) : target.closest('form')
            if (form) {
                event.preventDefault()
                form.submit()
            }
        }
    })

    // Intercept form submits with data-confirm* attributes
    document.addEventListener('submit', async (event) => {
        const form = event.target
        if (!(form instanceof HTMLFormElement)) return

        const attr = ['data-confirm', 'data-confirm-delete', 'data-confirm-save', 'data-confirm-logout'].find(a => form.hasAttribute(a))
        if (!attr) return

        event.preventDefault()

        let confirmed = false
        if (attr === 'data-confirm-delete') {
            const msg = form.getAttribute(attr) || 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?'
            confirmed = await window.AdminUtils.confirmDelete(msg)
        } else if (attr === 'data-confirm-save') {
            const msg = form.getAttribute(attr) || 'Simpan perubahan?'
            confirmed = await window.AdminUtils.confirmSave(msg)
        } else if (attr === 'data-confirm-logout') {
            const msg = form.getAttribute(attr) || 'Yakin ingin keluar dari sistem?'
            confirmed = await window.AdminUtils.confirmLogout(msg)
        } else {
            const msg = form.getAttribute(attr) || 'Apakah Anda yakin melakukan tindakan ini?'
            confirmed = await window.AdminUtils.confirm(msg)
        }

        if (confirmed) form.submit()
    })
})()


