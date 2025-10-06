@extends('layouts.admin')

@section('title', 'Parameter Sensor')
@section('page-title', 'Parameter Sensor')
@section('page-description', 'Kelola data parameter sensor monitoring')
@section('breadcrumb', 'Parameter Sensor')

@section('content')
<div class="space-y-6" x-data="masSensorParametersPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Parameter Sensor',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],
            [
                'type' => 'select',
                'name' => 'per_page',
                'label' => 'Per Halaman',
                'options' => [
                    ['value' => '10', 'label' => '10'],
                    ['value' => '15', 'label' => '15'],
                    ['value' => '25', 'label' => '25'],
                    ['value' => '50', 'label' => '50'],
                    ['value' => '100', 'label' => '100']
                ]
            ]
        ];
    @endphp

    <x-filter-bar 
        title="Filter & Pencarian Parameter Sensor"
        :filters="$filterConfig"
        :action="route('admin.mas-sensor-parameters.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar Parameter Sensor"
        :headers="$tableHeaders"
        :rows="$sensor_parameters"
        searchable
        searchPlaceholder="Cari Parameter Sensor..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Parameter Sensor
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="mas-sensor-parameters-create" title="Tambah Parameter Sensor" size="lg" :close-on-backdrop="true">
        <form id="masSensorParametersCreateForm" x-ref="createForm" action="{{ route('admin.mas-sensor-parameters.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="sensor_parameter_name" 
                    label="Nama Parameter" 
                    placeholder="Contoh: Suhu Air" 
                    required="true" 
                    :error="$errors->first('sensor_parameter_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="sensor_parameter_code" 
                    label="Kode Parameter" 
                    placeholder="Contoh: TEMP" 
                    required="true" 
                    :error="$errors->first('sensor_parameter_code')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="sensor_parameter_unit" 
                    label="Satuan" 
                    placeholder="Contoh: °C" 
                    required="true" 
                    :error="$errors->first('sensor_parameter_unit')"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="sensor_parameter_description" 
                    label="Deskripsi" 
                    placeholder="Deskripsi parameter sensor" 
                    :error="$errors->first('sensor_parameter_description')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-sensor-parameters-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masSensorParametersCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="mas-sensor-parameters-edit" title="Edit Parameter Sensor" size="lg" :close-on-backdrop="true">
        <form id="masSensorParametersEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="sensor_parameter_name" 
                    label="Nama Parameter" 
                    placeholder="Contoh: Suhu Air" 
                    required="true" 
                    x-model="editData.sensor_parameter_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="sensor_parameter_code" 
                    label="Kode Parameter" 
                    placeholder="Contoh: TEMP" 
                    required="true" 
                    x-model="editData.sensor_parameter_code"
                />
                <x-admin.form-input 
                    type="text" 
                    name="sensor_parameter_unit" 
                    label="Satuan" 
                    placeholder="Contoh: °C" 
                    required="true" 
                    x-model="editData.sensor_parameter_unit"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="sensor_parameter_description" 
                    label="Deskripsi" 
                    placeholder="Deskripsi parameter sensor" 
                    x-model="editData.sensor_parameter_description"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-sensor-parameters-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masSensorParametersEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function masSensorParametersPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-mas-sensor-parameters', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('mas-sensor-parameters-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-sensor-parameters.index') }}/${data.id}`;
            this.openModal('mas-sensor-parameters-edit');
        },
        
        openModal(name) {
            this.$dispatch('open-modal', name);
        },
        
        closeModal(name) {
            this.$dispatch('close-modal', name);
        }
    }
}
</script>
@endsection