<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBaku = BahanBaku::all();
        return view('manager.bahan_baku', compact('bahanBaku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan'    => 'required|string',
            'jenis'         => 'required|in:mudah_rusak,tahan_lama',
            'satuan'        => 'required|string',
            'stok_minimum'  => 'required|numeric|min:0',
        ]);

        BahanBaku::create($request->all());
        return back()->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            'nama_bahan'    => 'required|string',
            'jenis'         => 'required|in:mudah_rusak,tahan_lama',
            'satuan'        => 'required|string',
            'stok_minimum'  => 'required|numeric|min:0',
        ]);

        $bahanBaku->update($request->all());
        return back()->with('success', 'Bahan baku berhasil diupdate.');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();
        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}