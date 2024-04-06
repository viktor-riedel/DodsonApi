<?php

namespace App\Console\Commands;

use App\Http\Traits\SyncBaseItemModificationsTrait;
use App\Models\NomenclatureBaseItem;
use Illuminate\Console\Command;

class SyncInnerIdsWithBaseItemsCommand extends Command
{
    protected $signature = 'sync:inner-ids-with-base-items';

    protected $description = 'This command syncs modifications';

    use SyncBaseItemModificationsTrait;

    public function handle(): void
    {
        $baseItems = NomenclatureBaseItem::withTrashed()->get();
        foreach($baseItems as $baseItem) {
            $this->syncBaseItemModifications($baseItem);
        }
        $this->info('done');
    }
}
