<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class ManagerController extends Controller
{
    public function dashboard()
    {
        return view('manager.dashboard');
    }

    public function laporanIndex()
    {
        $transaksi = \App\Models\Transaksi::with('kasir', 'detail')
            ->latest()
            ->get();

        $foodWastage = \App\Models\FoodWastage::with('batch.bahanBaku', 'pelapor')
            ->latest()
            ->get();

        $stok = \App\Models\BahanBaku::with(['batch' => function($q) {
            $q->where('status', 'aktif');
        }])->get()->map(function($bahan) {
            $bahan->stok_total = $bahan->batch->sum('qty_sisa');
            return $bahan;
        });

        return view('manager.laporan', compact('transaksi', 'foodWastage', 'stok'));
    }
}