<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Tampilkan daftar semua user
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        
        $tableHeaders = [
            [
                'key' => 'id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'name',
                'label' => 'Nama',
                'sortable' => true
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'sortable' => true
            ],
            [
                'key' => 'role',
                'label' => 'Role',
                'sortable' => true,
                'format' => 'status'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'sortable' => true,
                'format' => 'status'
            ],
            [
                'key' => 'created_at',
                'label' => 'Tanggal Dibuat',
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
        
        // Variabel untuk sorting dan searching
        $sortColumn = request('sort', 'id');
        $sortDirection = request('direction', 'asc');
        $searchQuery = request('search', '');
        
        // Jika ada search query, filter users
        if ($searchQuery) {
            $users = User::where('name', 'like', "%{$searchQuery}%")
                        ->orWhere('email', 'like', "%{$searchQuery}%")
                        ->orWhere('role', 'like', "%{$searchQuery}%")
                        ->latest()
                        ->paginate(10);
        }
        
        // Jika ada sorting, apply sorting
        if ($sortColumn && in_array($sortColumn, ['id', 'name', 'email', 'role', 'status', 'created_at'])) {
            $users = User::orderBy($sortColumn, $sortDirection)->paginate(10);
        }
        
        // Tambahkan actions untuk setiap user
        $users->getCollection()->transform(function ($user) {
            $user->actions = [
                [
                    'label' => 'Edit',
                    'url' => route('admin.users.edit', $user),
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'url' => route('admin.users.destroy', $user),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'confirm' => 'Apakah Anda yakin ingin menghapus user ini?'
                ]
            ];
            return $user;
        });
        
        return view('admin.users.index', compact('users', 'tableHeaders', 'sortColumn', 'sortDirection', 'searchQuery'));
    }

    /**
     * Tampilkan form untuk membuat user baru
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user,moderator',
            'status' => 'required|in:active,inactive,pending',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'bio' => $request->bio,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat!');
    }

    /**
     * Tampilkan form untuk edit user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user,moderator',
            'status' => 'required|in:active,inactive,pending',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'email', 'role', 'status', 'bio']));

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }
}
