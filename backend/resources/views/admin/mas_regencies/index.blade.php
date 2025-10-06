@extends('layouts.admin')

@section('title', 'Kabupaten/Kota')
@section('page-title', 'Kabupaten/Kota')
@section('page-description', 'Kelola data kabupaten/kota')
@section('breadcrumb', 'Kabupaten/Kota')

@section('content')
<div class="space-y-6" x-data="regenciesPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Kabupaten/Kota',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],
            [
                'type' => 'select',
                'name' => 'provinces_code',
                'label' => 'Provinsi',
                'empty_option' => 'Semua Provinsi',
                'options' => $provinces->toArray()
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
        title="Filter & Pencarian Kabupaten/Kota"
        :filters="$filterConfig"
        :action="route('admin.mas-regencies.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar Kabupaten/Kota"
        :headers="$tableHeaders"
        :rows="$regencies"
        searchable
        searchPlaceholder="Cari kabupaten/kota..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Kabupaten/Kota
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="regency-create" title="Tambah Kabupaten/Kota" size="lg" :close-on-backdrop="true">
        <form id="regencyCreateForm" x-ref="createForm" action="{{ route('admin.mas-regencies.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="provinces_code" 
                    label="Provinsi" 
                    required="true" 
                    :error="$errors->first('provinces_code')"
                    :options="$provinces"
                    empty-option="Pilih Provinsi"
                />
                <x-admin.form-input 
                    type="text" 
                    name="regencies_name" 
                    label="Nama Kabupaten/Kota" 
                    placeholder="Contoh: Kabupaten Malang" 
                    required="true" 
                    :error="$errors->first('regencies_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="regencies_code" 
                    label="Kode Kabupaten/Kota" 
                    placeholder="Contoh: 3507" 
                    required="true" 
                    :error="$errors->first('regencies_code')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('regency-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="regencyCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="regency-edit" title="Edit Kabupaten/Kota" size="lg" :close-on-backdrop="true">
        <form id="regencyEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="provinces_code" 
                    label="Provinsi" 
                    required="true" 
                    :options="$provinces"
                    empty-option="Pilih Provinsi"
                    x-model="editData.provinces_code"
                />
                <x-admin.form-input 
                    type="text" 
                    name="regencies_name" 
                    label="Nama Kabupaten/Kota" 
                    placeholder="Contoh: Kabupaten Malang" 
                    required="true" 
                    x-model="editData.regencies_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="regencies_code" 
                    label="Kode Kabupaten/Kota" 
                    placeholder="Contoh: 3507" 
                    required="true" 
                    x-model="editData.regencies_code"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('regency-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="regencyEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function regenciesPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-regency', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('regency-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-regencies.index') }}/${data.id}`;
            this.openModal('regency-edit');
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