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
        // tabel untuk menyimpan jumlah pengunjung setiap hari, sama sekali tidak berhubungan dengan tabel lain
        Schema::create('visitors', function (Blueprint $table) {
            $table->dateTime('date')->primary();
            $table->integer('amount')->nullable(); // null asumsi manajemen belum isi di hari itu???
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
