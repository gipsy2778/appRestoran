<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Batch;
use App\Models\User;
use Carbon\Carbon;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->user()->id)
            ->latest()
            ->get();
        return view('manager.notifikasi', compact('notifikasi'));
    }

    public function baca($id)
    {
        Notifikasi::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->update(['status' => 'dibaca']);
        return back()->with('success', 'Notifikasi ditandai dibaca.');
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', auth()->user()->id)
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'dibaca']);
        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function fetch()
    {
        $this->cekExpiredWarning();

        $notifikasi = Notifikasi::where('user_id', auth()->user()->id)
            ->where('status', 'belum_dibaca')
            ->latest()
            ->get();

        return response()->json($notifikasi);
    }

    private function cekExpiredWarning()
    {
        $batches = Batch::with('bahanBaku')
            ->where('status', 'aktif')
            ->get();

        foreach ($batches as $batch) {
            $hariTersisa = (int) Carbon::now()->diffInDays(
                Carbon::parse($batch->tanggal_expired), false
            );

            if ($hariTersisa <= 3 && $hariTersisa >= 0) {
                $managers = User::where('role', 'manager')->get();

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
    }
    
    public function hapus($id)
    {
        Notifikasi::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->delete();
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function hapusSemua()
    {
        Notifikasi::where('user_id', auth()->user()->id)->delete();
        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}