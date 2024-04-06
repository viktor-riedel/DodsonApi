<?php

namespace App\Console\Commands;

use App\Http\Traits\InnerIdTrait;
use App\Http\Traits\SyncBaseItemModificationsTrait;
use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrPosition;
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

        $positions = NomenclatureBaseItemPdrPosition::with('nomenclatureBaseItemModifications')
            ->withTrashed()->get();

        foreach($positions as $position) {
            $position->modifications()->delete();
            foreach($position->nomenclatureBaseItemModifications as $mod) {
                $position->modifications()->create($mod->toArray());
            }
        }

        $this->info('done');
    }
}
