<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Sama seperti fix sebelumnya untuk batch_id: bahan_id di tabel batch & resep_detail
     * sebelumnya pakai onDelete('cascade'). Artinya menghapus satu bahan baku akan
     * otomatis ikut menghapus SEMUA batch (riwayat harga beli/HPP) dan SEMUA baris resep
     * yang memakai bahan itu — bisa merusak resep menu tanpa peringatan apapun.
     * Diubah jadi 'restrict' supaya database menolak penghapusan selama masih dipakai.
     */
    public function up(): void
    {
        Schema::table('batch', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->foreign('bahan_id')->references('id')->on('bahan_baku')->onDelete('restrict');
        });

        Schema::table('resep_detail', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->foreign('bahan_id')->references('id')->on('bahan_baku')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->foreign('bahan_id')->references('id')->on('bahan_baku')->onDelete('cascade');
        });

        Schema::table('resep_detail', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->foreign('bahan_id')->references('id')->on('bahan_baku')->onDelete('cascade');
        });
    }
};
