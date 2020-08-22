<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Belt extends Model
{
    protected $fillable = ['belt'];

    public function users() {

        return $this->hasMany('App\User');
    }

    public function settings() {

        return $this->hasMany('App\Setting');
    }

    public function ranks() {

        return $this->hasMany('App\Rank');
    }
}
