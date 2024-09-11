<?php

namespace App\Console\Commands;

use App\Jobs\Sync\GetUserBalanceJob;
use App\Models\User;
use Illuminate\Console\Command;

class SyncUserBalanceCommand extends Command
{
    protected $signature = 'sync:user-balance';

    protected $description = 'This command syncs user balance with 1C';

    public function handle(): void
    {
        if (app()->environment('production')) {
            $users = User::with('userCard')->get();
            foreach ($users as $user) {
                if (!$user->userCard) {
                    $user->userCard()->create([]);
                }
                if ($user->userCard->trading_name) {
                    GetUserBalanceJob::dispatch($user);
                }
            }
        }
    }
}
