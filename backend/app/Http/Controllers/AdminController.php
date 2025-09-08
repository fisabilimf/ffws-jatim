<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard admin
     */
    public function dashboard()
    {
        // Data statistik untuk dashboard
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'total_revenue' => 0, // Placeholder untuk revenue
        ];

        // User terbaru
        $recent_users = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users'));
    }

    /**
     * Tampilkan profil admin
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update profil admin
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password admin
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}


