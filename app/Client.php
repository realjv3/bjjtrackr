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

    public function checkins() {
        return $this->hasMany('App\Checkin');
    }

    public function users() {
        return $this->hasMany('App\User');
    }
}
