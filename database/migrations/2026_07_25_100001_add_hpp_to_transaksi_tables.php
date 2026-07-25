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
        // HPP per baris detail (total biaya bahan baku untuk qty menu yang terjual di baris ini)
        Schema::table('transaksi_detail', function (Blueprint $table) {
            $table->decimal('hpp', 12, 2)->default(0)->after('subtotal');
        });

        // Total HPP satu transaksi (akumulasi dari semua baris detail), disimpan juga di header
        // supaya laporan laba/rugi tidak perlu join & sum berulang setiap kali diakses.
        Schema::table('transaksi', function (Blueprint $table) {
            $table->decimal('total_hpp', 12, 2)->default(0)->after('total_harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_detail', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('total_hpp');
        });
    }
};
