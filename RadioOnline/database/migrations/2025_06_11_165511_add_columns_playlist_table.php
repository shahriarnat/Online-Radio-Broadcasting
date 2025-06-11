<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->enum('playlist_type', ['live', 'podcast', 'music'])->default('music')->after('channel_playlist');
            $table->json('playlist_options')->nullable()->after('playlist_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn('playlist_type');
            $table->dropColumn('playlist_options');
        });
    }
};
