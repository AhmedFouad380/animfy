<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
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
        'price_paid',
        'status',
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
     * Payments relationship.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
