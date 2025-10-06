@extends('layouts.admin')

@section('title', 'Kecamatan')
@section('page-title', 'Kecamatan')
@section('page-description', 'Kelola data kecamatan')
@section('breadcrumb', 'Kecamatan')

@section('content')
<div class="space-y-6" x-data="citiesPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari Kecamatan',
                'placeholder' => 'Cari berdasarkan nama atau kode kecamatan...'
            ],
            [
                'type' => 'select',
                'name' => 'regencies_code',
                'label' => 'Kabupaten/Kota',
                'empty_option' => 'Semua Kabupaten/Kota',
                'options' => $regencies->toArray()
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
        title="Filter & Pencarian Kecamatan"
        :filters="$filterConfig"
        :action="route('admin.mas-cities.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar Kecamatan"
        :headers="$tableHeaders"
        :rows="$cities"
        searchable
        searchPlaceholder="Cari kecamatan..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah Kecamatan
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="city-create" title="Tambah Kecamatan" size="lg" :close-on-backdrop="true">
        <form id="cityCreateForm" x-ref="createForm" action="{{ route('admin.mas-cities.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="regencies_code" 
                    label="Kabupaten/Kota" 
                    required="true" 
                    :error="$errors->first('regencies_code')"
                    :options="$regencies"
                    empty-option="Pilih Kabupaten/Kota"
                />
                <x-admin.form-input 
                    type="text" 
                    name="cities_name" 
                    label="Nama Kecamatan" 
                    placeholder="Contoh: Kecamatan Lowokwaru" 
                    required="true" 
                    :error="$errors->first('cities_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="cities_code" 
                    label="Kode Kecamatan" 
                    placeholder="Contoh: 350704" 
                    required="true" 
                    :error="$errors->first('cities_code')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('city-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="cityCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="city-edit" title="Edit Kecamatan" size="lg" :close-on-backdrop="true">
        <form id="cityEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="regencies_code" 
                    label="Kabupaten/Kota" 
                    required="true" 
                    :options="$regencies"
                    empty-option="Pilih Kabupaten/Kota"
                    x-model="editData.regencies_code"
                />
                <x-admin.form-input 
                    type="text" 
                    name="cities_name" 
                    label="Nama Kecamatan" 
                    placeholder="Contoh: Kecamatan Lowokwaru" 
                    required="true" 
                    x-model="editData.cities_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="cities_code" 
                    label="Kode Kecamatan" 
                    placeholder="Contoh: 350704" 
                    required="true" 
                    x-model="editData.cities_code"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('city-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="cityEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function citiesPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-city', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('city-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-cities.index') }}/${data.id}`;
            this.openModal('city-edit');
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