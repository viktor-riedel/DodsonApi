<?php

namespace App\Console\Commands;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\PartList;
use Illuminate\Console\Command;

class UpdateRussianNomenclatureNamesCommand extends Command
{
    protected $signature = 'update:russian-nomenclature-names {baseId}';

    protected $description = 'This command updates russian names in cards';

    public function handle(): void
    {
        $cards = NomenclatureBaseItemPdrCard::with('nomenclatureBaseItemPdrPosition')->get();
        $defaultItems = PartList::get();
        $baseItemPdr = NomenclatureBaseItem::with('baseItemPDR')->find($this->argument('baseId'));
        //update pdr first
        foreach($baseItemPdr->baseItemPDR as $pdrItem) {
            if (!$pdrItem->item_name_ru) {
                $ru_name_default = $defaultItems->where('item_name_eng', $pdrItem->item_name_eng)->first();
                $pdrItem->update(['item_name_ru' => $ru_name_default?->item_name_ru]);
            }
        }

        foreach($cards as $card) {
            if ((!$card->name_ru || !$card->name_eng) && $card->nomenclatureBaseItemPdrPosition) {
                $this->info('Update card ' . $card->id . ' to ' . $card->nomenclatureBaseItemPdrPosition->item_name_ru);
                if (!$card->name_ru && !$card->name_eng) {
                    $card->update([
                        'name_ru' => $card->nomenclatureBaseItemPdrPosition->item_name_ru,
                        'name_eng' => $card->nomenclatureBaseItemPdrPosition->item_name_eng,
                    ]);
                } else if (!$card->name_ru && $card->name_eng) {
                    $card->update([
                        'name_ru' => $card->nomenclatureBaseItemPdrPosition->item_name_ru,
                    ]);
                }
            } else if ($card->name_ru && !$card->name_eng) {
                $card->update([
                    'name_eng' => $card->nomenclatureBaseItemPdrPosition->item_name_eng,
                ]);
            } else if ($card->nomenclatureBaseItemPdrPosition) {
                $pdr = $card->nomenclatureBaseItemPdrPosition->nomenclatureBaseItemPdr;
                if ($pdr && $pdr->item_name_ru) {
                    $this->info('Update card using PDR ' . $card->id . ' to ' . $pdr->item_name_ru);
                    $card->update([
                        'name_ru' => $pdr->item_name_ru,
                        'name_eng' => $pdr->item_name_eng,
                    ]);
                }
            }
        }
    }
}
