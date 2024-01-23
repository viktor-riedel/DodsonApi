<?php

namespace App\Actions\ReadyCars;

use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Support\Collection;

class ReadyCarPartsAction
{
    use BaseItemPdrTreeTrait;

    public function handle(string $make, string $model, string $generation, string $header = null): array
    {
        $item = NomenclatureBaseItem::with('baseItemPDR', 'baseItemPDR.nomenclatureBaseItemPdrPositions',
            'baseItemPDR.nomenclatureBaseItemPdrPositions.nomenclatureBaseItemPdrCard',
            'baseItemPDR.nomenclatureBaseItemPdrPositions.nomenclatureBaseItemModifications')
            ->where('make', $make)
            ->where('model', $model)
            ->where('generation', $generation)
            ->first();
        if ($header) {
            foreach ($item->baseItemPDR as $positionItem) {
                if ($positionItem->nomenclatureBaseItemPdrPositions->count()) {
                    foreach ($positionItem->nomenclatureBaseItemPdrPositions as $position) {
                        $accepted = false;
                        if (!$position->nomenclatureBaseItemModifications->count()) {
                            $accepted = true;
                        } else {
                            foreach ($position->nomenclatureBaseItemModifications as $modification) {
                                if ($modification->header === $header) {
                                    $accepted = true;
                                }
                            }
                        }
                        $position->accepted = $accepted;
                    }
                }
            }
            $item->baseItemPDR->filter(function($pdr) {
                return $pdr->nomenclatureBaseItemPdrPositions->filter(function($position) {
                    return $position->accepted;
                });
            });
        }
        return $this->buildPdrTree($item->baseItemPDR);
    }
}
