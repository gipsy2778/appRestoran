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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('kasir_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', ['selesai', 'dibatalkan'])->default('selesai');
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('dibatalkan_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
