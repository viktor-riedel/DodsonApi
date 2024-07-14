<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyRate extends Model
{
    use SoftDeletes;

    public const CURRENCIES = [
      'MNT' => 'MNT',
      'RUB' => 'RUB',
      'NZD' => 'NZD',
      'EUR' => 'EUR',
      'USD' => 'USD',
    ];

    protected $fillable = [
        'currency_code',
        'rate_from_jpy',
        'rate_to_jpy',
    ];
}
