<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FoodWastage;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class FoodWastageController extends Controller
{
    public function index()
    {
        $foodWastage = FoodWastage::with('batch.bahanBaku', 'pelapor')->latest()->get();
        $batch = Batch::with('bahanBaku')->where('status', 'aktif')->get();
        return view('manager.food_wastage', compact('foodWastage', 'batch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batch,id',
            'jumlah'   => 'required|numeric|min:0.01',
            'alasan'   => 'required|string',
        ]);

        $batch = Batch::findOrFail($request->batch_id);

        if ($request->jumlah > $batch->qty_sisa) {
            return back()->with('error', 'Jumlah wastage melebihi stok sisa batch (' . $batch->qty_sisa . ' ' . $batch->bahanBaku->satuan . ').');
        }

        FoodWastage::create([
            'batch_id'   => $request->batch_id,
            'pelapor_id' => auth()->user()->id,
            'jumlah'     => $request->jumlah,
            'alasan'     => $request->alasan,
        ]);

        // Kurangi stok batch
        $batch->qty_sisa -= $request->jumlah;
        if ($batch->qty_sisa <= 0) {
            $batch->qty_sisa = 0;
            $batch->status = 'habis';
        }
        $batch->save();

        // Cek notifikasi stok minimum
        BatchController::cekNotifikasiStokMinimum($batch->bahanBaku);

        return back()->with('success', 'Food wastage berhasil dicatat dan stok diperbarui.');
    }
}