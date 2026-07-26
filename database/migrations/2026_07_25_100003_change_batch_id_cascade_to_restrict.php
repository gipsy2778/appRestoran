<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Sebelumnya, batch_id di tabel food_wastage & pemakaian_batch pakai onDelete('cascade'),
     * artinya menghapus satu batch akan otomatis ikut menghapus SEMUA riwayat food wastage
     * dan riwayat pemakaian (transaksi) yang terkait — data historis penting bisa hilang
     * tanpa sengaja. Diubah jadi 'restrict' supaya database MENOLAK penghapusan batch
     * selama masih ada riwayat yang menunjuk ke batch tersebut.
     */
    public function up(): void
    {
        Schema::table('food_wastage', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->foreign('batch_id')->references('id')->on('batch')->onDelete('restrict');
        });

        Schema::table('pemakaian_batch', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->foreign('batch_id')->references('id')->on('batch')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_wastage', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->foreign('batch_id')->references('id')->on('batch')->onDelete('cascade');
        });

        Schema::table('pemakaian_batch', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->foreign('batch_id')->references('id')->on('batch')->onDelete('cascade');
        });
    }
};
