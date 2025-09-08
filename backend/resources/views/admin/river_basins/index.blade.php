@extends('layouts.admin')

@section('title', 'Daerah Aliran Sungai')
@section('page-title', 'Daerah Aliran Sungai')
@section('page-description', 'Kelola data Daerah Aliran Sungai')
@section('breadcrumb', 'DAS')

@section('content')
<div class="space-y-6" x-data="riverBasinsPage()" x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Daftar DAS</h2>
            <p class="mt-1 text-sm text-gray-500">Kelola master data DAS</p>
        </div>
    </div>

    <x-datatable
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
    </x-datatable>

    <!-- Modal Create -->
    <x-admin.modal :show="false" name="river-basin-create" title="Tambah DAS" size="md">
        <form x-ref="createForm" action="{{ route('admin.master.river-basins.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="context" value="create" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input type="text" name="name" label="Nama DAS" placeholder="Contoh: Brantas" required="true" />
                <x-admin.form-input type="text" name="code" label="Kode DAS" placeholder="Contoh: DAS-BRANTAS" required="true" />
            </div>
            <x-slot:footer>
                <button type="button" class="px-4 py-2 border rounded-md" @click="$dispatch('close-modal', 'river-basin-create')">Batal</button>
                <x-admin.button type="submit" variant="primary">
                    <i class="fas fa-check -ml-1 mr-2"></i>
                    Simpan
                </x-admin.button>
            </x-slot:footer>
        </form>
    </x-admin.modal>

    <!-- Modal Edit -->
    <x-admin.modal :show="false" name="river-basin-edit" title="Edit DAS" size="md">
        <form x-ref="editForm" :action="editAction" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="context" value="edit" />
            <input type="hidden" name="id" :value="editData.id" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.form-input type="text" name="name" label="Nama DAS" x-model="editData.name" required="true" />
                <x-admin.form-input type="text" name="code" label="Kode DAS" x-model="editData.code" required="true" />
            </div>
            <x-slot:footer>
                <button type="button" class="px-4 py-2 border rounded-md" @click="$dispatch('close-modal', 'river-basin-edit')">Batal</button>
                <x-admin.button type="submit" variant="primary">
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
            // Buka modal otomatis jika ada error validasi
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            const ctx = @json(old('context'));
            if (hasErrors && ctx === 'create') {
                this.$dispatch('open-modal', 'river-basin-create');
            }
            if (hasErrors && ctx === 'edit') {
                this.editData = {
                    id: @json(old('id')),
                    name: @json(old('name')) ?? '',
                    code: @json(old('code')) ?? ''
                };
                if (this.editData.id) {
                    this.editAction = `${window.location.origin}/admin/master/river-basins/${this.editData.id}`;
                }
                this.$dispatch('open-modal', 'river-basin-edit');
            }

            // Listener untuk aksi edit dari tabel
            window.addEventListener('open-edit-river-basin', (e) => {
                const item = e.detail || {};
                this.openEdit(item);
            });
            window.addEventListener('rb:edit', (e) => {
                const item = e.detail || {};
                this.openEdit(item);
            });
        },
        openCreate() {
            this.$dispatch('open-modal', 'river-basin-create');
        },
        openEdit(item) {
            this.editData = { id: item.id, name: item.name, code: item.code };
            this.editAction = `${window.location.origin}/admin/master/river-basins/${item.id}`;
            this.$dispatch('open-modal', 'river-basin-edit');
        }
    }
}
</script>
@endpush
@endsection


