<?php

namespace App\Helpers;

use App\Models\User;
use Cache;

class Consts
{
    public const DODSON_USER_KEY = 'DODSON_USER';

    public static function getPartsSaleUserId(): int
    {
        if (Cache::has(self::DODSON_USER_KEY)) {
            return Cache::get(self::DODSON_USER_KEY);
        }
        $id = User::whereHas("userCard", static function ($q) {
            return $q->where("parts_sale_user", 1);
        })->first()?->id;

        if ($id) {
            Cache::put(self::DODSON_USER_KEY, $id);
        }

        return $id;
    }
}
