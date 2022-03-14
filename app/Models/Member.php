<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'user_id',
        'cust_id',
        'subscription_id',
        'item_id',
        'current_period_end',
        'price_id',
        'status',
    ];

    /**
     * Relationship to clients
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
     * Relationship to price
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo {
        return $this->belongsTo('App\Models\Price');
    }

    /**
     * Accessor of current_period_end converts timestamp to datetime
     *
     * @param $val
     *
     * @return string
     */
    public function getCurrentPeriodEndAttribute($val): string {

        $carbon = new Carbon($val);

        return $carbon->format('Y-m-d H:i:s');
    }
}
