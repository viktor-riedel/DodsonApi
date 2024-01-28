<?php

namespace App\Actions\BaseItemPosition;

use App\Models\NomenclatureBaseItem;
use Illuminate\Support\Collection;

class BaseItemIcListAction
{
    public function handle(NomenclatureBaseItem $baseItemPdr): Collection
    {
        $positions = $baseItemPdr->nomenclaturePositions()
            ->with(['photos', 'nomenclatureBaseItemPdrCard', 'nomenclatureBaseItemModifications'])
            ->where('is_virtual', false)
            ->get();
        return $positions;
    }
}
