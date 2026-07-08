<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BahanBaku;
use App\Models\Notifikasi;
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
            'tanggal_masuk'    => 'required|date',
            'tanggal_expired'  => 'required|date|after:tanggal_masuk',
        ]);

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
        $batch->delete();
        return back()->with('success', 'Batch berhasil dihapus.');
    }

    private function cekNotifikasiExpired(Batch $batch)
    {
        $hariTersisa = Carbon::now()->diffInDays(Carbon::parse($batch->tanggal_expired), false);

        if ($hariTersisa <= 3 && $hariTersisa >= 0) {
            $managers = \App\Models\User::where('role', 'manager')->get();
            foreach ($managers as $manager) {
                // Cek duplikasi per hari per batch
                $sudahAda = Notifikasi::where('user_id', $manager->id)
                    ->where('tipe', 'expired_warning')
                    ->whereDate('created_at', Carbon::today())
                    ->where('pesan', 'like', '%' . $batch->kode_batch . '%')
                    ->exists();

                if (!$sudahAda) {
                    Notifikasi::create([
                        'user_id' => $manager->id,
                        'judul'   => 'Bahan Mendekati Kedaluwarsa',
                        'pesan'   => $batch->bahanBaku->nama_bahan . ' (Batch ' . $batch->kode_batch . ') akan kedaluwarsa dalam ' . $hariTersisa . ' hari.',
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