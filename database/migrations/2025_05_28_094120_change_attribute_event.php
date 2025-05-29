<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('poster_public_id', 'poster_path');
            $table->renameColumn('cover_image_public_id', 'cover_path');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('poster_path', 'poster_public_id');
            $table->renameColumn('cover_path', 'cover_image_public_id');
        });
    }
};
