<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Batch;

class MenuPublikController extends Controller
{
    public function index()
    {
        $menu = Menu::with('resepDetail.bahanBaku')->get();

        foreach ($menu as $m) {
            if ($m->resepDetail->count() === 0) {
                $m->maks_porsi = 0;
                continue;
            }

            $maksPorsi = PHP_INT_MAX;
            foreach ($m->resepDetail as $resep) {
                $stokTotal = Batch::where('bahan_id', $resep->bahan_id)
                    ->where('status', 'aktif')
                    ->sum('qty_sisa');

                if ($resep->jumlah > 0) {
                    $porsi = (int) floor($stokTotal / $resep->jumlah);
                    $maksPorsi = min($maksPorsi, $porsi);
                }
            }

            $m->maks_porsi = $maksPorsi === PHP_INT_MAX ? 0 : $maksPorsi;
        }

        return view('publik.menu', compact('menu'));
    }

    public function qrcode()
    {
        $url = url('/menu-publik');
        return view('manager.qrcode', compact('url'));
    }
}