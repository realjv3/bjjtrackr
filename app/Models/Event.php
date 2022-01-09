<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['client_id', 'name', 'day', 'start', 'end'];

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }

    public function day() {
        return $this->hasOne('App\Models\Day');
    }

    public function checkins() {
        return $this->hasMany('App\Models\Checkin');
    }
}
