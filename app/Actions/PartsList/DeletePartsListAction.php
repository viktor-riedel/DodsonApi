<?php

namespace App\Actions\PartsList;

use App\Models\PartList;

class DeletePartsListAction
{
    public function handle(PartList $part): void
    {
        if (!$part->is_folder) {
            $part->delete();
        } else {
            //recursive delete
            $items = PartList::where('parent_id', $part->id)->get()->toArray();
            $this->deleteNodesRecursive($items, $part->id);
            $part->delete();
        }
    }

    private function deleteNodesRecursive(array $items, $parentId = 0): void
    {
        foreach ($items as $el) {
            if ($el['parent_id'] === $parentId) {
                $subItems = PartList::where('parent_id', $el['id'])->get()->toArray();
                $this->deleteNodesRecursive($subItems, $el['id']);
            }

            PartList::find($el['id'])?->delete();
        }
    }
}
