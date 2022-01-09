<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $fillable = ['user_id', 'client_id', 'event_id', 'checked_in_at'];

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function event() {
        return $this->belongsTo('App\Models\Event');
    }
}
