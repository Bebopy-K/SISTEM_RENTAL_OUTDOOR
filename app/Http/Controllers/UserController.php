<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengakses halaman ini.');
        }

        $users = User::with('cabang')->orderBy('id_user', 'desc')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $cabangs = Cabang::all(); // ← PASTIKAN MODEL Cabang
        return view('users.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,manager,staff',
            'cabang_id' => 'nullable|exists:cabang,id_cabang', // ← DIUBAH
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

    public function edit($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);
        $cabangs = Cabang::all(); // ← PASTIKAN MODEL Cabang

        return view('users.edit', compact('user', 'cabangs'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',id_user',
            'role' => 'required|in:superadmin,manager,staff',
            'cabang_id' => 'nullable|exists:cabang,id_cabang', // ← DIUBAH
            'email' => 'nullable|email|unique:users,email,' . $id . ',id_user',
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
            'cabang_id' => $request->cabang_id,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        if ($user->id_user === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}