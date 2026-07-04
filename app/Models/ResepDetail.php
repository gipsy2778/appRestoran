<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepDetail extends Model
{
    protected $table = 'resep_detail';

    protected $fillable = [
        'menu_id',
        'bahan_id',
        'jumlah',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }
}