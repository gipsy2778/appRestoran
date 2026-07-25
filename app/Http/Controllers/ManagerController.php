<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $today = \Carbon\Carbon::today();

        // Kartu ringkasan
        $totalMenu = \App\Models\Menu::count();
        $totalBahanBaku = \App\Models\BahanBaku::count();

        $transaksiHariIni = \App\Models\Transaksi::where('status', 'selesai')
            ->whereDate('created_at', $today);
        $jumlahTransaksiHariIni = (clone $transaksiHariIni)->count();
        $pendapatanHariIni = (clone $transaksiHariIni)->sum('total_harga');

        $notifikasiBelumDibaca = Notifikasi::where('user_id', auth()->id())
            ->where('status', 'belum_dibaca')
            ->count();

        // Bahan stok kritis (stok total <= stok minimum), untuk preview cepat
        $stokKritis = \App\Models\BahanBaku::with(['batch' => function ($q) {
            $q->where('status', 'aktif');
        }])->get()
            ->map(function ($bahan) {
                $bahan->stok_total = $bahan->batch->sum('qty_sisa');
                return $bahan;
            })
            ->filter(fn($bahan) => $bahan->stok_total <= $bahan->stok_minimum)
            ->take(5);

        // Batch mendekati kedaluwarsa (H-3) atau sudah lewat, untuk preview cepat
        $batchMendekatiExpired = \App\Models\Batch::with('bahanBaku')
            ->where('status', 'aktif')
            ->where('qty_sisa', '>', 0)
            ->whereDate('tanggal_expired', '<=', $today->copy()->addDays(3))
            ->orderBy('tanggal_expired', 'asc')
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
            'totalMenu', 'totalBahanBaku',
            'jumlahTransaksiHariIni', 'pendapatanHariIni',
            'notifikasiBelumDibaca', 'stokKritis', 'batchMendekatiExpired'
        ));
    }

    public function riwayatIndex()
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

        return view('manager.riwayat', compact('transaksi', 'foodWastage', 'stok'));
    }

    public function laporanAnalitik(\Illuminate\Http\Request $request)
    {
        // Tentukan rentang tanggal berdasarkan filter periode
        $periode = $request->get('periode', 'bulan_ini');
        $now = \Carbon\Carbon::now();

        switch ($periode) {
            case 'hari_ini':
                $dari = $now->copy()->startOfDay();
                $sampai = $now->copy()->endOfDay();
                break;
            case 'minggu_ini':
                $dari = $now->copy()->startOfWeek();
                $sampai = $now->copy()->endOfWeek();
                break;
            case 'semua':
                $dari = null;
                $sampai = null;
                break;
            case 'custom':
                $dari = $request->dari ? \Carbon\Carbon::parse($request->dari)->startOfDay() : $now->copy()->startOfMonth();
                $sampai = $request->sampai ? \Carbon\Carbon::parse($request->sampai)->endOfDay() : $now->copy()->endOfDay();
                break;
            case 'bulan_ini':
            default:
                $dari = $now->copy()->startOfMonth();
                $sampai = $now->copy()->endOfMonth();
                break;
        }

        // ==== Ringkasan Laba/Rugi ====
        $transaksiQuery = \App\Models\Transaksi::where('status', 'selesai');
        if ($dari && $sampai) {
            $transaksiQuery->whereBetween('created_at', [$dari, $sampai]);
        }

        $totalPendapatan = (clone $transaksiQuery)->sum('total_harga');
        $totalHpp        = (clone $transaksiQuery)->sum('total_hpp');
        $labaKotor       = $totalPendapatan - $totalHpp;
        $jumlahTransaksi = (clone $transaksiQuery)->count();

        // ==== Menu Paling Laris ====
        $menuLaris = \App\Models\TransaksiDetail::select('menu_id', 'nama_menu')
            ->selectRaw('SUM(qty) as total_qty, SUM(subtotal) as total_pendapatan, SUM(hpp) as total_hpp')
            ->whereHas('transaksi', function ($q) use ($dari, $sampai) {
                $q->where('status', 'selesai');
                if ($dari && $sampai) {
                    $q->whereBetween('created_at', [$dari, $sampai]);
                }
            })
            ->groupBy('menu_id', 'nama_menu')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->margin = $item->total_pendapatan - $item->total_hpp;
                $item->margin_per_porsi = $item->total_qty > 0 ? $item->margin / $item->total_qty : 0;
                return $item;
            });

        // ==== Bahan Baku Paling Sering Dibuang ====
        $wastageQuery = \App\Models\FoodWastage::with('batch.bahanBaku');
        if ($dari && $sampai) {
            $wastageQuery->whereBetween('created_at', [$dari, $sampai]);
        }

        $bahanTerbuang = $wastageQuery->get()
            ->filter(fn($fw) => $fw->batch !== null)
            ->groupBy(fn($fw) => $fw->batch->bahan_id)
            ->map(function ($group) {
                $first = $group->first();
                $totalJumlah = $group->sum('jumlah');
                $totalKerugian = $group->sum(fn($fw) => $fw->jumlah * $fw->batch->harga_beli);
                return (object) [
                    'nama_bahan'     => $first->batch->bahanBaku->nama_bahan ?? '-',
                    'satuan'         => $first->batch->bahanBaku->satuan ?? '',
                    'total_jumlah'   => $totalJumlah,
                    'total_kerugian' => $totalKerugian,
                    'jumlah_laporan' => $group->count(),
                ];
            })
            ->sortByDesc('total_jumlah')
            ->take(10)
            ->values();

        return view('manager.laporan', compact(
            'periode', 'dari', 'sampai',
            'totalPendapatan', 'totalHpp', 'labaKotor', 'jumlahTransaksi',
            'menuLaris', 'bahanTerbuang'
        ));
    }
}