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

    public function user() {
        return $this->belongsToMany('App\User');
    }
}
