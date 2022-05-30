<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    protected $fillable = ['name', 'email', 'phone', 'password', 'notes', 'start_date', 'cust_id', 'api_token', 'active'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'cust_id', 'api_token'];

    public function roles(): BelongsToMany {
        return $this->belongsToMany('App\Models\Role', 'user_role')->withTimestamps();
    }

    public function rank(): HasOne {
        return $this->hasOne('App\Models\Rank');
    }

    public function client(): BelongsTo {
        return $this->belongsTo('App\Models\Client');
    }

    public function checkins(): HasMany {
        return $this->hasMany('App\Models\Checkin');
    }

    public function lastCheckin(): HasOne {
        return $this->hasOne('App\Models\Checkin')->latest();
    }

    public function subscription(): HasOne {
        return $this->hasOne('App\Models\Subscription');
    }

    public function log(): HasMany {
        return $this->hasMany('App\Models\Log');
    }

    public function documents(): HasMany {
        return $this->hasMany('App\Models\Document');
    }

    public function members(): HasMany {
        return $this->hasMany('App\Models\Member');
    }

    public function sales(): HasMany {
        return $this->hasMany('App\Models\Sale');
    }
}
