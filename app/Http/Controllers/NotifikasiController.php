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
            $hariTersisa = Carbon::now()->diffInDays(
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
    }
}