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
        Schema::create('musics', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('cover')->nullable();
            $table->string('music');
            $table->integer('duration')->nullable()->comment('Duration in seconds');

            $table->unsignedBigInteger('genre_id')->nullable()->comment('Genre name');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('restrict');

            $table->integer('guest_like')->comment('Number of likes from guest users')->default(0);
            $table->boolean('is_ads')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musics');
    }
};
