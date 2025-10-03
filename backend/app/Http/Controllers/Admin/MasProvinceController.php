<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasProvinceController extends Controller
{
    public function index(Request $request)
    {
        $query = MasProvince::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('provinces_name', 'like', '%' . $search . '%')
                    ->orWhere('provinces_code', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $provinces = $query->orderBy('provinces_name', 'asc')->paginate($perPage);

        return view('admin.mas_provinces.index', compact('provinces'));
    }

    public function create()
    {
        return view('admin.mas_provinces.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provinces_name' => 'required|string|max:255|unique:mas_provinces,provinces_name',
            'provinces_code' => 'required|string|max:10|unique:mas_provinces,provinces_code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasProvince::create($request->only(['provinces_name', 'provinces_code']));

        return redirect()->route('admin.mas-provinces.index')
            ->with('success', 'Province berhasil ditambahkan');
    }

    public function show(MasProvince $masProvince)
    {
        return view('admin.mas_provinces.show', compact('masProvince'));
    }

    public function edit(MasProvince $masProvince)
    {
        return view('admin.mas_provinces.edit', compact('masProvince'));
    }

    public function update(Request $request, MasProvince $masProvince)
    {
        $validator = Validator::make($request->all(), [
            'provinces_name' => 'required|string|max:255|unique:mas_provinces,provinces_name,' . $masProvince->id,
            'provinces_code' => 'required|string|max:10|unique:mas_provinces,provinces_code,' . $masProvince->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $masProvince->update($request->only(['provinces_name', 'provinces_code']));

        return redirect()->route('admin.mas-provinces.index')
            ->with('success', 'Province berhasil diperbarui');
    }

    public function destroy(MasProvince $masProvince)
    {
        try {
            $masProvince->delete();
            return redirect()->route('admin.mas-provinces.index')
                ->with('success', 'Province berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.mas-provinces.index')
                ->with('error', 'Province tidak dapat dihapus karena masih memiliki data terkait');
        }
    }
}