<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {

            $table->dropColumn('vtuber_id'); // Remove column
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->foreignId('vtuber_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};