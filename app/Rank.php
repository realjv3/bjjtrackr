<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = ['user_id', 'belt', 'stripes', 'last_ranked_up'];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
