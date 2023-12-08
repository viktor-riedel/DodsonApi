<?php

namespace App\Actions\PartsList;

use App\Models\PartList;

class UpdatePartsListAction
{
    public function handle(array $request, PartList $partList): void
    {
        $partList->update([
            'item_name_eng' => strtoupper(trim($request['item_name_eng'])),
            'item_name_ru' => mb_strtoupper(trim($request['item_name_ru'])),
        ]);
    }
}
