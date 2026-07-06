<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())
            ->latest()
            ->get();
        return view('manager.notifikasi', compact('notifikasi'));
    }

    public function baca($id)
    {
        Notifikasi::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['status' => 'dibaca']);
        return back()->with('success', 'Notifikasi ditandai dibaca.');
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', auth()->id())
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'dibaca']);
        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function fetch()
    {
        // Cek batch mendekati expired setiap kali polling
        $this->cekExpiredWarning();

        $notifikasi = Notifikasi::where('user_id', auth()->id())
            ->where('status', 'belum_dibaca')
            ->latest()
            ->get();

        return response()->json($notifikasi);
    }

    private function cekExpiredWarning()
    {
        $batches = \App\Models\Batch::with('bahanBaku')
            ->where('status', 'aktif')
            ->get();

        foreach ($batches as $batch) {
            $hariTersisa = \Carbon\Carbon::now()->diffInDays(
                \Carbon\Carbon::parse($batch->tanggal_expired), false
            );

            if ($hariTersisa <= 3 && $hariTersisa >= 0) {
                $managers = \App\Models\User::where('role', 'manager')->get();

                foreach ($managers as $manager) {
                    // Cek duplikasi — hanya sekali per hari per batch
                    $sudahAda = Notifikasi::where('user_id', $manager->id)
                        ->where('tipe', 'expired_warning')
                        ->whereDate('created_at', \Carbon\Carbon::today())
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