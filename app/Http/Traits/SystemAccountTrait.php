<?php

namespace App\Http\Traits;

use App\Models\User;

trait SystemAccountTrait
{
    private function getSystemAccount(): User
    {
        $user = User::where('system_account', true)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'system account',
                'email' => 'system-account@example.com',
                'password' => bcrypt('qwerty333'),
                'reset_code' => null,
                'last_login_at' => null,
                'country_code' => 'RU',
                'is_api_user' =>  false,
                'first_name' => 'system',
                'last_name' => 'account',
                'system_account' => true,
            ]);
            $user->userCard()->create([
                'country' => 'Russia',
                'wholesaler' => false,
            ]);
        }
        return $user;
    }
}
