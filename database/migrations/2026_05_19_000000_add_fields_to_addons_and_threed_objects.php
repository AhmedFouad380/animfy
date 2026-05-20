<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely drop foreign keys and unique constraint
        try {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'course_id']);
            });
        } catch (\Exception $e) {}

        Schema::table('enrollments', function (Blueprint $table) {
            // Make course_id nullable
            $table->unsignedBigInteger('course_id')->nullable()->change();
            
            // Add addon_id and three_d_object_id if they don't exist
            if (!Schema::hasColumn('enrollments', 'addon_id')) {
                $table->foreignId('addon_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('enrollments', 'three_d_object_id')) {
                $table->foreignId('three_d_object_id')->nullable()->after('addon_id')->constrained()->onDelete('cascade');
            }
            
            // Re-add foreign key constraints safely
            // We drop if they exist before adding
            try {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            } catch (\Exception $e) {}
            
            try {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            } catch (\Exception $e) {}
        });

        Schema::table('addons', function (Blueprint $table) {
            if (!Schema::hasColumn('addons', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }
            if (!Schema::hasColumn('addons', 'description_header')) {
                $table->json('description_header')->nullable()->after('thumbnail');
            }
            if (!Schema::hasColumn('addons', 'description')) {
                $table->json('description')->nullable()->after('description_header');
            }
            if (!Schema::hasColumn('addons', 'file_path')) {
                $table->string('file_path')->nullable()->after('purchase_url');
            }
        });

        Schema::table('three_d_objects', function (Blueprint $table) {
            if (!Schema::hasColumn('three_d_objects', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }
            if (!Schema::hasColumn('three_d_objects', 'description_header')) {
                $table->json('description_header')->nullable()->after('thumbnail');
            }
            if (!Schema::hasColumn('three_d_objects', 'description')) {
                $table->json('description')->nullable()->after('description_header');
            }
            if (!Schema::hasColumn('three_d_objects', 'file_path')) {
                $table->string('file_path')->nullable()->after('purchase_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for safety in production/dev sync issues
    }
};
