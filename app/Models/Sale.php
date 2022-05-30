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

    /**
     * Relationship to products
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Relationship to price
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo {
        return $this->belongsTo('App\Models\Price');
    }
}
