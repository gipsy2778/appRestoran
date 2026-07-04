<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\ResepDetail;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    public function index()
    {
        $menu = Menu::with('resepDetail.bahanBaku')->get();
        $bahanBaku = BahanBaku::all();
        return view('manager.resep', compact('menu', 'bahanBaku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id'  => 'required|exists:menu,id',
            'bahan_id' => 'required|exists:bahan_baku,id',
            'jumlah'   => 'required|numeric|min:0',
        ]);

        // Cek apakah kombinasi menu+bahan sudah ada
        $exists = ResepDetail::where('menu_id', $request->menu_id)
            ->where('bahan_id', $request->bahan_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bahan ini sudah ada di resep menu tersebut.');
        }

        ResepDetail::create($request->all());
        return back()->with('success', 'Resep berhasil ditambahkan.');
    }

    public function destroy(ResepDetail $resepDetail)
    {
        $resepDetail->delete();
        return back()->with('success', 'Resep berhasil dihapus.');
    }
}