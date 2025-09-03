// Admin Panel JavaScript
import Alpine from 'alpinejs'

// Make Alpine available globally
window.Alpine = Alpine

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

// Utility functions
window.AdminUtils = {
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
        return new Promise((resolve) => {
            const confirmed = window.confirm(message)
            resolve(confirmed)
        })
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
}

// Start Alpine
Alpine.start()

// Export for use in other modules
export default Alpine


