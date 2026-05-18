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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->json('title'); // Translatable (ar/en)
            $table->string('video_path'); // Local file path or external link
            $table->json('description')->nullable(); // Translatable (ar/en)
            $table->string('attachment_path')->nullable(); // Course files for download
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_preview')->default(false); // Free preview before purchase
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
