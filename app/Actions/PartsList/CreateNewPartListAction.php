<?php

namespace App\Actions\PartsList;

use App\Models\PartList;

class CreateNewPartListAction
{
    public function handle(array $request): void
    {
        $part = PartList::create([
            'parent_id' => 0,
            'item_name_eng' => strtoupper(trim($request['item_name_eng'])),
            'item_name_ru' => mb_strtoupper(trim($request['item_name_ru'])),
            'is_folder' => false,
            'is_virtual' => false,
            'icon_name' => 'pi pi-fw pi-cog',
            'key' => null,
            'is_used' => true,
        ]);

        $part->update(['key' => '0-' . $part->id]);
    }
}
