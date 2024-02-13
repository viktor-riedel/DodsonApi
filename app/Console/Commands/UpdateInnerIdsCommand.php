<?php

namespace App\Console\Commands;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrCard;
use Illuminate\Console\Command;

class UpdateInnerIdsCommand extends Command
{
    protected $signature = 'update:inner-ids';

    protected $description = 'this command updates inner ids';

    public function handle(): void
    {
        $baseItems = NomenclatureBaseItem::whereNull('inner_id')
                ->orWhere('inner_id', '')->get();
        foreach($baseItems as $baseItem) {
            $innerId =  $baseItem->generateInnerId(
                $baseItem->make . $baseItem->model . $baseItem->generation . $baseItem->created_at
            );
            $baseItem->setInnerId($innerId);
        }
        $this->info('updated ' . $baseItems->count());

        $this->info('*************update cards******************');
        //exclude virtual cards
        $cards = NomenclatureBaseItemPdrCard::whereNull('inner_id')
            ->whereNotNull('name_eng')->get();
        foreach($cards as $card) {
            $innerId =  $card->generateInnerId(
                $card->name_eng . $card->name_ru . $card->ic_number .
                $card->decsription . $card->created_at
            );
            $card->setInnerId($innerId);
        }
        $this->info('updated ' . $cards->count() . ' cards');
    }
}
