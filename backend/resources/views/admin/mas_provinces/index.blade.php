@extends('layouts.admin')

@section('title', 'Provinsi')
@section('page-title', 'Provinsi')
@section('page-description', 'Kelola data provinsi')
@section('breadcrumb', 'Provinsi')

@section('content')
<div class="space-y-6" x-data="provincesPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Provinsi',
                'placeholder' => 'Cari berdasarkan nama atau kode provinsi...'
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
        title="Filter & Pencarian Provinsi"
        :filters="$filterConfig"
        :action="route('admin.mas-provinces.index')"
        gridCols="md:grid-cols-2"
    />

    <x-table
        title="Daftar Provinsi"
        :headers="$tableHeaders"
        :rows="$provinces"
        searchable
        searchPlaceholder="Cari provinsi..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Provinsi
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="province-create" title="Tambah Provinsi" size="lg" :close-on-backdrop="true">
        <form id="provinceCreateForm" x-ref="createForm" action="{{ route('admin.mas-provinces.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="provinces_name" 
                    label="Nama Provinsi" 
                    placeholder="Contoh: Jawa Timur" 
                    required="true" 
                    :error="$errors->first('provinces_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="provinces_code" 
                    label="Kode Provinsi" 
                    placeholder="Contoh: 35" 
                    required="true" 
                    :error="$errors->first('provinces_code')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('province-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="provinceCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="province-edit" title="Edit Provinsi" size="lg" :close-on-backdrop="true">
        <form id="provinceEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="provinces_name" 
                    label="Nama Provinsi" 
                    placeholder="Contoh: Jawa Timur" 
                    required="true" 
                    x-model="editData.provinces_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="provinces_code" 
                    label="Kode Provinsi" 
                    placeholder="Contoh: 35" 
                    required="true" 
                    x-model="editData.provinces_code"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('province-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="provinceEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function provincesPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-province', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('province-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-provinces.index') }}/${data.id}`;
            this.openModal('province-edit');
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