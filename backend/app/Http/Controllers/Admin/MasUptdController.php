<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasUptd;
use App\Models\MasUpt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasUptdController extends Controller
{
    public function index(Request $request)
    {
        $query = MasUptd::query()->with('upt');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($uptCode = $request->get('upt_code')) {
            $query->where('upt_code', $uptCode);
        }

        $uptds = $query->orderBy('name')->paginate(10)->withQueryString();
        
        // Get UPTs for dropdown
        $upts = MasUpt::orderBy('upts_name')->get()
            ->map(function ($upt) {
                return [
                    'value' => $upt->upts_code,
                    'label' => $upt->upts_name
                ];
            });
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama UPTD', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode UPTD', 'sortable' => true],
            ['key' => 'upt_name', 'label' => 'UPT', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform UPTDs data for table
        $uptds->getCollection()->transform(function ($uptd) {
            $detailData = [
                'id' => $uptd->id,
                'name' => addslashes($uptd->name),
                'code' => addslashes($uptd->code ?? ''),
                'upt_code' => $uptd->upt_code
            ];
            $detailJson = json_encode($detailData);

            $uptd->upt_name = $uptd->upt ? $uptd->upt->upts_name : '-';
            $uptd->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit UPTD',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-uptd', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus UPTD',
                    'url' => route('admin.mas-uptds.destroy', $uptd->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus UPTD ini?'
                ]
            ];
            
            return $uptd;
        });
        
        return view('admin.mas_uptds.index', compact('uptds', 'upts', 'tableHeaders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:mas_uptds,name',
                'code' => 'required|string|max:50|unique:mas_uptds,code',
                'upt_code' => 'required|exists:mas_upts,upts_code'
            ]);

            $uptd = MasUptd::create($validated);

            return redirect()->route('admin.mas-uptds.index')
                ->with('success', "UPTD '{$uptd->name}' berhasil ditambahkan.");
                
        } catch (\Exception $e) {
            Log::error('Error creating UPTD: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-uptds.index')
                ->with('error', 'Terjadi kesalahan saat menambahkan UPTD. Silakan coba lagi.');
        }
    }

    public function update(Request $request, MasUptd $masUptd)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:mas_uptds,name,' . $masUptd->id,
                'code' => 'required|string|max:50|unique:mas_uptds,code,' . $masUptd->id,
                'upt_code' => 'required|exists:mas_upts,upts_code'
            ]);

            $masUptd->update($validated);

            return redirect()->route('admin.mas-uptds.index')
                ->with('success', "UPTD '{$masUptd->name}' berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error('Error updating UPTD: ' . $e->getMessage(), [
                'uptd_id' => $masUptd->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-uptds.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui UPTD. Silakan coba lagi.');
        }
    }

    public function destroy(MasUptd $masUptd)
    {
        try {
            DB::beginTransaction();

            $uptdName = $masUptd->name;
            $masUptd->delete();

            DB::commit();

            return redirect()->route('admin.mas-uptds.index')
                ->with('success', "UPTD '{$uptdName}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error deleting UPTD: ' . $e->getMessage(), [
                'uptd_id' => $masUptd->id,
                'uptd_name' => $masUptd->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-uptds.index')
                ->with('error', 'UPTD tidak dapat dihapus karena masih memiliki data terkait atau terjadi kesalahan sistem.');
        }
    }
}