<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SetUserToAdminCommand extends Command
{
    protected $signature = 'set:user-to-admin {user}';

    protected $description = 'This command sets users permissions to admin';

    public function handle(): void
    {
        $user = User::find($this->argument('user'));
        if ($user) {
            if (!$user->hasRole('Admin')) {
                $user->removeRole('USER');
                $user->assignRole(['ADMIN']);
                $this->info('done');
            }
        }
    }
}
