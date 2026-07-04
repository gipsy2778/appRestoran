<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'nama_menu',
        'harga',
    ];

    public function resepDetail()
    {
        return $this->hasMany(ResepDetail::class, 'menu_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'menu_id');
    }
}