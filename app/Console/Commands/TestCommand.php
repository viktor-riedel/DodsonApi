<?php

namespace App\Console\Commands;

use App\Http\ExternalApiHelpers\CapartsApiHelper;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $helper = new CapartsApiHelper();
        $result = $helper->fundCarByStockNumber('BZ0058');
        dd($result);
    }
}
