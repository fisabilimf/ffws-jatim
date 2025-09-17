@push('scripts')
<script>
// Global functions untuk halaman mas_models
window.MasModelsPage = {
    // Function untuk format model type
    formatModelType: function(type) {
        const types = {
            'lstm': 'LSTM',
            'gru': 'GRU', 
            'transformer': 'Transformer',
            'cnn': 'CNN',
            'rnn': 'RNN',
            'other': 'Lainnya'
        };
        return types[type] || type.toUpperCase();
    },

    // Function untuk format status
    formatStatus: function(isActive) {
        return isActive ? 'Aktif' : 'Non-aktif';
    },

    // Function untuk format status badge
    getStatusBadgeClass: function(isActive) {
        return isActive 
            ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
            : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800';
    },

    // Function untuk format model type badge
    getModelTypeBadgeClass: function(type) {
        const classes = {
            'lstm': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
            'gru': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
            'transformer': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800',
            'cnn': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
            'rnn': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800',
            'other': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
        };
        return classes[type] || classes['other'];
    },

    // Function untuk validasi form
    validateModelForm: function(formData) {
        const errors = {};
        
        // Validasi nama model
        if (!formData.name || formData.name.trim() === '') {
            errors.name = 'Nama model harus diisi';
        }
        
        // Validasi tipe model
        if (!formData.model_type || formData.model_type.trim() === '') {
            errors.model_type = 'Tipe model harus dipilih';
        }
        
        // Validasi status
        if (formData.is_active === undefined || formData.is_active === '') {
            errors.is_active = 'Status harus dipilih';
        }
        
        // Validasi n_steps_in jika diisi
        if (formData.n_steps_in && (isNaN(formData.n_steps_in) || formData.n_steps_in < 1 || formData.n_steps_in > 255)) {
            errors.n_steps_in = 'Jumlah step input harus antara 1-255';
        }
        
        // Validasi n_steps_out jika diisi
        if (formData.n_steps_out && (isNaN(formData.n_steps_out) || formData.n_steps_out < 1 || formData.n_steps_out > 255)) {
            errors.n_steps_out = 'Jumlah step output harus antara 1-255';
        }
        
        return errors;
    },

    // Function untuk konfirmasi hapus
    confirmDelete: function(modelId, modelName) {
        if (confirm(`Apakah Anda yakin ingin menghapus model "${modelName}"?`)) {
            // Trigger delete action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/mas-models/${modelId}`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    },

    // Function untuk toggle status
    toggleStatus: function(modelId, currentStatus) {
        const newStatus = currentStatus ? 0 : 1;
        const statusText = newStatus ? 'mengaktifkan' : 'menonaktifkan';
        
        if (confirm(`Apakah Anda yakin ingin ${statusText} model ini?`)) {
            // Trigger status update
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/mas-models/${modelId}/toggle-status`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'is_active';
            statusInput.value = newStatus;
            
            form.appendChild(csrfInput);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }
    },

    // Function untuk copy file path
    copyFilePath: function(filePath) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(filePath).then(() => {
                // Show success message
                this.showNotification('Path file berhasil disalin', 'success');
            }).catch(() => {
                this.showNotification('Gagal menyalin path file', 'error');
            });
        } else {
            // Fallback untuk browser lama
            const textArea = document.createElement('textarea');
            textArea.value = filePath;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showNotification('Path file berhasil disalin', 'success');
        }
    },

    // Function untuk show notification
    showNotification: function(message, type = 'info') {
        // Buat element notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Hapus setelah 3 detik
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    },

    // Function untuk export data
    exportData: function(format = 'csv') {
        const url = `/admin/mas-models/export?format=${format}`;
        window.open(url, '_blank');
    },

    // Function untuk import data
    importData: function(file) {
        if (!file) return;
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/admin/mas-models/import', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Data berhasil diimport', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showNotification(data.message || 'Gagal mengimport data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Terjadi kesalahan saat mengimport data', 'error');
        });
    }
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    });
    
    // Form validation
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const errors = MasModelsPage.validateModelForm(data);
            
            if (Object.keys(errors).length > 0) {
                e.preventDefault();
                // Show errors
                Object.keys(errors).forEach(field => {
                    const errorElement = document.querySelector(`[data-field="${field}"] .error-message`);
                    if (errorElement) {
                        errorElement.textContent = errors[field];
                        errorElement.style.display = 'block';
                    }
                });
            }
        });
    });
});
</script>
@endpush
