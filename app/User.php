<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'notes', 'start_date', 'cust_id', 'api_token', 'active'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'cust_id', 'api_token'];

    public function roles() {
        return $this->belongsToMany('App\Role', 'user_role')->withTimestamps();
    }

    public function rank() {
        return $this->hasOne('App\Rank');
    }

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function checkins() {
        return $this->hasMany('App\Checkin');
    }

    public function lastCheckin() {
        return $this->hasOne('App\Checkin')->latest();
    }

    public function subscription() {
        return $this->hasOne('App\Subscription');
    }

    public function log() {
        return $this->hasMany('App\Log');
    }
}
