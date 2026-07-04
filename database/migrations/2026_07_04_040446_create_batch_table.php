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
        Schema::create('batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->string('kode_batch')->unique();
            $table->decimal('qty_awal', 10, 2);
            $table->decimal('qty_sisa', 10, 2);
            $table->date('tanggal_masuk');
            $table->date('tanggal_expired');
            $table->enum('status', ['aktif', 'habis'])->default('aktif');
            $table->foreignId('input_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch');
    }
};
