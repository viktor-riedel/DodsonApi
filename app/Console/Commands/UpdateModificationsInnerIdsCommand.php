<?php

namespace App\Console\Commands;

use App\Http\Traits\InnerIdTrait;
use App\Models\NomenclatureBaseItemModification;
use Illuminate\Console\Command;

class UpdateModificationsInnerIdsCommand extends Command
{
    use InnerIdTrait;

    protected $signature = 'update:modifications-inner-ids';

    protected $description = 'This command sets inner ids for modifications';

    public function handle(): void
    {
        $mods = NomenclatureBaseItemModification::whereNull('inner_id')->get();
        foreach($mods as $mod) {
            $mod->update([
                'inner_id' => $this->generateInnerId($mod->id . $mod->created_at),
            ]);
        }
        $this->info('done');
    }
}
