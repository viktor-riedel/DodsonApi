<?php

namespace App\Http\Traits;

use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\CarPdrPositionCard;
use Illuminate\Support\Collection;

trait CarPdrTrait
{
    private function buildCardPdrTree(Car $car): array
    {
        $pdr = $car->pdrs;
        $pdr->load('positions');
        return $this->recursivePDRTree($pdr->toArray());
    }

    private function recursivePDRTree(array $elements, $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $el) {
            if ($el['parent_id'] === $parent_id) {
                $children = $this->recursivePDRTree($elements, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }
                if ($el['is_folder']) {
                    $el['icon'] = 'pi pi-pw pi-folder';
                    $el['photos'] = $this->loadPhotos([$el]);
                } else {
                    $el['icon'] = 'pi pi-fw pi-cog';
                }
                $count = 0;
                foreach($el['positions'] as $element) {
                    if (!$element['is_virtual']) {
                        $count++;
                    }
                }
                $el['key'] = $el['parent_id'] . '-'. $el['id'];

                $positions = CarPdrPosition::where('car_pdr_id', $el['id'])->get();
                if ($positions->count()) {
                    foreach($positions as $position) {
                        if ($position->card)  {
                            $el['children'][] =
                                [
                                    'id' => $position->card->id,
                                    'is_folder' => true,
                                    'key' => $el['key'] = $el['parent_id'] . '-'. $el['id'] . '-' . $position->card->id,
                                    'icon' => 'pi pi-pw pi-book',
                                    'positions' => [],
                                    'is_card' =>  true,
                                    'ic_number' => $position->card->ic_number,
                                    'ic_description' => $position->card->description,
                                    'name_eng' => $position->card->name_ru,
                                    'name_ru' => $position->card->name_eng,
                                    'card' => $position->card->load('priceCard', 'partAttributesCard', 'images'),
                                ];
                        }
                    }
                }
                $el['positions_count'] = $count;
                $branch[] = $el;
            }
        }

        return $branch;
    }

    private function buildPdrTreeWithoutEmpty(Car $car): array
    {
        $tree = $this->buildCardPdrTree($car);
        return $this->deleteEmptyItemsFromTree($tree);
    }

    private function deleteEmptyItemsFromTree(array &$elements, $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $i => &$el) {
            if ($el['is_folder'] && isset($el['children']) && count($el['children'])) {
                $this->deleteEmptyItemsFromTree($el['children'], $el['id']);
            }
            if (!$el['is_folder'] && !count($el['positions'])) {
                unset($elements[$i]);
            } else if ($el['is_folder'] && isset($el['children']) && !count($el['children'])) {
                unset($elements[$i]);
            } else if ($el['is_folder'] && !isset($el['children']) && count($el['positions']) === 1) {
                if ($el['positions'][0]['is_virtual']) {
                    unset($elements[$i]);
                }
            } else {
                $el['key'] = $parent_id . '-'. $el['id'];
                if (isset($el['children'])) {
                    $el['children'] = array_values($el['children']);
                }
                $branch[] = $el;
            }
        }
        return $branch;
    }

    private function loadPhotos(array $elements, &$photos = []): array
    {
        foreach ($elements as $el) {
            if (isset($el['children']) && count($el['children'])) {
                $photos = $this->loadPhotos($el['children'], $photos);
            }
            if (isset($el['positions'])) {
//                ray($el['positions']);
//                if (count($el['positions']['photos'])) {
//                    $photos = $el['positions']['images'];
//                }
            }
        }

        return $photos;
    }

    private function getPartsList(Car $car): Collection
    {
        $parts = \DB::table('cars')
            ->selectRaw('car_pdr_position_cards.id, 
            car_pdr_position_cards.parent_inner_id, 
            car_pdr_position_cards.name_eng, 
            car_pdr_position_cards.name_ru, 
            car_pdr_position_cards.ic_number,
            car_pdr_position_cards.oem_number, 
            car_pdr_position_cards.description as ic_description, 
            car_pdr_position_cards.comment')
            ->join('car_pdrs', 'car_pdrs.car_id', '=', 'cars.id')
            ->join('car_pdr_positions','car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
            ->join('car_pdr_position_cards', 'car_pdr_position_cards.car_pdr_position_id', '=', 'car_pdr_positions.id')
            ->where('cars.id', $car->id)
            ->whereNull('car_pdr_positions.deleted_at')
            ->get()->each(function($position) {
                $card = CarPdrPositionCard::with('images', 'priceCard', 'partAttributesCard')
                    ->find($position->id);
                $position->images = $card->images ?? [];
                $position->card = $card ?? null;
            });

        return $parts;
    }
}
