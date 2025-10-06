@extends('layouts.admin')

@section('title', 'Parameter Perangkat')
@section('page-title', 'Parameter Perangkat')
@section('page-description', 'Kelola data parameter perangkat monitoring')
@section('breadcrumb', 'Parameter Perangkat')

@section('content')
<div class="space-y-6" x-data="masDeviceParametersPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Parameter Perangkat',
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
        title="Filter & Pencarian Parameter Perangkat"
        :filters="$filterConfig"
        :action="route('admin.mas-device-parameters.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar Parameter Perangkat"
        :headers="$tableHeaders"
        :rows="$device_parameters"
        searchable
        searchPlaceholder="Cari Parameter Perangkat..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Parameter Perangkat
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="mas-device-parameters-create" title="Tambah Parameter Perangkat" size="lg" :close-on-backdrop="true">
        <form id="masDeviceParametersCreateForm" x-ref="createForm" action="{{ route('admin.mas-device-parameters.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="device_parameter_name" 
                    label="Nama Parameter" 
                    placeholder="Contoh: Tinggi Muka Air" 
                    required="true" 
                    :error="$errors->first('device_parameter_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="device_parameter_code" 
                    label="Kode Parameter" 
                    placeholder="Contoh: TMA" 
                    required="true" 
                    :error="$errors->first('device_parameter_code')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="device_parameter_unit" 
                    label="Satuan" 
                    placeholder="Contoh: cm" 
                    required="true" 
                    :error="$errors->first('device_parameter_unit')"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="device_parameter_description" 
                    label="Deskripsi" 
                    placeholder="Deskripsi parameter" 
                    :error="$errors->first('device_parameter_description')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-device-parameters-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masDeviceParametersCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="mas-device-parameters-edit" title="Edit Parameter Perangkat" size="lg" :close-on-backdrop="true">
        <form id="masDeviceParametersEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="device_parameter_name" 
                    label="Nama Parameter" 
                    placeholder="Contoh: Tinggi Muka Air" 
                    required="true" 
                    x-model="editData.device_parameter_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="device_parameter_code" 
                    label="Kode Parameter" 
                    placeholder="Contoh: TMA" 
                    required="true" 
                    x-model="editData.device_parameter_code"
                />
                <x-admin.form-input 
                    type="text" 
                    name="device_parameter_unit" 
                    label="Satuan" 
                    placeholder="Contoh: cm" 
                    required="true" 
                    x-model="editData.device_parameter_unit"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="device_parameter_description" 
                    label="Deskripsi" 
                    placeholder="Deskripsi parameter" 
                    x-model="editData.device_parameter_description"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-device-parameters-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masDeviceParametersEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function masDeviceParametersPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-mas-device-parameters', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('mas-device-parameters-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-device-parameters.index') }}/${data.id}`;
            this.openModal('mas-device-parameters-edit');
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