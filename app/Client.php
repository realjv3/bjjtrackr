<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'affiliation',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'country',
        'notes',
    ];

    public function events() {
        return $this->hasMany('App\Event');
    }

    public function checkins() {
        return $this->hasMany('App\Checkin');
    }

    public function users() {
        return $this->hasMany('App\User');
    }

    public function subscription() {
        return $this->hasOne('App\Subscription');
    }
}
