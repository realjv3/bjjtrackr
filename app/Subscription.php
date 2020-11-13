<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'id',
        'client_id',
        'cust_id',
        'subscription_id',
        'item_id',
        'current_period_end',
        'price_id',
        'status',
    ];

    public function client() {
        return $this->belongsTo('App\Client');
    }
}
