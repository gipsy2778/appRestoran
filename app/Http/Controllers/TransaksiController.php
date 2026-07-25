<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Batch;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\BahanBaku;
use App\Models\PemakaianBatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('kasir')->latest()->get();
        return view('kasir.transaksi', compact('transaksi'));
    }

    public function create()
    {
        $menu = Menu::with('resepDetail.bahanBaku')->get();
        return view('kasir.buat_transaksi', compact('menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu'     => 'required|array|min:1',
            'menu.*'   => 'exists:menu,id',
            'qty'      => 'required|array',
            'qty.*'    => 'integer|min:1',
        ]);

        // Cek stok untuk semua menu yang dipesan
        foreach ($request->menu as $index => $menuId) {
            $qty = $request->qty[$index];
            $menuItem = Menu::with('resepDetail.bahanBaku')->findOrFail($menuId);

            foreach ($menuItem->resepDetail as $resep) {
                $stokTotal = Batch::where('bahan_id', $resep->bahan_id)
                    ->where('status', 'aktif')
                    ->sum('qty_sisa');

                $kebutuhan = $resep->jumlah * $qty;

                if ($stokTotal < $kebutuhan) {
                    return back()->with('error', 'Stok ' . $resep->bahanBaku->nama_bahan . ' tidak cukup untuk menu ' . $menuItem->nama_menu . '.');
                }
            }
        }

        // Buat kode transaksi
        $kode = 'TRX-' . Carbon::now()->format('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', Carbon::today())->count() + 1, 3, '0', STR_PAD_LEFT);

        // Hitung total harga
        $totalHarga = 0;
        foreach ($request->menu as $index => $menuId) {
            $menuItem = Menu::findOrFail($menuId);
            $totalHarga += $menuItem->harga * $request->qty[$index];
        }

        // Simpan transaksi (total_hpp diisi 0 dulu, diupdate setelah detail dihitung)
        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode,
            'kasir_id'       => auth()->user()->id,
            'total_harga'    => $totalHarga,
            'total_hpp'      => 0,
            'status'         => 'selesai',
        ]);

        // Simpan detail & potong stok FIFO sekaligus hitung HPP per baris
        $totalHppTransaksi = 0;

        foreach ($request->menu as $index => $menuId) {
            $qty = $request->qty[$index];
            $menuItem = Menu::with('resepDetail')->findOrFail($menuId);

            // Potong stok FIFO per bahan pada resep, sekaligus akumulasi HPP
            // dari batch mana saja yang benar-benar dipakai untuk qty menu ini.
            $hppMenuIni = 0;
            foreach ($menuItem->resepDetail as $resep) {
                $kebutuhan = $resep->jumlah * $qty;
                $hppMenuIni += $this->potongStokFIFO($resep->bahan_id, $kebutuhan, $transaksi->id);
            }

            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'menu_id'      => $menuItem->id,
                'nama_menu'    => $menuItem->nama_menu,
                'harga'        => $menuItem->harga,
                'qty'          => $qty,
                'subtotal'     => $menuItem->harga * $qty,
                'hpp'          => $hppMenuIni,
            ]);

            $totalHppTransaksi += $hppMenuIni;
        }

        $transaksi->update(['total_hpp' => $totalHppTransaksi]);

        return redirect()->route('kasir.transaksi.struk', $transaksi->id)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function struk($id)
    {
        $transaksi = Transaksi::with('detail', 'kasir')->findOrFail($id);
        return view('kasir.struk', compact('transaksi'));
    }

    public function batal($id)
    {
        $transaksi = Transaksi::with('detail.menu')->findOrFail($id);

        if ($transaksi->status === 'dibatalkan') {
            return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        // Cek batas waktu pembatalan (30 menit)
        if (\Carbon\Carbon::parse($transaksi->created_at)->diffInMinutes(now()) > 30) {
            return back()->with('error', 'Transaksi tidak dapat dibatalkan karena sudah lebih dari 30 menit.');
        }

        // Rollback stok — dikembalikan PERSIS ke batch asalnya berdasarkan catatan pemakaian
        $pemakaian = PemakaianBatch::where('transaksi_id', $transaksi->id)->get();

        foreach ($pemakaian as $catatan) {
            $batch = Batch::find($catatan->batch_id);
            if ($batch) {
                $batch->qty_sisa += $catatan->qty;
                if ($batch->qty_sisa > 0) {
                    $batch->status = 'aktif';
                }
                $batch->save();
            }
        }

        // Catatan pemakaian sudah "dipakai" untuk rollback, hapus supaya tidak
        // ke-rollback dua kali kalau ada bug lain yang memanggil batal() berulang.
        PemakaianBatch::where('transaksi_id', $transaksi->id)->delete();

        $transaksi->update([
            'status'          => 'dibatalkan',
            'dibatalkan_oleh' => auth()->user()->id,
            'dibatalkan_at'   => \Carbon\Carbon::now(),
        ]);

        return back()->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
    }

    /**
     * Memotong stok bahan baku secara FIFO (batch dengan tanggal expired terdekat dihabiskan dulu),
     * sekaligus menghitung total HPP dan MENCATAT persis batch mana + berapa qty yang diambil
     * (lewat PemakaianBatch), supaya kalau transaksi dibatalkan, rollback bisa akurat per batch.
     *
     * @return float Total biaya (HPP) untuk kebutuhan yang dipotong dari batch-batch ini.
     */
    private function potongStokFIFO($bahanId, $kebutuhan, $transaksiId)
    {
        $batches = Batch::where('bahan_id', $bahanId)
            ->where('status', 'aktif')
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_expired', 'asc')
            ->get();

        $totalHpp = 0;

        foreach ($batches as $batch) {
            if ($kebutuhan <= 0) break;

            if ($batch->qty_sisa >= $kebutuhan) {
                $qtyTerpakai = $kebutuhan;
                $batch->qty_sisa -= $kebutuhan;
                $kebutuhan = 0;
            } else {
                $qtyTerpakai = $batch->qty_sisa;
                $kebutuhan -= $batch->qty_sisa;
                $batch->qty_sisa = 0;
            }

            $totalHpp += $qtyTerpakai * $batch->harga_beli;

            // Catat persis: transaksi ini mengambil $qtyTerpakai dari batch ini.
            PemakaianBatch::create([
                'transaksi_id' => $transaksiId,
                'batch_id'     => $batch->id,
                'bahan_id'     => $bahanId,
                'qty'          => $qtyTerpakai,
            ]);

            if ($batch->qty_sisa == 0) {
                $batch->status = 'habis';
            }

            $batch->save();
        }

        // Cek notifikasi stok minimum
        $bahan = BahanBaku::findOrFail($bahanId);
        BatchController::cekNotifikasiStokMinimum($bahan);

        return $totalHpp;
    }
}