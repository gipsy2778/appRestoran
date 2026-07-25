<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'kasir_id',
        'total_harga',
        'total_hpp',
        'status',
        'dibatalkan_oleh',
        'dibatalkan_at',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function dibatalkanOleh()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function pemakaianBatch()
    {
        return $this->hasMany(PemakaianBatch::class, 'transaksi_id');
    }
}