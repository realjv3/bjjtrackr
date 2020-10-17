<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'cust_id',
        'subscription_id',
        'item_id',
        'current_period_end',
        'price_id',
        'status',
    ];

    public function user() {
        $this->belongsTo('App\User');
    }
}
