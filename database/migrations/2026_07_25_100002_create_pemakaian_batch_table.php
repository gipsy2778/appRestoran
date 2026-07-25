<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buku catatan: setiap kali potongStokFIFO() mengambil qty dari sebuah batch
        // untuk sebuah transaksi, dicatat di sini. Ini yang menjadi dasar rollback yang
        // akurat saat transaksi dibatalkan — qty dikembalikan PERSIS ke batch asalnya,
        // bukan ditumpuk ke satu batch sembarangan.
        Schema::create('pemakaian_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batch')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('qty', 10, 2); // qty yang diambil dari batch ini untuk transaksi ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_batch');
    }
};
