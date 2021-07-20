<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;

    protected $table = 'log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'user_id', 'type', 'action'];

    public function clients() {
        return $this->belongsTo('App\Client');
    }

    public function users() {
        return $this->belongsTo('App\User');
    }
}
