@extends('layouts.admin')

@section('title', 'UPT')
@section('page-title', 'UPT')
@section('page-description', 'Kelola data Unit Pelaksana Teknis')
@section('breadcrumb', 'UPT')

@section('content')
<div class="space-y-6" x-data="masUptsPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari UPT',
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
        title="Filter & Pencarian UPT"
        :filters="$filterConfig"
        :action="route('admin.mas-upts.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar UPT"
        :headers="$tableHeaders"
        :rows="$upts"
        searchable
        searchPlaceholder="Cari UPT..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah UPT
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="mas-upts-create" title="Tambah UPT" size="lg" :close-on-backdrop="true">
        <form id="masUptsCreateForm" x-ref="createForm" action="{{ route('admin.mas-upts.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="upts_name" 
                    label="Nama UPT" 
                    placeholder="Contoh: UPT Pengairan Kota Malang" 
                    required="true" 
                    :error="$errors->first('upts_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="upts_code" 
                    label="Kode UPT" 
                    placeholder="Contoh: UPT001" 
                    required="true" 
                    :error="$errors->first('upts_code')"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="upts_address" 
                    label="Alamat UPT" 
                    placeholder="Alamat lengkap UPT" 
                    :error="$errors->first('upts_address')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-upts-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masUptsCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="mas-upts-edit" title="Edit UPT" size="lg" :close-on-backdrop="true">
        <form id="masUptsEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="upts_name" 
                    label="Nama UPT" 
                    placeholder="Contoh: UPT Pengairan Kota Malang" 
                    required="true" 
                    x-model="editData.upts_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="upts_code" 
                    label="Kode UPT" 
                    placeholder="Contoh: UPT001" 
                    required="true" 
                    x-model="editData.upts_code"
                />
                <x-admin.form-input 
                    type="textarea" 
                    name="upts_address" 
                    label="Alamat UPT" 
                    placeholder="Alamat lengkap UPT" 
                    x-model="editData.upts_address"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('mas-upts-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="masUptsEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function masUptsPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-upt', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('mas-upts-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-upts.index') }}/${data.id}`;
            this.openModal('mas-upts-edit');
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