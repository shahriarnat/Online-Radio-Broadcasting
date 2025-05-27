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
        Schema::table('playlist_music', function (Blueprint $table) {
            $table->enum('play_status', ['pending', 'playing', 'played'])->default('pending')->after('position')->comment('Indicates if the music is currently playing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playlist_music', function (Blueprint $table) {
            $table->dropColumn('playing');
        });
    }
};
