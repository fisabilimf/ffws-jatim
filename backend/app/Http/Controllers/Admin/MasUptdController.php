<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasUptd;
use App\Models\MasUpt;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasUptdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasUptd::with(['upt']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('upt', function ($q) use ($search) {
                      $q->where('upts_name', 'like', "%{$search}%")
                        ->orWhere('upts_code', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by UPT
        if ($request->filled('upt_code')) {
            $query->where('upt_code', $request->get('upt_code'));
        }

        $uptds = $query->orderBy('name')
                      ->paginate(15)
                      ->withQueryString();

        // Get filter options
        $upts = MasUpt::orderBy('upts_name')->get();

        return view('admin.mas_uptds.index', compact('uptds', 'upts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $upts = MasUpt::orderBy('upts_name')->get();
        
        return view('admin.mas_uptds.create', compact('upts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mas_uptds,code',
            'name' => 'required|string|max:255',
            'upt_code' => 'required|string|exists:mas_upts,upts_code',
        ]);

        MasUptd::create($validated);

        return redirect()->route('admin.mas-uptds.index')
                        ->with('success', 'UPTD created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasUptd $masUptd): View
    {
        $masUptd->load(['upt']);
        
        return view('admin.mas_uptds.show', compact('masUptd'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasUptd $masUptd): View
    {
        $upts = MasUpt::orderBy('upts_name')->get();
        
        return view('admin.mas_uptds.edit', compact('masUptd', 'upts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasUptd $masUptd): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mas_uptds,code,' . $masUptd->id,
            'name' => 'required|string|max:255',
            'upt_code' => 'required|string|exists:mas_upts,upts_code',
        ]);

        $masUptd->update($validated);

        return redirect()->route('admin.mas-uptds.index')
                        ->with('success', 'UPTD updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasUptd $masUptd): RedirectResponse
    {
        $masUptd->delete();

        return redirect()->route('admin.mas_uptds.index')
                        ->with('success', 'UPTD deleted successfully.');
    }
}