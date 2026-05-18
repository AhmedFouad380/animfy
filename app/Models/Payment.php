<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'enrollment_id',
        'transaction_reference',
        'amount',
        'status',
        'payment_method',
        'paymob_payload',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'paymob_payload' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Enrollment relationship.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}
