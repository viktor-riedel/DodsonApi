<?php

namespace App\Actions\BaseItemPosition;

use App\Models\NomenclatureBaseItem;

class BaseItemIcListAction
{
    public function handle(NomenclatureBaseItem $baseItemPdr)
    {
        $positions = $baseItemPdr->nomenclaturePositions()
            ->with(['photos', 'nomenclatureBaseItemPdrCard', 'nomenclatureBaseItemModifications'])
            ->where('is_virtual', false)
            ->get();
        return $positions;
    }
}
