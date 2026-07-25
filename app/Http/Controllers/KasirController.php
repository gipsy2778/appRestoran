<?php

namespace App\Http\Controllers;

class KasirController extends Controller
{
    public function dashboard()
    {
        $today = \Carbon\Carbon::today();

        $transaksiSaya = \App\Models\Transaksi::where('kasir_id', auth()->id())
            ->where('status', 'selesai')
            ->whereDate('created_at', $today);

        $jumlahTransaksiSaya = (clone $transaksiSaya)->count();
        $pendapatanSaya = (clone $transaksiSaya)->sum('total_harga');

        return view('kasir.dashboard', compact('jumlahTransaksiSaya', 'pendapatanSaya'));
    }

    public function menuIndex()
    {
        $menu = \App\Models\Menu::with('resepDetail.bahanBaku')->get();

        // Hitung maks porsi per menu
        foreach ($menu as $m) {
            if ($m->resepDetail->count() === 0) {
                $m->maks_porsi = 0;
                continue;
            }

            $maksPorsi = PHP_INT_MAX;
            foreach ($m->resepDetail as $resep) {
                $stokTotal = \App\Models\Batch::where('bahan_id', $resep->bahan_id)
                    ->where('status', 'aktif')
                    ->sum('qty_sisa');

                if ($resep->jumlah > 0) {
                    $porsi = (int) floor($stokTotal / $resep->jumlah);
                    $maksPorsi = min($maksPorsi, $porsi);
                }
            }

            $m->maks_porsi = $maksPorsi === PHP_INT_MAX ? 0 : $maksPorsi;
        }

        return view('kasir.menu', compact('menu'));
    }
}