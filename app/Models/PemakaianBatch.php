<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemakaianBatch extends Model
{
    protected $table = 'pemakaian_batch';

    protected $fillable = [
        'transaksi_id',
        'batch_id',
        'bahan_id',
        'qty',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }
}
