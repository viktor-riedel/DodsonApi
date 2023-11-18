<?php

namespace App\Console\Commands;

use App\Http\ExternalApiHelpers\CapartsApiHelper;
use App\Http\ExternalApiHelpers\FindCarsInOneC;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $helper = new FindCarsInOneC();
        $result = $helper->findCarByStockNumber('KI0923');
        if ($result['car']) {
            dd($result['car']);
        }
        $this->info('nothing');
    }
}
