<?php

namespace App\Actions\PartsList;

use App\Models\PartList;

class PartsListDefaultAction
{
    private const DEFAULT_PDR = [
      [
          'parent_id' => 0,
          'item_name_eng' => 'FRONT END ASSY',
          'item_name_ru' => '',
          'is_folder' => 1,
          'children' => [
              [
                'parent_id' => 0,
                'item_name_eng' => 'FRONT BUMPER',
                'item_name_ru' => '',
                'is_folder' => 0,
                'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'LEFT HEADLAMP',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'RIGHT HEADLAMP',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'GRILLE',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'FRONT BAR BRKT/REINFORCEMENT',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'RADIATOR SUPPORT',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'RADIATOR',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'A/C CONDENSER',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
          ],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'BONNET',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'RIGHT GUARD',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'RIGHT GUARD LINER',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'LEFT GUARD',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'LEFT GUARD LINER',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'LEFT FRONT DOOR',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'RIGHT FRONT DOOR',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'RIGHT DOOR MIRROR',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'LEFT DOOR MIRROR',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'LEFT REAR DOOR',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'RIGHT REAR DOOR',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'BOOTLID/TAILGATE',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'REAR BUMPER',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'REAR BAR BRKT/REINFORCEMENT',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'LEFT TAILLIGHT',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'RIGHT TAILLIGHT',
          'item_name_ru' => '',
          'is_folder' => 0,
          'children' => [],
      ],
      [
          'parent_id' => 0,
          'item_name_eng' => 'ENGINE WITH GEARBOX',
          'item_name_ru' => '',
          'is_folder' => 1,
          'children' => [
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'ENGINE',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ],
              [
                  'parent_id' => 0,
                  'item_name_eng' => 'TRANS/GEARBOX',
                  'item_name_ru' => '',
                  'is_folder' => 0,
                  'children' => [],
              ]
          ],
      ],
    ];

    public function handle(): array
    {
        $list = PartList::all();
        if (!$list->count()) {
            $this->createDefaultList(self::DEFAULT_PDR);
            $list = PartList::all();
        }

        return $this->loadDefaultList($list->toArray());
    }

    private function createDefaultList(array $elements, $parentId = 0): void
    {
        foreach ($elements as $el) {
            if (isset($el['children']) && count($el['children'])) {
                $el['icon'] = 'pi pi-pw pi-folder';
            } else {
                $el['icon'] = 'pi pi-fw pi-cog';
            }

            $part = PartList::create([
                    'parent_id' => $parentId,
                    'item_name_eng' => $el['item_name_eng'],
                    'item_name_ru' => $el['item_name_ru'],
                    'is_folder' => $el['is_folder'],
                    'is_virtual' => false,
                    'icon_name' => $el['icon'],
                    'key' => null,
                    'is_used' => true
                ]);

            $el['key'] = $el['parent_id'] . '-'. $part->id;
            $part->update(['key' => $el['key']]);

            if (isset($el['children']) && count($el['children'])) {
                $this->createDefaultList($el['children'], $part->id);
            }
        }
    }

    private function loadDefaultList(array $elements, $parentId = 0): array
    {
        $branch = [];
        foreach ($elements as $el) {
            if ($el['parent_id'] === $parentId) {
                $children = $this->loadDefaultList($elements, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }
                $el['icon'] = $el['icon_name'];
                $branch[] = $el;
            }
        }
        return $branch;
    }
}
