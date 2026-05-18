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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Translatable (ar/en)
            $table->string('slug')->unique();
            $table->json('slogan')->nullable(); // Translatable (ar/en)
            $table->string('thumbnail')->nullable();
            $table->string('video_overview_url')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->boolean('is_best_seller')->default(false);
            $table->json('description_header')->nullable(); // Translatable (ar/en)
            $table->json('description')->nullable(); // Translatable (ar/en)
            $table->json('what_you_will_learn')->nullable(); // Translatable JSON array of checkmarks
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->integer('duration_hours')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
