<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasUpt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasUptController extends Controller
{
    public function index(Request $request)
    {
        $query = MasUpt::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('upts_name', 'like', "%{$search}%")
                  ->orWhere('upts_code', 'like', "%{$search}%");
            });
        }

        $upts = $query->orderBy('upts_name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'upts_name', 'label' => 'Nama UPT', 'sortable' => true],
            ['key' => 'upts_code', 'label' => 'Kode UPT', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform UPTs data for table
        $upts->getCollection()->transform(function ($upt) {
            $detailData = [
                'id' => $upt->id,
                'upts_name' => addslashes($upt->upts_name),
                'upts_code' => addslashes($upt->upts_code ?? '')
            ];
            $detailJson = json_encode($detailData);

            $upt->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit UPT',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-upt', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus UPT',
                    'url' => route('admin.mas-upts.destroy', $upt->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus UPT ini?'
                ]
            ];
            
            return $upt;
        });
        
        return view('admin.mas_upts.index', compact('upts', 'tableHeaders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'upts_name' => 'required|string|max:255|unique:mas_upts,upts_name',
                'upts_code' => 'required|string|max:50|unique:mas_upts,upts_code',
                'upts_address' => 'nullable|string|max:500'
            ]);

            $upt = MasUpt::create($validated);

            return redirect()->route('admin.mas-upts.index')
                ->with('success', "UPT '{$upt->upts_name}' berhasil ditambahkan.");
                
        } catch (\Exception $e) {
            Log::error('Error creating UPT: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-upts.index')
                ->with('error', 'Terjadi kesalahan saat menambahkan UPT. Silakan coba lagi.');
        }
    }

    public function update(Request $request, MasUpt $masUpt)
    {
        try {
            $validated = $request->validate([
                'upts_name' => 'required|string|max:255|unique:mas_upts,upts_name,' . $masUpt->id,
                'upts_code' => 'required|string|max:50|unique:mas_upts,upts_code,' . $masUpt->id,
                'upts_address' => 'nullable|string|max:500'
            ]);

            $masUpt->update($validated);

            return redirect()->route('admin.mas-upts.index')
                ->with('success', "UPT '{$masUpt->upts_name}' berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error('Error updating UPT: ' . $e->getMessage(), [
                'upt_id' => $masUpt->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-upts.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui UPT. Silakan coba lagi.');
        }
    }

    public function destroy(MasUpt $masUpt)
    {
        try {
            DB::beginTransaction();

            $uptName = $masUpt->upts_name;
            $masUpt->delete();

            DB::commit();

            return redirect()->route('admin.mas-upts.index')
                ->with('success', "UPT '{$uptName}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error deleting UPT: ' . $e->getMessage(), [
                'upt_id' => $masUpt->id,
                'upt_name' => $masUpt->upts_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-upts.index')
                ->with('error', 'UPT tidak dapat dihapus karena masih memiliki data terkait atau terjadi kesalahan sistem.');
        }
    }
}