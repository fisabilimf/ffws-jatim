<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasRegency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasRegencyController extends Controller
{
    public function index(Request $request)
    {
        $query = MasRegency::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('regencies_name', 'like', '%' . $search . '%')
                    ->orWhere('regencies_code', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $regencies = $query->orderBy('regencies_name', 'asc')->paginate($perPage);

        return view('admin.mas_regencies.index', compact('regencies'));
    }

    public function create()
    {
        return view('admin.mas_regencies.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'regencies_name' => 'required|string|max:255|unique:mas_regencies,regencies_name',
            'regencies_code' => 'required|string|max:10|unique:mas_regencies,regencies_code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasRegency::create($request->only(['regencies_name', 'regencies_code']));

        return redirect()->route('admin.mas-regencies.index')
            ->with('success', 'Regency berhasil ditambahkan');
    }

    public function show(MasRegency $masRegency)
    {
        return view('admin.mas_regencies.show', compact('masRegency'));
    }

    public function edit(MasRegency $masRegency)
    {
        return view('admin.mas_regencies.edit', compact('masRegency'));
    }

    public function update(Request $request, MasRegency $masRegency)
    {
        $validator = Validator::make($request->all(), [
            'regencies_name' => 'required|string|max:255|unique:mas_regencies,regencies_name,' . $masRegency->id,
            'regencies_code' => 'required|string|max:10|unique:mas_regencies,regencies_code,' . $masRegency->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $masRegency->update($request->only(['regencies_name', 'regencies_code']));

        return redirect()->route('admin.mas-regencies.index')
            ->with('success', 'Regency berhasil diperbarui');
    }

    public function destroy(MasRegency $masRegency)
    {
        try {
            $masRegency->delete();
            return redirect()->route('admin.mas-regencies.index')
                ->with('success', 'Regency berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.mas-regencies.index')
                ->with('error', 'Regency tidak dapat dihapus karena masih memiliki data terkait');
        }
    }
}