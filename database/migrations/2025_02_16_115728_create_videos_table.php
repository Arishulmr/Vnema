<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_video_id')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail_url');
            $table->foreignId('channel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('livestream_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vtuber_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};