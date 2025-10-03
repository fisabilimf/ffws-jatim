<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasCity;
use App\Models\MasProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasCityController extends Controller
{
    public function index(Request $request)
    {
        $query = MasCity::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('cities_name', 'like', '%' . $search . '%')
                    ->orWhere('cities_code', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $cities = $query->orderBy('cities_name', 'asc')->paginate($perPage);

        return view('admin.mas_cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.mas_cities.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cities_name' => 'required|string|max:255',
            'cities_code' => 'required|string|max:10|unique:mas_cities,cities_code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasCity::create($request->only(['cities_name', 'cities_code']));

        return redirect()->route('admin.mas-cities.index')
            ->with('success', 'City berhasil ditambahkan');
    }

    public function show(MasCity $masCity)
    {
        // Load only relationships that definitely exist and won't cause issues
        $masCity->load(['upts']);
        return view('admin.mas_cities.show', compact('masCity'));
    }

    public function edit(MasCity $masCity)
    {
        return view('admin.mas_cities.edit', compact('masCity'));
    }

    public function update(Request $request, MasCity $masCity)
    {
        $validator = Validator::make($request->all(), [
            'cities_name' => 'required|string|max:255',
            'cities_code' => 'required|string|max:10|unique:mas_cities,cities_code,' . $masCity->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $masCity->update($request->only(['cities_name', 'cities_code']));

        return redirect()->route('admin.mas-cities.index')
            ->with('success', 'City berhasil diperbarui');
    }

    public function destroy(MasCity $masCity)
    {
        try {
            $masCity->delete();
            return redirect()->route('admin.mas-cities.index')
                ->with('success', 'City berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.mas-cities.index')
                ->with('error', 'City tidak dapat dihapus karena masih memiliki data terkait');
        }
    }
}