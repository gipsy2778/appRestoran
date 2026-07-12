<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\ResepDetail;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    public function simpan(Request $request, $menuId)
    {
        $request->validate([
            'bahan_id'   => 'required|array|min:1',
            'bahan_id.*' => 'exists:bahan_baku,id',
            'jumlah'     => 'required|array',
            'jumlah.*'   => 'numeric|min:0.01',
        ]);

        // Cek duplikasi bahan dalam satu resep
        if (count($request->bahan_id) !== count(array_unique($request->bahan_id))) {
            return back()->with('error', 'Terdapat bahan yang sama dalam resep.');
        }

        // Hapus resep lama lalu simpan yang baru
        ResepDetail::where('menu_id', $menuId)->delete();

        foreach ($request->bahan_id as $index => $bahanId) {
            ResepDetail::create([
                'menu_id'  => $menuId,
                'bahan_id' => $bahanId,
                'jumlah'   => $request->jumlah[$index],
            ]);
        }

        return back()->with('success', 'Resep berhasil disimpan.');
    }
}