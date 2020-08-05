<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['client_id', 'name', 'day', 'start', 'end'];

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function day() {
        return $this->hasOne('App\Day');
    }

    public function checkins() {
        return $this->hasMany('App\Checkin');
    }
}
