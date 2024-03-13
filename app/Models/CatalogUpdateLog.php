<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogUpdateLog extends Model
{
    protected $fillable = [
        'ip_address',
        'agent',
        'api_point',
        'user_id',
        'packet',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
