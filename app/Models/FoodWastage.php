<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodWastage extends Model
{
    protected $table = 'food_wastage';

    protected $fillable = [
        'batch_id',
        'pelapor_id',
        'jumlah',
        'alasan',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}