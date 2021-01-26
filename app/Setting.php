<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'client_id',
        'belt_id',
        'sessions_til_stripe',
        'weeks_absent_til_contact',
        'combine_same_day_checkins',
    ];

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function belt() {
        return $this->belongsTo('App\Belt');
    }
}
