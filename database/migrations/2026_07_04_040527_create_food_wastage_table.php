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
        Schema::create('food_wastage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batch')->onDelete('cascade');
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('jumlah', 10, 2);
            $table->string('alasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_wastage');
    }
};
