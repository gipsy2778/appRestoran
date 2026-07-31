<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBaku = BahanBaku::with(['batch' => function ($q) {
            $q->where('status', 'aktif');
        }])->get()->map(function ($bahan) {
            $bahan->stok_total = $bahan->batch->sum('qty_sisa');
            return $bahan;
        });

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
        $punyaBatch = \App\Models\Batch::where('bahan_id', $bahanBaku->id)->exists();
        $dipakaiResep = \App\Models\ResepDetail::where('bahan_id', $bahanBaku->id)->exists();

        if ($punyaBatch || $dipakaiResep) {
            $alasan = [];
            if ($punyaBatch) $alasan[] = 'sudah punya riwayat batch/stok';
            if ($dipakaiResep) $alasan[] = 'masih dipakai di resep menu';

            return back()->with('error',
                'Bahan "' . $bahanBaku->nama_bahan . '" tidak bisa dihapus karena ' . implode(' dan ', $alasan) . '. ' .
                'Hapus dulu batch/resep terkait, atau biarkan saja kalau memang masih dipakai.'
            );
        }

        $bahanBaku->delete();
        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}