<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('video_vtuber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('videos', 'id')->onDelete('cascade');
            $table->foreignId('vtuber_id')->constrained('vtubers', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_vtuber');
    }
};
