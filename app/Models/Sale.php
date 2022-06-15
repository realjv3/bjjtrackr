<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'payment_method'];

    /**
     * Gets the sale's total attribute prefixed with dollar sign & having decimal places
     *
     * @param $value
     *
     * @return string
     */
    public function getTotalAttribute($value): string {
        return '$' . number_format($value / 100, 2);
    }

    /**
     * Relationship to client
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo {
        return $this->belongsTo('App\Models\Client');
    }

    /**
     * Relationship to user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo('App\Models\User');
    }
}
