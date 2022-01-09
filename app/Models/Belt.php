<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Belt extends Model
{
    protected $fillable = ['belt'];

    public function users() {

        return $this->hasMany('App\Models\User');
    }

    public function settings() {

        return $this->hasMany('App\Models\Setting');
    }

    public function ranks() {

        return $this->hasMany('App\Models\Rank');
    }
}
