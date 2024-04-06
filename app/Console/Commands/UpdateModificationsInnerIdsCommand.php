<?php

namespace App\Console\Commands;

use App\Http\Traits\InnerIdTrait;
use App\Http\Traits\SyncBaseItemModificationsTrait;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use Illuminate\Console\Command;

class UpdateModificationsInnerIdsCommand extends Command
{
    use InnerIdTrait, SyncBaseItemModificationsTrait;

    protected $signature = 'update:modifications-inner-ids';

    protected $description = 'This command sets inner ids for modifications';

    public function handle(): void
    {
        $mods = NomenclatureBaseItemModification::withTrashed()->get();
        foreach($mods as $mod) {
            $mod->update([
                'inner_id' => $this->generateInnerId(
                    $mod->header . $mod->generation . $mod->chassis
                ),
            ]);
        }

        $this->info('done');
    }
}
