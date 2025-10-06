@extends('layouts.admin')

@section('title', 'UPTD')
@section('page-title', 'UPTD')
@section('page-description', 'Kelola data Unit Pelaksana Teknis Daerah')
@section('breadcrumb', 'UPTD')

@section('content')
<div class="space-y-6" x-data="masUptdsPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari UPTD',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],
            [
                'type' => 'select',
                'name' => 'upt_code',
                'label' => 'UPT',
                'empty_option' => 'Semua UPT',
                'options' => $upts->toArray()
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
        title="Filter & Pencarian UPTD"
        :filters="$filterConfig"
        :action="route('admin.mas-uptds.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar UPTD"
        :headers="$tableHeaders"
        :rows="$uptds"
        searchable
        searchPlaceholder="Cari UPTD..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah UPTD
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="mas-uptds-create" title="Tambah UPTD" size="lg" :close-on-backdrop="true">
        <form id="masUptdsCreateForm" x-ref="createForm" action="{{ route('admin.mas-uptds.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="uptd_name" 
                    label="Nama UPTD" 
                    placeholder="Contoh: UPTD Pengairan Wilayah Malang" 
                    required="true" 
                    :error="$errors->first('uptd_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="uptd_code" 
                    label="Kode UPTD" 
                    placeholder="Contoh: UPTD001" 
                    required="true" 
                    :error="$errors->first('uptd_code')"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="uptd_address" 
                    label="Alamat UPTD" 
                    placeholder="Alamat lengkap UPTD" 
                    :error="$errors->first('uptd_address')"
                />
                <x-admin.form-input 
                    type="select"
                    name="upt_code" 
                    label="UPT" 
                    required="true" 
                    :error="$errors->first('upt_code')"
                    :options="$upts"
                    empty-option="Pilih UPT"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-uptds-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masUptdsCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="mas-uptds-edit" title="Edit UPTD" size="lg" :close-on-backdrop="true">
        <form id="masUptdsEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="uptd_name" 
                    label="Nama UPTD" 
                    placeholder="Contoh: UPTD Pengairan Wilayah Malang" 
                    required="true" 
                    x-model="editData.uptd_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="uptd_code" 
                    label="Kode UPTD" 
                    placeholder="Contoh: UPTD001" 
                    required="true" 
                    x-model="editData.uptd_code"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="uptd_address" 
                    label="Alamat UPTD" 
                    placeholder="Alamat lengkap UPTD" 
                    x-model="editData.uptd_address"
                />
                <x-admin.form-input 
                    type="select"
                    name="upt_code" 
                    label="UPT" 
                    required="true" 
                    :options="$upts"
                    empty-option="Pilih UPT"
                    x-model="editData.upt_code"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-uptds-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masUptdsEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function masUptdsPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-mas-uptds', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('mas-uptds-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-uptds.index') }}/${data.id}`;
            this.openModal('mas-uptds-edit');
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