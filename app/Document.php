<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'file_name',
        'original_name',
        'template_id',
        'status',
        'contract_id',
        'contract_pdf_url',
    ];

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
