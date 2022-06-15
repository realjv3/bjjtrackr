<?php

namespace App\Models;

use App\Enums\PricePeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Price extends Model
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
    protected $fillable = ['amount', 'recurring', 'period', 'period_count', 'active'];

    /**
     * Relationship to clients
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo {
        return $this->belongsTo('App\Models\Client');
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
     * Relationship to members
     *
     * @return HasMany
     */
    public function members(): HasMany {
        return $this->hasMany('App\Models\Member');
    }

    /**
     * Gets the price's amount attribute prefixed with dollar sign & having decimal places
     *
     * @param $value
     *
     * @return string
     */
    public function getAmountAttribute($value): string {
        return '$' . number_format($value / 100, 2);
    }

    /**
     * Validate interval
     *
     * @param $value
     *
     * @return string
     */
    public function setIntervalAttribute($value): string {

        if (PricePeriod::tryFrom($value) === null) {
            return PricePeriod::MONTH->value;
        }
        return $value;
    }
}
