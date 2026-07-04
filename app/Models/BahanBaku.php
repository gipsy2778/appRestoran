<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama_bahan',
        'jenis',
        'satuan',
        'stok_minimum',
    ];

    public function batch()
    {
        return $this->hasMany(Batch::class, 'bahan_id');
    }

    public function resepDetail()
    {
        return $this->hasMany(ResepDetail::class, 'bahan_id');
    }
}