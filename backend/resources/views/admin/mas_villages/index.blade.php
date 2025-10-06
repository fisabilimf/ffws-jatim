@extends('layouts.admin')

@section('title', 'Desa/Kelurahan')
@section('page-title', 'Desa/Kelurahan')
@section('page-description', 'Kelola data desa/kelurahan')
@section('breadcrumb', 'Desa/Kelurahan')

@section('content')
<div class="space-y-6" x-data="villagesPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Desa/Kelurahan',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],
            [
                'type' => 'select',
                'name' => 'cities_code',
                'label' => 'Kecamatan',
                'empty_option' => 'Semua Kecamatan',
                'options' => $cities->toArray()
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
        title="Filter & Pencarian Desa/Kelurahan"
        :filters="$filterConfig"
        :action="route('admin.mas-villages.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar Desa/Kelurahan"
        :headers="$tableHeaders"
        :rows="$villages"
        searchable
        searchPlaceholder="Cari desa/kelurahan..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Desa/Kelurahan
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="village-create" title="Tambah Desa/Kelurahan" size="lg" :close-on-backdrop="true">
        <form id="villageCreateForm" x-ref="createForm" action="{{ route('admin.mas-villages.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="cities_code" 
                    label="Kecamatan" 
                    required="true" 
                    :error="$errors->first('cities_code')"
                    :options="$cities"
                    empty-option="Pilih Kecamatan"
                />
                <x-admin.form-input 
                    type="text" 
                    name="villages_name" 
                    label="Nama Desa/Kelurahan" 
                    placeholder="Contoh: Desa Sumbersari" 
                    required="true" 
                    :error="$errors->first('villages_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="villages_code" 
                    label="Kode Desa/Kelurahan" 
                    placeholder="Contoh: 3507041001" 
                    required="true" 
                    :error="$errors->first('villages_code')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('village-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="villageCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="village-edit" title="Edit Desa/Kelurahan" size="lg" :close-on-backdrop="true">
        <form id="villageEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="cities_code" 
                    label="Kecamatan" 
                    required="true" 
                    :options="$cities"
                    empty-option="Pilih Kecamatan"
                    x-model="editData.cities_code"
                />
                <x-admin.form-input 
                    type="text" 
                    name="villages_name" 
                    label="Nama Desa/Kelurahan" 
                    placeholder="Contoh: Desa Sumbersari" 
                    required="true" 
                    x-model="editData.villages_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="villages_code" 
                    label="Kode Desa/Kelurahan" 
                    placeholder="Contoh: 3507041001" 
                    required="true" 
                    x-model="editData.villages_code"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('village-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="villageEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function villagesPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-village', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('village-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-villages.index') }}/${data.id}`;
            this.openModal('village-edit');
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