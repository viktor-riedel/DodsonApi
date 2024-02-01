<?php

namespace App\Console\Commands;

use App\Models\NomenclatureBaseItemPdrCard;
use Illuminate\Console\Command;

class UpdateRussianNomenclatureNamesCommand extends Command
{
    protected $signature = 'update:russian-nomenclature-names';

    protected $description = 'This command updates russian names in cards';

    public function handle(): void
    {
        $cards = NomenclatureBaseItemPdrCard::with('nomenclatureBaseItemPdrPosition')->get();
        foreach($cards as $card) {
            if (!$card->name_ru && $card->nomenclatureBaseItemPdrPosition
                    && $card->nomenclatureBaseItemPdrPosition->item_name_ru
                    && $card->nomenclatureBaseItemPdrPosition->item_name_ru !== '') {
                $this->info('Update card ' . $card->id . ' to ' . $card->nomenclatureBaseItemPdrPosition->item_name_ru);
                $card->update(['name_ru' => $card->nomenclatureBaseItemPdrPosition->item_name_ru]);
            }
        }
    }
}
