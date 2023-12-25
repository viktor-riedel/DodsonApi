<?php

namespace App\Actions\BaseItem;

use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class BaseItemModificationsUpdateAction
{
    public function handle(Request $request, NomenclatureBaseItem $nomenclatureBaseItem)
    {
        $positions = $nomenclatureBaseItem->load('baseItemPDR', 'baseItemPDR.nomenclatureBaseItemPdrPositions');
        foreach($positions->baseItemPDR as $pdr) {
            foreach ($pdr->nomenclatureBaseItemPdrPositions as $position) {
                $position->nomenclatureBaseItemModifications()->delete();
            }
        }
        if (count($request->toArray())) {
            foreach($positions->baseItemPDR as $pdr) {
                foreach ($pdr->nomenclatureBaseItemPdrPositions as $position) {
                    if (!$position->is_virtual) {
                        foreach($request->toArray() as $modification) {
                            $position->nomenclatureBaseItemModifications()->create($modification);
                        }
                    }
                }
            }
        }
    }
}
