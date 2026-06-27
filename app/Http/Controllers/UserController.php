<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     * Hanya bisa diakses oleh superadmin.
     */
    public function index()
    {
        // Cek apakah user yang login adalah superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengakses halaman ini.');
        }

        $users = User::with('cabang')->orderBy('id_user', 'desc')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $cabangs = Cabang::all();
        return view('users.create', compact('cabangs'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,manager,staff',
            'cabang_id' => 'nullable|exists:dim_cabang,id_cabang',
            'email' => 'nullable|email|unique:users,email',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'cabang_id' => $request->cabang_id,
            'email' => $request->email,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);
        $cabangs = Cabang::all();

        // Superadmin tidak boleh diedit oleh siapapun (termasuk dirinya sendiri?)
        // Kita biarkan untuk keamanan, tapi bisa diubah jika perlu.
        // if ($user->role === 'superadmin' && $user->id_user !== Auth::id()) {
        //     abort(403, 'Tidak dapat mengedit superadmin lain.');
        // }

        return view('users.edit', compact('user', 'cabangs'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',id_user',
            'role' => 'required|in:superadmin,manager,staff',
            'cabang_id' => 'nullable|exists:dim_cabang,id_cabang',
            'email' => 'nullable|email|unique:users,email,' . $id . ',id_user',
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
            'cabang_id' => $request->cabang_id,
            'email' => $request->email,
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        // Jangan izinkan menghapus diri sendiri
        if ($user->id_user === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Jangan izinkan menghapus superadmin lain (opsional)
        // if ($user->role === 'superadmin') {
        //     return redirect()->route('users.index')
        //         ->with('error', 'Tidak dapat menghapus superadmin.');
        // }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}