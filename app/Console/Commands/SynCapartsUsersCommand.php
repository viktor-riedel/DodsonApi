<?php

namespace App\Console\Commands;

use App\Actions\Import\ImportCapartsUsersAction;
use Illuminate\Console\Command;

class SynCapartsUsersCommand extends Command
{
    protected $signature = 'sync:caparts-users';

    protected $description = 'This command syncs users from caparts to dodson';

    public function handle(): void
    {
        app()->make(ImportCapartsUsersAction::class)->handle();
    }
}
