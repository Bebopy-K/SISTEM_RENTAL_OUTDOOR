<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // =============================================
    // 1. INDEX (Daftar User)
    // =============================================
    public function index()
    {
        $user = Auth::user();
        $query = User::with('cabang');

        if ($user->role === 'manager') {
            // Manager hanya melihat staff di cabangnya
            $query->where('cabang_id', $user->cabang_id)
                  ->where('role', 'staff');
        }
        // Superadmin melihat semua user

        $users = $query->orderBy('id_user', 'desc')->get();
        return view('users.index', compact('users'));
    }

    // =============================================
    // 2. CREATE (Form Tambah User)
    // =============================================
    public function create()
    {
        $user = Auth::user();
        $cabangs = Cabang::all();

        if ($user->role === 'manager') {
            // Manager hanya bisa menambah staff di cabangnya sendiri
            $cabangs = Cabang::where('id_cabang', $user->cabang_id)->get();
            return view('users.create', compact('cabangs'))->with('restricted_role', 'staff');
        }

        return view('users.create', compact('cabangs'))->with('restricted_role', null);
    }

    // =============================================
    // 3. STORE (Simpan User Baru)
    // =============================================
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'cabang_id' => 'nullable|exists:cabang,id_cabang',
            'email' => 'nullable|email|unique:users,email',
        ];

        if ($user->role === 'manager') {
            $rules['role'] = 'required|in:staff';
            $request->merge(['cabang_id' => $user->cabang_id]);
        } else {
            $rules['role'] = 'required|in:superadmin,manager,staff';
        }

        $request->validate($rules);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'staff',
            'cabang_id' => $request->cabang_id,
            'email' => $request->email,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    // =============================================
    // 4. EDIT (Form Edit User)
    // =============================================
    public function edit($id)
    {
        $user = Auth::user();
        $targetUser = User::findOrFail($id);
        $cabangs = Cabang::all();

        if ($user->role === 'manager') {
            if ($targetUser->role !== 'staff' || $targetUser->cabang_id !== $user->cabang_id) {
                abort(403, 'Anda hanya dapat mengedit staff di cabang Anda.');
            }
            $cabangs = Cabang::where('id_cabang', $user->cabang_id)->get();
            return view('users.edit', compact('targetUser', 'cabangs'))->with('restricted_role', 'staff');
        }

        return view('users.edit', compact('targetUser', 'cabangs'))->with('restricted_role', null);
    }

    // =============================================
    // 5. UPDATE (Update User)
    // =============================================
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $targetUser = User::findOrFail($id);

        $rules = [
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',id_user',
            'cabang_id' => 'nullable|exists:cabang,id_cabang',
            'email' => 'nullable|email|unique:users,email,' . $id . ',id_user',
        ];

        if ($user->role === 'manager') {
            if ($targetUser->role !== 'staff' || $targetUser->cabang_id !== $user->cabang_id) {
                abort(403, 'Anda tidak memiliki akses ke user ini.');
            }
            $rules['role'] = 'required|in:staff';
            $request->merge(['cabang_id' => $user->cabang_id]);
        } else {
            $rules['role'] = 'required|in:superadmin,manager,staff';
        }

        $request->validate($rules);

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

        $targetUser->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    // =============================================
    // 6. DESTROY (Hapus User)
    // =============================================
    public function destroy($id)
    {
        $user = Auth::user();
        $targetUser = User::findOrFail($id);

        if ($targetUser->id_user === $user->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->role === 'manager') {
            if ($targetUser->role !== 'staff' || $targetUser->cabang_id !== $user->cabang_id) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus user ini.');
            }
        }

        if ($user->role === 'superadmin' && $targetUser->role === 'superadmin' && $targetUser->id_user !== $user->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus superadmin lain.');
        }

        $targetUser->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}