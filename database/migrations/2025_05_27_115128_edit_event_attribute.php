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
        Schema::table('events', function (Blueprint $table) {
            // Tambahkan kolom public_id jika belum ada
            if (!Schema::hasColumn('events', 'cover_image_public_id')) {
                $table->string('cover_image_public_id')->nullable()->after('cover_image');
            }

            if (!Schema::hasColumn('events', 'poster_public_id')) {
                $table->string('poster_public_id')->nullable()->after('poster');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Hapus kolom public_id
            $table->dropColumn(['cover_image_public_id', 'poster_public_id']);
        });
    }
};
