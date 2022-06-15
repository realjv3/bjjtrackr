<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
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
    protected $fillable = ['name', 'unit', 'active'];

    /**
     * Relationship to clients
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo {

        return $this->belongsTo('App\Models\Client');
    }

    /**
     * Relationship to prices
     *
     * @return HasMany
     */
    public function prices(): HasMany {

        return $this->hasMany('App\Models\Price');
    }

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['prices'];
}
