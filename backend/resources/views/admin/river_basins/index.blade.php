@extends('layouts.admin')

@section('title', 'Daerah Aliran Sungai')
@section('page-title', 'Daerah Aliran Sungai')
@section('page-description', 'Kelola data Daerah Aliran Sungai')
@section('breadcrumb', 'DAS')

@section('content')
<div class="space-y-6" x-data="riverBasinsPage()" x-init="init()">

    <x-table
        title="Daftar DAS"
        :headers="$tableHeaders"
        :rows="$riverBasins"
        searchable
        :pagination="$riverBasins->links()"
    >
        <x-slot:actions>
            <button type="button" @click="openCreate()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-plus -ml-1 mr-2"></i>
                Tambah DAS
            </button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="river-basin-create" title="Tambah DAS" size="md" :close-on-backdrop="true">
        <form id="rbCreateForm" x-ref="createForm" action="{{ route('admin.region.river-basins.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="name" 
                    label="Nama DAS" 
                    placeholder="Contoh: Brantas" 
                    required="true" 
                    :error="$errors->first('name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="code" 
                    label="Kode DAS" 
                    placeholder="Contoh: DAS-BRANTAS" 
                    required="true" 
                    :error="$errors->first('code')"
                />
            </div>
            <x-slot:footer>
                <button type="button" class="px-4 py-2 border rounded-md" @click="$dispatch('close-modal', 'river-basin-create')">Batal</button>
                <x-admin.button type="submit" variant="primary" form="rbCreateForm">
                    <i class="fas fa-check -ml-1 mr-2"></i>
                    Simpan
                </x-admin.button>
            </x-slot:footer>
        </form>
    </x-admin.modal>

    <!-- Modal Edit -->
    <x-admin.modal :show="false" name="river-basin-edit" title="Edit DAS" size="md" :close-on-backdrop="true">
        <form id="rbEditForm" x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <input type="hidden" name="id" :value="editData.id" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input 
                    type="text" 
                    name="name" 
                    label="Nama DAS" 
                    x-model="editData.name" 
                    required="true" 
                    :error="$errors->first('name')"
                />
                <x-admin.form-input 
                    type="text" 
                    name="code" 
                    label="Kode DAS" 
                    x-model="editData.code" 
                    required="true" 
                    :error="$errors->first('code')"
                />
            </div>
            <x-slot:footer>
                <button type="button" class="px-4 py-2 border rounded-md" @click="$dispatch('close-modal', 'river-basin-edit')">Batal</button>
                <x-admin.button type="submit" variant="primary" form="rbEditForm">
                    <i class="fas fa-check -ml-1 mr-2"></i>
                    Update
                </x-admin.button>
            </x-slot:footer>
        </form>
    </x-admin.modal>
</div>

@push('scripts')
<script>
function riverBasinsPage() {
    return {
        editData: { id: null, name: '', code: '' },
        editAction: '',
        init() {
            // Listener untuk aksi edit dari tabel
            window.addEventListener('open-edit-river-basin', (e) => {
                const item = e.detail || {};
                this.openEdit(item);
            });
        },
        openCreate() {
            this.$dispatch('open-modal', 'river-basin-create');
        },
        openEdit(item) {
            this.editData = { id: item.id, name: item.name, code: item.code };
            this.editAction = `${window.location.origin}/admin/region/river-basins/${item.id}`;
            this.$dispatch('open-modal', 'river-basin-edit');
        }
    }
}
</script>
@endpush
@endsection