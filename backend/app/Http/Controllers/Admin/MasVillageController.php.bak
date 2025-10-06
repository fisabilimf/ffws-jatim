<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasVillageController extends Controller
{
    public function index(Request $request)
    {
        $query = MasVillage::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('villages_name', 'like', '%' . $search . '%')
                    ->orWhere('villages_code', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $villages = $query->orderBy('villages_name', 'asc')->paginate($perPage);

        return view('admin.mas_villages.index', compact('villages'));
    }

    public function create()
    {
        return view('admin.mas_villages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'villages_name' => 'required|string|max:255',
            'villages_code' => 'required|string|max:10|unique:mas_villages,villages_code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasVillage::create($request->only(['villages_name', 'villages_code']));

        return redirect()->route('admin.mas-villages.index')
            ->with('success', 'Village berhasil ditambahkan');
    }

    public function show(MasVillage $masVillage)
    {
        return view('admin.mas_villages.show', compact('masVillage'));
    }

    public function edit(MasVillage $masVillage)
    {
        return view('admin.mas_villages.edit', compact('masVillage'));
    }

    public function update(Request $request, MasVillage $masVillage)
    {
        $validator = Validator::make($request->all(), [
            'villages_name' => 'required|string|max:255',
            'villages_code' => 'required|string|max:10|unique:mas_villages,villages_code,' . $masVillage->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $masVillage->update($request->only(['villages_name', 'villages_code']));

        return redirect()->route('admin.mas-villages.index')
            ->with('success', 'Village berhasil diperbarui');
    }

    public function destroy(MasVillage $masVillage)
    {
        try {
            $masVillage->delete();
            return redirect()->route('admin.mas-villages.index')
                ->with('success', 'Village berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.mas-villages.index')
                ->with('error', 'Village tidak dapat dihapus karena masih memiliki data terkait');
        }
    }
}