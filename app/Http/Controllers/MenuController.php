<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::with('resepDetail.bahanBaku')->get();
        $bahanBaku = BahanBaku::all();
        return view('manager.menu', compact('menu', 'bahanBaku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string',
            'harga'     => 'required|numeric|min:0',
            'gambar'    => 'nullable|image|max:2048',
        ]);

        $data = $request->only('nama_menu', 'harga');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('menu', 'public');
        }

        Menu::create($data);
        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string',
            'harga'     => 'required|numeric|min:0',
            'gambar'    => 'nullable|image|max:2048',
        ]);

        $data = $request->only('nama_menu', 'harga');

        if ($request->hasFile('gambar')) {
            if ($menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('menu', 'public');
        }

        $menu->update($data);
        return back()->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->gambar) {
            Storage::disk('public')->delete($menu->gambar);
        }
        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }
}