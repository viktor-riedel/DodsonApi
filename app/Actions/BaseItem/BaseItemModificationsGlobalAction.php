<?php

namespace App\Actions\BaseItem;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Traits\BaseItemModificationsTrait;
use App\Models\NomenclatureBaseItem;

class BaseItemModificationsGlobalAction
{
    use BaseItemModificationsTrait;

    public function handle(NomenclatureBaseItem $nomenclatureBaseItem, CatalogApiHelper $apiHelper): array
    {
        $result = [
            'ic_list' => [],
            'modifications' => [],
        ];

        $iclist = $nomenclatureBaseItem->nomenclaturePositions()->where('is_virtual', false)->get();
        foreach($iclist as $listItem) {
            $listItem->item_name_ru = $listItem->nomenclatureBaseItemPdr?->item_name_ru;
            $listItem->item_name_eng = $listItem->nomenclatureBaseItemPdr?->item_name_eng;
            $listItem->oem_number = $listItem->nomenclatureBaseItemPdrCard?->oem_number;
            $listItem->count_modifications = $listItem->nomenclatureBaseItemModifications->count();
        }

        $result['ic_list'] = $iclist;
        $result['modifications'] = $this->loadAvailableModificationByBaseItem($nomenclatureBaseItem, $apiHelper);
        return $result;
    }
}
