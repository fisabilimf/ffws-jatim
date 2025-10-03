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

// Global helper functions
window.showToast = {
    success: (message, options = {}) => window.AdminUtils.toastSuccess(message, options),
    error: (message, options = {}) => window.AdminUtils.toastError(message, options),
    warning: (message, options = {}) => window.AdminUtils.toastWarning(message, options),
    info: (message, options = {}) => window.AdminUtils.toastInfo(message, options),
    errorDetail: (title, message, options = {}) => window.AdminUtils.toastErrorDetail(title, message, options)
};

// Sistem Notifikasi untuk Operasi CRUD
window.NotificationSystem = {
    create: {
        success: (itemName = 'Data') => window.AdminUtils.toastSuccess(`${itemName} berhasil ditambahkan!`),
        error: (itemName = 'Data', error = '') => {
            const message = error ? `${itemName} gagal ditambahkan. ${error}` : `${itemName} gagal ditambahkan. Silakan coba lagi.`;
            return window.AdminUtils.toastErrorDetail('Gagal Menambah Data', message);
        }
    },
    
    update: {
        success: (itemName = 'Data') => window.AdminUtils.toastSuccess(`${itemName} berhasil diperbarui!`),
        error: (itemName = 'Data', error = '') => {
            const message = error ? `${itemName} gagal diperbarui. ${error}` : `${itemName} gagal diperbarui. Silakan coba lagi.`;
            return window.AdminUtils.toastErrorDetail('Gagal Memperbarui Data', message);
        }
    },
    
    delete: {
        success: (itemName = 'Data') => window.AdminUtils.toastSuccess(`${itemName} berhasil dihapus!`),
        error: (itemName = 'Data', error = '') => {
            const message = error ? `${itemName} gagal dihapus. ${error}` : `${itemName} gagal dihapus. Silakan coba lagi.`;
            return window.AdminUtils.toastErrorDetail('Gagal Menghapus Data', message);
        },
        foreignKeyConstraint: (itemName = 'Data', relatedData = 'data terkait') => {
            const title = 'Tidak Dapat Menghapus Data';
            const message = `${itemName} tidak dapat dihapus karena masih memiliki ${relatedData} yang terkait. Hapus terlebih dahulu ${relatedData} sebelum menghapus ${itemName}.`;
            return window.AdminUtils.toastErrorDetail(title, message, { timer: 7000 });
        }
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

// Alpine Store for Theme Management
Alpine.store('theme', {
    // Inisialisasi theme dari localStorage atau default ke light
    current: localStorage.getItem('theme') || 'light',
    
    init() {
        // Apply saved theme on init
        this.applyTheme();
        
        // Listen for system theme changes if set to system
        if (this.current === 'system') {
            this.watchSystemTheme();
        }
    },
    
    // Toggle between light and dark
    toggle() {
        this.current = this.current === 'light' ? 'dark' : 'light';
        this.applyTheme();
        this.saveToStorage();
    },
    
    // Set specific theme
    setTheme(theme) {
        if (['light', 'dark', 'system'].includes(theme)) {
            this.current = theme;
            this.applyTheme();
            this.saveToStorage();
        }
    },
    
    // Apply theme to document
    applyTheme() {
        const html = document.documentElement;
        
        if (this.current === 'dark') {
            html.classList.add('dark');
        } else if (this.current === 'light') {
            html.classList.remove('dark');
        } else if (this.current === 'system') {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
    },
    
    // Save theme preference to localStorage
    saveToStorage() {
        localStorage.setItem('theme', this.current);
    },
    
    // Watch for system theme changes
    watchSystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', () => {
            if (this.current === 'system') {
                this.applyTheme();
            }
        });
    },
    
    // Get current effective theme (resolves 'system' to actual theme)
    getEffectiveTheme() {
        if (this.current === 'system') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        return this.current;
    },
    
    // Check if current theme is dark
    isDark() {
        return this.getEffectiveTheme() === 'dark';
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

// Admin Dropdown Component
Alpine.data('adminDropdown', () => ({
    isOpen: false,
    
    toggle() {
        this.isOpen = !this.isOpen
    },
    
    close() {
        this.isOpen = false
    },
    
    init() {
        // Close on click outside
        this.$watch('isOpen', (value) => {
            if (value) {
                this.$nextTick(() => {
                    const handleClickOutside = (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.close()
                            document.removeEventListener('click', handleClickOutside)
                        }
                    }
                    document.addEventListener('click', handleClickOutside)
                })
            }
        })
    }
}))

// Theme Dropdown Component
Alpine.data('themeDropdown', () => ({
    isOpen: false,
    
    toggle() {
        this.isOpen = !this.isOpen
    },
    
    close() {
        this.isOpen = false
    },
    
    selectTheme(theme) {
        this.$store.theme.setTheme(theme)
        this.close()
    },
    
    getThemeIcon(theme) {
        const icons = {
            light: 'fas fa-sun text-yellow-500',
            dark: 'fas fa-moon text-indigo-500',
            system: 'fas fa-desktop text-gray-500'
        }
        return icons[theme] || 'fas fa-circle text-gray-400'
    },
    
    init() {
        // Close on click outside
        this.$watch('isOpen', (value) => {
            if (value) {
                this.$nextTick(() => {
                    const handleClickOutside = (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.close()
                            document.removeEventListener('click', handleClickOutside)
                        }
                    }
                    document.addEventListener('click', handleClickOutside)
                })
            }
        })
    }
}))

// Utility functions - Extend AdminUtils
Object.assign(window.AdminUtils, {
    // Format date
    formatDate(date, format = 'DD/MM/YYYY HH:mm') {
        if (!date) return '-'
        
        const d = new Date(date)
        if (isNaN(d.getTime())) return '-'
        
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
    }
})

// Start Alpine
Alpine.start()

// Initialize theme store after Alpine starts
Alpine.store('theme').init()

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

// Auto-bind confirm handlers untuk elements dengan data attributes
(function initSweetAlertAutoBinding() {
    if (window.__sweetalertAutoBound) return
    window.__sweetalertAutoBound = true

    // Click handler untuk elements dengan data-confirm* attributes
    document.addEventListener('click', async (event) => {
        const target = event.target.closest('[data-confirm],[data-confirm-delete]')
        if (!target) return

        let confirmed = false
        if (target.hasAttribute('data-confirm-delete')) {
            const msg = target.getAttribute('data-confirm-delete') || 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?'
            confirmed = await window.AdminUtils.confirmDelete(msg)
        } else if (target.hasAttribute('data-confirm')) {
            const msg = target.getAttribute('data-confirm') || 'Apakah Anda yakin melakukan tindakan ini?'
            confirmed = await window.AdminUtils.confirm(msg)
        }

        if (!confirmed) {
            event.preventDefault()
            event.stopPropagation()
            return
        }

        // Allow default behavior untuk link navigation atau form submit
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
})()

// Helper function untuk mengidentifikasi jenis error database
window.AdminUtils.identifyDatabaseError = function(errorMessage) {
    if (!errorMessage) return 'unknown';
    
    const message = errorMessage.toLowerCase();
    
    if (message.includes('foreign key constraint') || 
        message.includes('integrity constraint violation') ||
        message.includes('cannot delete or update a parent row')) {
        return 'foreign_key';
    }
    
    if (message.includes('duplicate entry') || 
        message.includes('unique constraint') ||
        message.includes('already exists')) {
        return 'duplicate';
    }
    
    return 'unknown';
}


