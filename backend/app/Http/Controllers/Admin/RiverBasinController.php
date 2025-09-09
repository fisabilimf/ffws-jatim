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
            ['key' => 'name', 'label' => 'Nama DAS', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Dibuat', 'sortable' => true, 'format' => 'date'],
            ['key' => 'actions', 'label' => 'Aksi', 'sortable' => false, 'format' => 'actions'],
        ];

        // Tambahkan actions untuk setiap baris (Detail, Edit, Hapus) dengan ikon
        $riverBasins->getCollection()->transform(function ($rb) {
            $rb->actions = [
                [
                    'label' => 'Detail',
                    'title' => 'Detail',
                    'url' => route('admin.master.river-basins.edit', $rb->id), // ganti ke show jika tersedia
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-river-basin', { detail: { id: {$rb->id}, name: '{$rb->name}', code: '{$rb->code}' } }))",
                    'icon' => 'eye',
                ],
                [
                    'label' => 'Edit',
                    'title' => 'Edit',
                    'url' => route('admin.master.river-basins.edit', $rb->id),
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-river-basin', { detail: { id: {$rb->id}, name: '{$rb->name}', code: '{$rb->code}' } }))",
                    'icon' => 'pen',
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus',
                    'url' => route('admin.master.river-basins.destroy', $rb->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus DAS ini?'
                ],
            ];
            return $rb;
        });

        return view('admin.river_basins.index', compact('riverBasins', 'tableHeaders'));
    }

    public function create(): View
    {
        return view('admin.river_basins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:mas_river_basins,code'],
        ]);

        MasRiverBasin::create($validated);

        return redirect()->route('admin.river-basins.index')
            ->with('success', 'DAS berhasil ditambahkan.');
    }

    public function edit(MasRiverBasin $river_basin): View
    {
        return view('admin.river_basins.edit', [
            'riverBasin' => $river_basin,
        ]);
    }

    public function update(Request $request, MasRiverBasin $river_basin): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:mas_river_basins,code,' . $river_basin->id],
        ]);

        $river_basin->update($validated);

        return redirect()->route('admin.river-basins.index')
            ->with('success', 'DAS berhasil diperbarui.');
    }

    public function destroy(MasRiverBasin $river_basin): RedirectResponse
    {
        $river_basin->delete();

        return redirect()->route('admin.river-basins.index')
            ->with('success', 'DAS berhasil dihapus.');
    }
}


