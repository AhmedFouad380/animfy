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
        // 1. Add new duration column
        Schema::table('courses', function (Blueprint $table) {
            $table->string('duration')->default('00:00:00')->after('rating');
        });

        // 2. Migrate existing data from duration_hours to duration
        $courses = DB::table('courses')->get();
        foreach ($courses as $course) {
            $hours = $course->duration_hours ?? 0;
            $durationStr = sprintf('%02d:00:00', $hours);
            DB::table('courses')
                ->where('id', $course->id)
                ->update(['duration' => $durationStr]);
        }

        // 3. Drop duration_hours column
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('duration_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add duration_hours column
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('duration_hours')->default(0)->after('rating');
        });

        // 2. Migrate data back from duration to duration_hours (integer)
        $courses = DB::table('courses')->get();
        foreach ($courses as $course) {
            $duration = $course->duration ?? '00:00:00';
            $parts = explode(':', $duration);
            $hours = (int)($parts[0] ?? 0);
            DB::table('courses')
                ->where('id', $course->id)
                ->update(['duration_hours' => $hours]);
        }

        // 3. Drop duration column
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
