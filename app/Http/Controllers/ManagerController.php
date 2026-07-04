<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class ManagerController extends Controller
{
    public function dashboard()
    {
        return view('manager.dashboard');
    }

    public function foodWastageIndex()
    {
        return view('manager.food_wastage');
    }

    public function notifikasiIndex()
    {
        return view('manager.notifikasi');
    }

    public function laporanIndex()
    {
        return view('manager.laporan');
    }

    public function notifikasiFetch()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())
            ->where('status', 'belum_dibaca')
            ->latest()
            ->get();

        return response()->json($notifikasi);
    }

    public function notifikasiBaca($id)
    {
        $notif = Notifikasi::findOrFail($id);
        $notif->update(['status' => 'dibaca']);
        return redirect()->back();
    }
}