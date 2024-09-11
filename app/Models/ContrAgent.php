<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContrAgent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'alias',
        'person_name',
        'country',
        'email',
        'phone',
        'fax',
        'address',
    ];

    protected $hidden = ['create_at', 'updated_at', 'deleted_at'];
}
