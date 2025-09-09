<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasRiverBasin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiverBasinController extends Controller
{
    public function index(Request $request): View
    {
        $query = MasRiverBasin::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $riverBasins = $query->orderBy('name')->paginate(10)->withQueryString();

        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama Aliran Sungai', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Dibuat', 'sortable' => true, 'format' => 'date'],
            ['key' => 'actions', 'label' => 'Aksi', 'sortable' => false, 'format' => 'actions'],
        ];

        // Tambahkan actions untuk setiap baris (Edit, Hapus)
        $riverBasins->getCollection()->transform(function ($rb) {
            $detailData = [
                'id' => $rb->id,
                'name' => addslashes($rb->name),
                'code' => addslashes($rb->code),
            ];
            $detailJson = json_encode($detailData);

            $rb->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Aliran Sungai',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-river-basin', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Aliran Sungai',
                    'url' => route('admin.wilayah.river-basins.destroy', $rb->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus Aliran Sungai ini?'
                ],
            ];
            return $rb;
        });

        return view('admin.river_basins.index', compact('riverBasins', 'tableHeaders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:mas_river_basins,name'],
            'code' => ['required', 'string', 'max:255', 'unique:mas_river_basins,code'],
        ], [
            'name.required' => 'Nama DAS wajib diisi.',
            'name.string' => 'Nama DAS harus berupa teks.',
            'name.max' => 'Nama DAS maksimal 255 karakter.',
            'name.unique' => 'Nama DAS sudah digunakan. Silakan gunakan nama yang berbeda.',
            'code.required' => 'Kode DAS wajib diisi.',
            'code.string' => 'Kode DAS harus berupa teks.',
            'code.max' => 'Kode DAS maksimal 255 karakter.',
            'code.unique' => 'Kode DAS sudah digunakan. Silakan gunakan kode yang berbeda.',
        ]);

        try {
            MasRiverBasin::create($validated);
            return redirect()->route('admin.wilayah.river-basins.index')
                ->with('success', 'Daerah Aliran Sungai berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, MasRiverBasin $river_basin): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:mas_river_basins,name,' . $river_basin->id],
            'code' => ['required', 'string', 'max:255', 'unique:mas_river_basins,code,' . $river_basin->id],
        ], [
            'name.required' => 'Nama DAS wajib diisi.',
            'name.string' => 'Nama DAS harus berupa teks.',
            'name.max' => 'Nama DAS maksimal 255 karakter.',
            'name.unique' => 'Nama DAS sudah digunakan. Silakan gunakan nama yang berbeda.',
            'code.required' => 'Kode DAS wajib diisi.',
            'code.string' => 'Kode DAS harus berupa teks.',
            'code.max' => 'Kode DAS maksimal 255 karakter.',
            'code.unique' => 'Kode DAS sudah digunakan. Silakan gunakan kode yang berbeda.',
        ]);

        try {
            $river_basin->update($validated);
            return redirect()->route('admin.wilayah.river-basins.index')
                ->with('success', 'DAS berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(MasRiverBasin $river_basin): RedirectResponse
    {
        try {
            $river_basin->delete();
            return redirect()->route('admin.wilayah.river-basins.index')
                ->with('success', 'DAS berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
