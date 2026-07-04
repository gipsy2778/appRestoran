<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::all();
        return view('manager.pengguna', compact('pengguna'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6',
            'role'     => 'required|in:manager,kasir',
        ]);

        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'password' => $request->password,
            'role'     => $request->role,
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama'     => 'required|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role'     => 'required|in:manager,kasir',
        ]);

        $data = [
            'nama'     => $request->nama,
            'username' => $request->username,
            'role'     => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = $request->password;
        }

        $user->update($data);
        return back()->with('success', 'Pengguna berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}