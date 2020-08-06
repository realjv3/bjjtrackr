<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['belt', 'classes_til_stripe', 'times_absent_til_contact', 'combine_same_day_checkins'];
}
