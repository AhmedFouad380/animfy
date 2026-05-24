<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'comment',
        'is_approved',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * User relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Course relationship.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::saved(function ($review) {
            $review->recalculateCourseRating();

            // If the course_id was changed, also recalculate the old course rating
            if ($review->wasChanged('course_id')) {
                $originalCourseId = $review->getOriginal('course_id');
                if ($originalCourseId) {
                    self::updateCourseRating($originalCourseId);
                }
            }
        });

        static::deleted(function ($review) {
            $review->recalculateCourseRating();
        });
    }

    /**
     * Recalculate course average rating and save it.
     */
    public function recalculateCourseRating()
    {
        if ($this->course_id) {
            self::updateCourseRating($this->course_id);
        }
    }

    /**
     * Calculate average rating of approved reviews and update Course table.
     */
    public static function updateCourseRating($courseId)
    {
        $avgRating = self::where('course_id', $courseId)
            ->where('is_approved', true)
            ->avg('rating');

        Course::where('id', $courseId)->update([
            'rating' => $avgRating !== null ? round($avgRating, 1) : 5.0
        ]);
    }
}

