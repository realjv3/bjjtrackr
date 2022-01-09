<?php

namespace App\Models;

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
        return $this->belongsTo('App\Models\Client');
    }

    public function belt() {
        return $this->belongsTo('App\Models\Belt');
    }
}
