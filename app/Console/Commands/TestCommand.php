<?php

namespace App\Console\Commands;

use App\Http\ExternalApiHelpers\FindCarsInOneC;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $item = \App\Models\NomenclatureBaseItem::find(50);
        $item->load('baseItemPDR');
        $pos = $item->baseItemPDR()->where('is_folder', true)
            ->with(['nomenclatureBaseItemVirtualPosition', 'nomenclatureBaseItemVirtualPosition.photos'])
            ->get();
        dd($pos);
    }
}
