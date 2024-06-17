<?php

namespace App\Http\Traits;

use App\Models\PartList;
use App\Models\SellingMapItem;
use Illuminate\Support\Collection;

trait DefaultSellingMapTrait
{
    private function createMainFolders(array $folders = []): void
    {
        if (count($folders)) {
            $directories = SellingMapItem::where('parent_id', 0)->get();
            foreach ($folders as $folder) {
                $exist = $directories->where('item_name_eng', $folder)->first();
                if (!$exist) {
                    SellingMapItem::create([
                        'item_name_eng' => $folder,
                    ]);
                }
            }
        }
    }

    private function getDefaultSellingMap(): Collection
    {
        $directories = SellingMapItem::where('parent_id', 0)->get();
        foreach ($directories as $directory) {
            $directory->items = SellingMapItem::where('parent_id', $directory->id)->get();
        }
        return $directories;
    }

    private function getDefaultPartsListWithoutUsed(): Collection
    {
        $usedPartsInMap = SellingMapItem::where('parent_id', '>', 0)->get()
            ->pluck('item_name_eng')
            ->toArray();
        return PartList::where('is_folder', 0)
            ->whereNotIn('item_name_eng', $usedPartsInMap)
            ->orderBy('item_name_eng')
            ->get();
    }
}
