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

    public function settings() {
        return $this->hasMany('App\Setting');
    }

    public function log() {
        return $this->hasMany('App\Log');
    }

    public function getFirstAdmin() {

        return $this->users()->whereHas('roles', function ($q) {
            $q->where('role_id', 2);
        })->first();
    }

    public function documents() {
        return $this->hasMany('App\Documents');
    }
}
