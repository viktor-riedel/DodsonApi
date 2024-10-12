<?php

namespace App\Console\Commands;

use App\Actions\Import\LiveStockAction;
use Illuminate\Console\Command;

class ImportLiveStockCommand extends Command
{
    protected $signature = 'import:live-stock';

    protected $description = 'This command imports live parts stock';

    public function handle(): void
    {
        if (in_array(config('app.env'), ['production', 'staging', 'local'])) {
            app()->make(LiveStockAction::class)->handle();
        }
    }
}
