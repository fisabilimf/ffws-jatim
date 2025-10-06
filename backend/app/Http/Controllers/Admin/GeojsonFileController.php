<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeojsonFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeojsonFileController extends Controller
{
    /**
     * Display a listing of the geojson files.
     */
    public function index(Request $request)
    {
        $query = GeojsonFile::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('original_name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $files = $query->latest()->paginate($perPage);
        
        // Define table headers
        $tableHeaders = [
            [
                'key' => 'id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'original_name',
                'label' => 'Nama File',
                'sortable' => true
            ],
            [
                'key' => 'label',
                'label' => 'Label',
                'sortable' => true
            ],
            [
                'key' => 'size',
                'label' => 'Ukuran',
                'sortable' => true,
                'format' => 'filesize'
            ],
            [
                'key' => 'mime_type',
                'label' => 'Tipe File',
                'sortable' => true
            ],
            [
                'key' => 'created_at',
                'label' => 'Tanggal Upload',
                'sortable' => true,
                'format' => 'date'
            ],
            [
                'key' => 'actions',
                'label' => 'Aksi',
                'sortable' => false,
                'format' => 'actions'
            ]
        ];
        
        // Add actions for each file
        $files->getCollection()->transform(function ($file) {
            $file->actions = [
                [
                    'label' => 'Lihat',
                    'url' => route('admin.geojson-files.show', $file),
                    'color' => 'blue'
                ],
                [
                    'label' => 'Edit',
                    'url' => route('admin.geojson-files.edit', $file),
                    'color' => 'yellow'
                ],
                [
                    'label' => 'Hapus',
                    'url' => route('admin.geojson-files.destroy', $file),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'confirm' => 'Apakah Anda yakin ingin menghapus file ini?'
                ]
            ];
            return $file;
        });
        
        return view('admin.geojson_file.index', compact('files', 'tableHeaders'));
    }

    /**
     * Show the form for creating a new geojson file.
     */
    public function create()
    {
        return view('admin.geojson_file.create');
    }

    /**
     * Store a newly created geojson file in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json,geojson|max:1501200', // 50MB max
            'label' => 'nullable|string|max:100',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads/geojson', 'public');
        
        // Generate SHA256 hash for file integrity
        $sha256 = hash_file('sha256', $file->getPathname());
        
        $geojsonFile = GeojsonFile::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'disk' => 'public',
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'sha256' => $sha256,
            'label' => $request->input('label'),
        ]);

        return redirect()->route('admin.geojson-files.index')
            ->with('success', "File {$geojsonFile->original_name} berhasil diupload");
    }

    /**
     * Display the specified geojson file.
     */
    public function show(GeojsonFile $geojsonFile)
    {
        return view('admin.geojson_file.show', compact('geojsonFile'));
    }

    /**
     * Show the form for editing the specified geojson file.
     */
    public function edit(GeojsonFile $geojsonFile)
    {
        return view('admin.geojson_file.edit', compact('geojsonFile'));
    }

    /**
     * Update the specified geojson file in storage.
     */
    public function update(Request $request, GeojsonFile $geojsonFile)
    {
        $request->validate([
            'label' => 'nullable|string|max:100',
        ]);

        $geojsonFile->update([
            'label' => $request->input('label'),
        ]);

        return redirect()->route('admin.geojson-files.index')
            ->with('success', "File {$geojsonFile->original_name} berhasil diperbarui");
    }

    /**
     * Remove the specified geojson file from storage.
     */
    public function destroy(GeojsonFile $geojsonFile)
    {
        // Delete file from storage
        if (Storage::disk($geojsonFile->disk)->exists($geojsonFile->stored_path)) {
            Storage::disk($geojsonFile->disk)->delete($geojsonFile->stored_path);
        }

        // Delete record from database
        $geojsonFile->delete();

        return redirect()->route('admin.geojson-files.index')
            ->with('success', "File {$geojsonFile->original_name} berhasil dihapus");
    }

    /**
     * Download the specified geojson file.
     */
    public function download(GeojsonFile $geojsonFile)
    {
        if (!Storage::disk($geojsonFile->disk)->exists($geojsonFile->stored_path)) {
            return redirect()->route('admin.geojson-files.index')
                ->with('error', 'File tidak ditemukan');
        }

        return response()->download(
            Storage::disk($geojsonFile->disk)->path($geojsonFile->stored_path),
            $geojsonFile->original_name
        );
    }
}
