<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batch';

    protected $fillable = [
        'bahan_id',
        'kode_batch',
        'qty_awal',
        'qty_sisa',
        'tanggal_masuk',
        'tanggal_expired',
        'status',
        'input_by',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }

    public function inputOleh()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function foodWastage()
    {
        return $this->hasMany(FoodWastage::class, 'batch_id');
    }
}