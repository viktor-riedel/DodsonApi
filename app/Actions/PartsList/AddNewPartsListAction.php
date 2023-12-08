<?php

namespace App\Actions\PartsList;

use App\Models\PartList;

class AddNewPartsListAction
{
    public function handle(array $request, PartList $parent): void
    {
        if (!$parent->is_folder) {
            $parent->update([
               'is_folder' => true,
               'icon_name' => 'pi pi-pw pi-folder'
            ]);
        }

        $part = PartList::create([
            'parent_id' => $parent->id,
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
