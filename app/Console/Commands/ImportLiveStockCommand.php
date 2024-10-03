<?php

namespace App\Console\Commands;

use App\Actions\Import\LiveStockAction;
use Illuminate\Console\Command;

class ImportLiveStockCommand extends Command
{
    protected $signature = 'import:live-stock';

    protected $description = 'Command description';

    public function handle(): void
    {
        if (config('app.env') === 'production') {
            app()->make(LiveStockAction::class)->handle();
        }
    }
}
