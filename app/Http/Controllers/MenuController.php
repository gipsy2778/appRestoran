<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::all();
        return view('manager.menu', compact('menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string',
            'harga'     => 'required|numeric|min:0',
        ]);

        Menu::create($request->all());
        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string',
            'harga'     => 'required|numeric|min:0',
        ]);

        $menu->update($request->all());
        return back()->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }
}