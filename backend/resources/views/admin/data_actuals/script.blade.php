<script>
    /**
 * Data Actuals Management JavaScript
 * Handles CRUD operations for Data Actuals with modal integration
 */

class DataActualsManager {
    constructor() {
        this.isEditMode = false;
        this.currentDataActualId = null;
        this.init();
    }

    init() {
        // Set default datetime untuk create mode
        this.setDefaultDateTime();
        
        // Bind event listeners
        this.bindEventListeners();
    }

    /**
     * Set default datetime untuk create mode
     */
    setDefaultDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const defaultDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        const receivedAtInput = document.getElementById('received_at');
        if (receivedAtInput) {
            receivedAtInput.value = defaultDateTime;
        }
    }

    /**
     * Bind event listeners
     */
    bindEventListeners() {
        // Sensor select change event
        const sensorSelect = document.getElementById('mas_sensor_id');
        if (sensorSelect) {
            sensorSelect.addEventListener('change', (e) => {
                this.handleSensorChange(e);
            });
        }
    }

    /**
     * Handle sensor select change
     */
    handleSensorChange(event) {
        const selectedOption = event.target.options[event.target.selectedIndex];
        const sensorInfo = document.getElementById('sensor_info');
        
        if (selectedOption.value && sensorInfo) {
            sensorInfo.innerHTML = 
                `<strong>Device:</strong> ${selectedOption.dataset.deviceName} | <strong>DAS:</strong> ${selectedOption.dataset.riverBasin} | <strong>Unit:</strong> ${selectedOption.dataset.unit}`;
        } else if (sensorInfo) {
            sensorInfo.innerHTML = '';
        }
    }

    /**
     * Open create modal
     */
    openCreateModal() {
        this.isEditMode = false;
        this.currentDataActualId = null;
        
        // Reset form
        const form = document.getElementById('dataActualForm');
        if (form) {
            form.reset();
        }
        
        // Set form method
        const formMethod = document.getElementById('formMethod');
        if (formMethod) {
            formMethod.value = 'POST';
        }
        
        // Update submit button text
        const submitText = document.getElementById('submitText');
        if (submitText) {
            submitText.textContent = 'Simpan';
        }
        
        // Update modal title
        this.updateModalTitle('Tambah Data Actual');
        
        // Load sensors
        this.loadSensors();
        
        // Open modal
        this.openModal();
    }

    /**
     * Open edit modal
     */
    openEditModal(id) {
        this.isEditMode = true;
        this.currentDataActualId = id;
        
        // Set form method
        const formMethod = document.getElementById('formMethod');
        if (formMethod) {
            formMethod.value = 'PUT';
        }
        
        // Update submit button text
        const submitText = document.getElementById('submitText');
        if (submitText) {
            submitText.textContent = 'Update';
        }
        
        // Load data untuk edit
        this.loadEditData(id);
    }

    /**
     * Load edit data
     */
    async loadEditData(id) {
        try {
            const response = await fetch(`/admin/data-actuals/${id}/edit`);
            const data = await response.json();
            
            if (data.success) {
                // Load sensors
                this.loadSensors(data.sensors);
                
                // Fill form dengan data existing
                this.fillForm(data.dataActual);
                
                // Update sensor info
                this.updateSensorInfo(data.dataActual.mas_sensor_id, data.sensors);
                
                // Update modal title
                this.updateModalTitle('Edit Data Actual');
                
                // Open modal
                this.openModal();
            } else {
                this.showError('Gagal memuat data untuk edit');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Gagal memuat data untuk edit');
        }
    }

    /**
     * Fill form with data
     */
    fillForm(dataActual) {
        const fields = [
            { id: 'dataActualId', value: dataActual.id },
            { id: 'mas_sensor_id', value: dataActual.mas_sensor_id },
            { id: 'value', value: dataActual.value },
            { id: 'received_at', value: dataActual.received_at },
            { id: 'threshold_status', value: dataActual.threshold_status }
        ];

        fields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element) {
                element.value = field.value;
            }
        });
    }

    /**
     * Close modal
     */
    closeModal() {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'dataActualModal' }));
    }

    /**
     * Open modal
     */
    openModal() {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'dataActualModal' }));
    }

    /**
     * Update modal title
     */
    updateModalTitle(title) {
        const titleElement = document.querySelector('[x-data] h3');
        if (titleElement) {
            titleElement.textContent = title;
        }
    }

    /**
     * Load sensors
     */
    async loadSensors(sensors = null) {
        if (sensors) {
            this.populateSensorSelect(sensors);
        } else {
            try {
                const response = await fetch('/admin/data-actuals/create');
                const data = await response.json();
                
                if (data.success) {
                    this.populateSensorSelect(data.sensors);
                } else {
                    this.showError('Gagal memuat data sensor');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showError('Gagal memuat data sensor');
            }
        }
    }

    /**
     * Populate sensor select
     */
    populateSensorSelect(sensors) {
        const select = document.getElementById('mas_sensor_id');
        if (!select) return;
        
        select.innerHTML = '<option value="">Pilih Sensor</option>';
        
        sensors.forEach(sensor => {
            const option = document.createElement('option');
            option.value = sensor.id;
            option.textContent = `${sensor.sensor_code} - ${sensor.parameter}`;
            option.dataset.deviceName = sensor.device_name;
            option.dataset.riverBasin = sensor.river_basin;
            option.dataset.unit = sensor.unit;
            select.appendChild(option);
        });
    }

    /**
     * Update sensor info
     */
    updateSensorInfo(sensorId, sensors) {
        const sensor = sensors.find(s => s.id == sensorId);
        const sensorInfo = document.getElementById('sensor_info');
        
        if (sensor && sensorInfo) {
            sensorInfo.innerHTML = 
                `<strong>Device:</strong> ${sensor.device_name} | <strong>DAS:</strong> ${sensor.river_basin} | <strong>Unit:</strong> ${sensor.unit}`;
        }
    }

    /**
     * Submit form
     */
    async submitForm() {
        const form = document.getElementById('dataActualForm');
        if (!form) return;
        
        const formData = new FormData(form);
        
        // Tentukan URL berdasarkan mode
        const url = this.isEditMode ? 
            `/admin/data-actuals/${this.currentDataActualId}` : 
            '/admin/data-actuals';
        
        // Tampilkan loading
        this.setLoadingState(true);
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.closeModal();
                // Reload halaman untuk menampilkan data terbaru
                window.location.reload();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Terjadi kesalahan saat menyimpan data');
        } finally {
            this.setLoadingState(false);
        }
    }

    /**
     * Set loading state
     */
    setLoadingState(isLoading) {
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        if (submitBtn && submitText) {
            if (isLoading) {
                submitBtn.disabled = true;
                submitText.textContent = 'Menyimpan...';
            } else {
                submitBtn.disabled = false;
                submitText.textContent = this.isEditMode ? 'Update' : 'Simpan';
            }
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        if (window.showToast && window.showToast.success) {
            window.showToast.success(message);
        } else {
            alert(message);
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        if (window.showToast && window.showToast.error) {
            window.showToast.error(message);
        } else {
            alert(message);
        }
    }
}

// Global functions untuk kompatibilitas dengan onclick attributes
let dataActualsManager;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    dataActualsManager = new DataActualsManager();
    
    // Make manager available globally
    window.dataActualsManager = dataActualsManager;
});

// Global functions
function openCreateModal() {
    if (dataActualsManager) {
        dataActualsManager.openCreateModal();
    }
}

function openEditModal(id) {
    if (dataActualsManager) {
        dataActualsManager.openEditModal(id);
    }
}

function closeModal() {
    if (dataActualsManager) {
        dataActualsManager.closeModal();
    }
}

function submitForm() {
    if (dataActualsManager) {
        dataActualsManager.submitForm();
    }
}

// Make functions available globally
window.openCreateModal = openCreateModal;
window.openEditModal = openEditModal;
window.closeModal = closeModal;
window.submitForm = submitForm;

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DataActualsManager;
}

</script>