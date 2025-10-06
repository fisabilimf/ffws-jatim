@extends('layouts.admin')

@section('title', 'DAS (Daerah Aliran Sungai)')
@section('page-title', 'DAS (Daerah Aliran Sungai)')
@section('page-description', 'Kelola data Daerah Aliran Sungai')
@section('breadcrumb', 'DAS')

@section('content')
<div class="space-y-6" x-data="watershedsPage()" x-init="init()">

    <!-- Filter Section -->
    @php
        $filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari DAS',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],
            [
                'type' => 'select',
                'name' => 'river_basin_code',
                'label' => 'Sub DAS',
                'empty_option' => 'Semua Sub DAS',
                'options' => $riverBasins->toArray()
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
        title="Filter & Pencarian DAS"
        :filters="$filterConfig"
        :action="route('admin.mas-watersheds.index')"
        gridCols="md:grid-cols-3"
    />

    <x-table
        title="Daftar DAS (Daerah Aliran Sungai)"
        :headers="$tableHeaders"
        :rows="$watersheds"
        searchable
        searchPlaceholder="Cari DAS..."
    >
        <x-slot:actions>
            <x-admin.button type="button" variant="primary" @click="openCreate()">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah DAS
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="watershed-create" title="Tambah DAS" size="lg" :close-on-backdrop="true">
        <form id="watershedCreateForm" x-ref="createForm" action="{{ route('admin.mas-watersheds.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="river_basin_code" 
                    label="Sub DAS" 
                    required="true" 
                    :error="$errors->first('river_basin_code')"
                    :options="$riverBasins"
                    empty-option="Pilih Sub DAS"
                />
                <x-admin.form-input 
                    type="text" 
                    name="watersheds_name" 
                    label="Nama DAS" 
                    placeholder="Contoh: DAS Brantas" 
                    required="true" 
                    :error="$errors->first('watersheds_name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="watersheds_code" 
                    label="Kode DAS" 
                    placeholder="Contoh: DAS001" 
                    required="true" 
                    :error="$errors->first('watersheds_code')"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('watershed-create')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="watershedCreateForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show="false" name="watershed-edit" title="Edit DAS" size="lg" :close-on-backdrop="true">
        <form id="watershedEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="select"
                    name="river_basin_code" 
                    label="Sub DAS" 
                    required="true" 
                    :options="$riverBasins"
                    empty-option="Pilih Sub DAS"
                    x-model="editData.river_basin_code"
                />
                <x-admin.form-input 
                    type="text" 
                    name="watersheds_name" 
                    label="Nama DAS" 
                    placeholder="Contoh: DAS Brantas" 
                    required="true" 
                    x-model="editData.watersheds_name"
                />
                <x-admin.form-input 
                    type="text" 
                    name="watersheds_code" 
                    label="Kode DAS" 
                    placeholder="Contoh: DAS001" 
                    required="true" 
                    x-model="editData.watersheds_code"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type="button" variant="secondary" @click="closeModal('watershed-edit')">
                Batal
            </x-admin.button>
            <x-admin.button type="submit" variant="primary" form="watershedEditForm">
                <i class="fa-solid fa-save -ml-1 mr-2"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function watershedsPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-watershed', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.$refs.createForm.reset();
            this.openModal('watershed-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.mas-watersheds.index') }}/${data.id}`;
            this.openModal('watershed-edit');
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