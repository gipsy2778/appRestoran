<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BahanBaku;
use App\Models\Notifikasi;
use App\Models\FoodWastage;
use App\Models\PemakaianBatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BatchController extends Controller
{
    public function index()
    {
        $batch = Batch::with('bahanBaku')->orderBy('tanggal_expired', 'asc')->get();
        $bahanBaku = BahanBaku::all();
        return view('manager.batch', compact('batch', 'bahanBaku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_id'         => 'required|exists:bahan_baku,id',
            'qty_awal'         => 'required|numeric|min:0.01',
            'mode_harga'       => 'required|in:total,satuan',
            'harga_input'      => 'required|numeric|min:0',
            'tanggal_masuk'    => 'required|date',
            'tanggal_expired'  => 'required|date|after:tanggal_masuk',
        ]);

        // Hitung harga beli PER SATUAN, apapun mode input yang dipilih user.
        // Kolom 'harga_beli' di database selalu menyimpan harga per satuan,
        // supaya perhitungan HPP tidak perlu tahu mode input mana yang tadinya dipakai.
        if ($request->mode_harga === 'total') {
            $hargaBeli = $request->harga_input / $request->qty_awal;
        } else {
            $hargaBeli = $request->harga_input;
        }

        $bahan = BahanBaku::findOrFail($request->bahan_id);

        // Generate kode batch otomatis
        $tanggal = Carbon::parse($request->tanggal_masuk)->format('Ymd');
        $kode_bahan = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $bahan->nama_bahan), 0, 3));
        $suffix = Batch::where('bahan_id', $request->bahan_id)
            ->whereDate('tanggal_masuk', $request->tanggal_masuk)
            ->count() + 1;
        $kode_batch = $kode_bahan . $bahan->id . '-' . $tanggal . '-' . str_pad($suffix, 2, '0', STR_PAD_LEFT);

        $batch = Batch::create([
            'bahan_id'        => $request->bahan_id,
            'kode_batch'      => $kode_batch,
            'qty_awal'        => $request->qty_awal,
            'qty_sisa'        => $request->qty_awal,
            'harga_beli'      => round($hargaBeli, 2),
            'tanggal_masuk'   => $request->tanggal_masuk,
            'tanggal_expired' => $request->tanggal_expired,
            'status'          => 'aktif',
            'input_by'        => auth()->user()->id,
        ]);

        // Cek notifikasi expired warning (H-3)
        $this->cekNotifikasiExpired($batch);

        return back()->with('success', 'Batch ' . $kode_batch . ' berhasil ditambahkan.');
    }

    public function destroy(Batch $batch)
    {
        // Jangan izinkan hapus batch yang sudah punya riwayat pemakaian (transaksi)
        // atau food wastage — kalau tetap dihapus, riwayat itu akan ikut hilang
        // permanen (data historis untuk laporan bisa rusak).
        $punyaRiwayatTransaksi = PemakaianBatch::where('batch_id', $batch->id)->exists();
        $punyaRiwayatWastage = FoodWastage::where('batch_id', $batch->id)->exists();

        if ($punyaRiwayatTransaksi || $punyaRiwayatWastage) {
            return back()->with('error',
                'Batch ' . $batch->kode_batch . ' tidak bisa dihapus karena sudah punya riwayat pemakaian/food wastage. ' .
                'Data ini disimpan untuk keakuratan laporan. Gunakan filter "Sembunyikan yang sudah habis" ' .
                'kalau cuma ingin merapikan tampilan.'
            );
        }

        $batch->delete();
        return back()->with('success', 'Batch berhasil dihapus.');
    }

    private function cekNotifikasiExpired(Batch $batch)
    {
        $hariTersisa = (int) Carbon::now()->diffInDays(Carbon::parse($batch->tanggal_expired), false);

        if ($hariTersisa <= 3 && $hariTersisa >= 0) {
            $managers = \App\Models\User::where('role', 'manager')->get();
            foreach ($managers as $manager) {
                $sudahAda = Notifikasi::where('user_id', $manager->id)
                    ->where('tipe', 'expired_warning')
                    ->whereDate('created_at', Carbon::today())
                    ->where('pesan', 'like', '%' . $batch->kode_batch . '%')
                    ->exists();

                if (!$sudahAda) {
                    if ($hariTersisa == 0) {
                        $pesanHari = 'hari ini';
                    } elseif ($hariTersisa == 1) {
                        $pesanHari = 'besok';
                    } else {
                        $pesanHari = 'dalam ' . $hariTersisa . ' hari';
                    }

                    Notifikasi::create([
                        'user_id' => $manager->id,
                        'judul'   => $hariTersisa == 0 ? 'Bahan Kedaluwarsa Hari Ini' : 'Bahan Mendekati Kedaluwarsa',
                        'pesan'   => $batch->bahanBaku->nama_bahan . ' (Batch ' . $batch->kode_batch . ') kedaluwarsa ' . $pesanHari . '.',
                        'tipe'    => 'expired_warning',
                        'status'  => 'belum_dibaca',
                    ]);
                }
            }
        }
    }

    public static function cekNotifikasiStokMinimum(BahanBaku $bahan)
    {
        $stokTotal = Batch::where('bahan_id', $bahan->id)
            ->where('status', 'aktif')
            ->sum('qty_sisa');

        if ($stokTotal <= $bahan->stok_minimum) {
            $managers = \App\Models\User::where('role', 'manager')->get();
            foreach ($managers as $manager) {
                $sudahAda = Notifikasi::where('user_id', $manager->id)
                    ->where('tipe', 'stok_minimum')
                    ->whereDate('created_at', Carbon::today())
                    ->where('pesan', 'like', '%' . $bahan->nama_bahan . '%')
                    ->exists();

                if (!$sudahAda) {
                    Notifikasi::create([
                        'user_id' => $manager->id,
                        'judul'   => 'Stok Menipis',
                        'pesan'   => $bahan->nama_bahan . ' tersisa ' . $stokTotal . ' ' . $bahan->satuan . ' (minimum: ' . $bahan->stok_minimum . ' ' . $bahan->satuan . ').',
                        'tipe'    => 'stok_minimum',
                        'status'  => 'belum_dibaca',
                    ]);
                }
            }
        }
    }
}