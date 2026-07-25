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
        Schema::table('batch', function (Blueprint $table) {
            // Harga beli PER SATUAN bahan baku pada batch ini (mis. harga per kg saat batch ini masuk).
            // Disimpan per batch (bukan per bahan baku) karena harga beli bisa berubah setiap kali restock,
            // dan ini yang menjadi dasar perhitungan HPP FIFO yang akurat.
            $table->decimal('harga_beli', 12, 2)->default(0)->after('qty_sisa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });
    }
};
