<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMeTemplate extends Model
{
    use SoftDeletes;

    public const DEFAULT_DURATION = [
        'Two' => '2',
        'Three' => '3',
        'Four' => '4',
        'Five' => '5',
        'Six' => '6',
        'Seven' => '7',
        'Ten' => '10',
        'Fourteen' => '14',
    ];

    public const SEND_PAYMENT_INSTRUCTIONS = true;
    public const SHIPPING_METHODS = [
        'Undecided' => '0',
        'Pickup' => '1',
        'Free' => '3',
    ];

    public const PAYMENT_METHODS = [
        'None' => '0',
        'Bank Deposit' =>  '1',
        'Credit Card' => '2',
        'Cash' => '4',
        'Ping' => '32',
        'Afterpay' => '64',
    ];

    public const REPLACE_TAGS = [
        'part_name' => '{part_name}',
        'make' => '{make}',
        'model' => '{model}',
        'year' => '{year}',
        'tag' => '{tag}',
        'color' => '{color}',
        'body_style' => '{body_style}',
        'vehicle' => '{vehicle}',
        'transmission' => '{transmission}',
        'engine_size' => '{engine_size}',
        'engine_code' => '{engine_code}',
        'description' => '{description}',
        'price' => '{price}',
    ];

    public const SUPPORTED_CHARACTERS = [
        'Italic' => "*I'm italic*",
        'Bold' => "**I'm bold**",
        'Bold and Italic' => "***I'm bold and Italic***",
        'Header' => "#I'm a heading",
        'Struct through' => "~~I'm struck through~~",
        'Bullet items' => '- Bulleted list item 1',
    ];

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'delivery_options',
        'default_duration',
        'payments_options',
        'update_prices',
        'relist',
    ];

    protected $casts = [
        'relist' => 'boolean',
        'update_prices' => 'boolean',
        'pickup_option' => 'boolean',
    ];

    public function getDeliveryOptionsArrayAttribute(): array
    {
        if ($this->delivery_options) {
            return explode(',', $this->delivery_options);
        }

        return [];
    }

    public function getPaymentOptionsArrayAttribute(): array
    {
        if ($this->payments_options) {
            return explode(',', $this->payments_options);
        }

        return [];
    }
}
