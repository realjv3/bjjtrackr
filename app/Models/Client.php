<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function events(): HasMany {
        return $this->hasMany('App\Models\Event');
    }

    public function checkins(): HasMany {
        return $this->hasMany('App\Models\Checkin');
    }

    public function users(): HasMany {
        return $this->hasMany('App\Models\User');
    }

    public function subscription(): HasOne {
        return $this->hasOne('App\Models\Subscription');
    }

    public function settings(): HasMany {
        return $this->hasMany('App\Models\Setting');
    }

    public function log(): HasMany {
        return $this->hasMany('App\Models\Log');
    }

    public function documents(): HasMany {
        return $this->hasMany('App\Models\Document');
    }

    public function getFirstAdmin() {

        return $this->users()->whereHas('roles', function ($q) {
            $q->where('role_id', 2);
        })->first();
    }
}
