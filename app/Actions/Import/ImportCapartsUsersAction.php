<?php

namespace App\Actions\Import;

use App\Http\ExternalApiHelpers\CapartsApiHelper;
use App\Models\User;

class ImportCapartsUsersAction
{
    public function handle(): void
    {
        $helper = new CapartsApiHelper();

        $users = $helper->getUsersList();
        foreach($users as $user) {
            $dbUser = User::where('email', trim($user['email']))->withTrashed()->first();
            if (!$dbUser) {
                $newUser = User::create([
                    'name' => $user['user_info']['last_name'] . ' ' . $user['user_info']['first_name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'country_code' => $user['user_info']['country_code'],
                    'is_api_user' => false,
                    'first_name' => $user['user_info']['first_name'],
                    'last_name' => $user['user_info']['last_name'],
                ]);
                $newUser->userCard()->create([
                    'mobile_phone' => $user['phone'],
                    'landline_phone' => null,
                    'company_name' => $user['company_name'],
                    'trading_name' => $user['trading_name'],
                    'country' => $user['user_info']['country'],
                    'wholesaler' => (bool) $user['is_whole_seller'],
                ]);
            }
        }
    }
}
