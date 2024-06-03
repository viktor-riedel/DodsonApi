<?php

namespace App\Actions\Api;

use App\Models\PartList;

class PartsListAction
{
    public function handle(): array
    {
        $list = PartList::all();
        return $this->getRecursiveList($list->toArray());
    }

    private function getRecursiveList(array $parts, $parentId = 0): array
    {
        $branch = [];
        foreach ($parts as $el) {
            if ($el['parent_id'] === $parentId) {
                $children = $this->getRecursiveList($parts, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }
                $branch[] = [
                    'id' => $el['id'],
                    'is_folder' => $el['is_folder'],
                    'item_name_eng' => $el['item_name_eng'],
                    'item_name_ru' => $el['item_name_ru'],
                    "key" => $el['key'],
                    'children' => $el['children'] ?? [],
                ];
            }
        }
        return $branch;
    }
}
