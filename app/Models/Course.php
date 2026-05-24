<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = [
        'title',
        'slogan',
        'description_header',
        'description',
        'what_you_will_learn',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'slogan',
        'thumbnail',
        'video_overview_url',
        'price',
        'discount_price',
        'is_best_seller',
        'description_header',
        'description',
        'what_you_will_learn',
        'rating',
        'duration',
        'is_active',
        'students_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'what_you_will_learn' => 'array',
        'is_best_seller' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating' => 'decimal:1',
        'students_count' => 'integer',
    ];

    /**
     * Get chapters relation.
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }

    /**
     * Get lessons directly through chapters.
     */
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    /**
     * Get enrollments relation.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get reviews relation.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }
}
