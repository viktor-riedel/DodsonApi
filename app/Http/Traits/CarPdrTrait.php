<?php

namespace App\Http\Traits;

use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\CarPdrPositionCard;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\PartList;
use Illuminate\Support\Collection;

trait CarPdrTrait
{
    private function buildCardPdrTree(Car $car, bool $defaultIcon = true): array
    {
        $pdr = $car->pdrs;
        $pdr->load('positions');
        return $this->recursivePDRTree($pdr->toArray(), 0, $defaultIcon);
    }

    private function recursivePDRTree(array $elements, $parent_id = 0, bool $default_icon = true): array
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
                                    'icon' => $default_icon ? 'pi pi-fw pi-cog' : 'pi pi-pw pi-book',
                                    'positions' => [],
                                    'is_card' =>  true,
                                    'ic_number' => $position->card->ic_number,
                                    'ic_description' => $position->card->description,
                                    'name_eng' => $position->card->name_eng,
                                    'name_ru' => $position->card->name_ru,
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

    private function buildPdrTreeWithoutEmpty(Car $car, bool $defaultIcon = true): array
    {
        $tree = $this->buildCardPdrTree($car, $defaultIcon);
        return $this->deleteEmptyItemsFromTree($tree);
    }

    private function loadAddDefaultList(array $elements, $parentId = 0): array
    {
        $branch = [];
        foreach ($elements as $el) {
            if ($el['parent_id'] === $parentId) {
                $children = $this->loadAddDefaultList($elements, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }
                $el['icon'] = $el['icon_name'];
                $branch[] = $el;
            }
        }
        return $branch;
    }

    private function buildDefaultPdrTreeByCar(Car $car): array
    {
        $baseCar = NomenclatureBaseItem::where('inner_id', $car->parent_inner_id)->first();
        $pdr = $baseCar->baseItemPDR;
        $pdr = $this->buildDefaultPdrTree($pdr);
        if (count($pdr)) {
            return $pdr;
        } else {
            $list = PartList::all();
            $pdr = $this->loadDefaultList($list->toArray());
        }
        return $pdr;
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

    private function buildDefaultPdrTree($pdr): array
    {
        $pdr->load('nomenclatureBaseItemPdrPositions');
        return $this->recursiveDefaultPDRTree($pdr->toArray());
    }

    private function recursiveDefaultPDRTree(array $elements, $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $el) {
            if ($el['parent_id'] === $parent_id) {
                $children = $this->recursiveDefaultPDRTree($elements, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }
                if ($el['is_folder']) {
                    $el['icon'] = 'pi pi-pw pi-folder';
                    $el['photos'] = $this->getPhotos([$el]);
                } else {
                    $el['icon'] = 'pi pi-fw pi-cog';
                }
                $count = 0;
                foreach($el['nomenclature_base_item_pdr_positions'] as $element) {
                    if (!$element['is_virtual']) {
                        $count++;
                    }
                }
                $el['key'] = $el['parent_id'] . '-'. $el['id'];
                $el['positions_count'] = $count;
                $branch[] = $el;
            }
        }

        return $branch;
    }

    private function deleteDefaultEmptyItemsFromTree(array &$elements, int $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $i => &$el) {
            if ($el['is_folder'] && isset($el['children']) && count($el['children'])) {
                $this->deleteDefaultEmptyItemsFromTree($el['children'], $el['id']);
            }
            if (!$el['is_folder'] && !count($el['nomenclature_base_item_pdr_positions'])) {
                unset($elements[$i]);
            } else if ($el['is_folder'] && isset($el['children']) && !count($el['children'])) {
                if (!count($el['nomenclature_base_item_pdr_positions'])) {
                    unset($elements[$i]);
                }
            } else if ($el['is_folder'] && !isset($el['children']) && count($el['nomenclature_base_item_pdr_positions']) === 1) {
                if ($el['nomenclature_base_item_pdr_positions'][0]['is_virtual']) {
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
                if (!isset($el['nomenclature_base_item_pdr_positions']) || !count($el['nomenclature_base_item_pdr_positions'])) {
                    unset($elements[$i]);
                }
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

    private function getPhotos(array $elements, &$photos = []): array
    {
        foreach ($elements as $el) {
            if (isset($el['children']) && count($el['children'])) {
                $photos = $this->getPhotos($el['children'], $photos);
            }
            if (isset($el['nomenclature_base_item_virtual_position'])) {
                if (count($el['nomenclature_base_item_virtual_position']['photos'])) {
                    $photos = $el['nomenclature_base_item_virtual_position']['photos'];
                }
            }
        }

        return $photos;
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
            car_pdrs.item_name_eng as folder,
            car_pdr_position_cards.parent_inner_id, 
            car_pdr_position_cards.name_eng, 
            car_pdr_position_cards.name_ru, 
            car_pdr_position_cards.ic_number,
            car_pdr_position_cards.oem_number, 
            car_pdr_position_cards.description as ic_description, 
            car_pdr_position_card_prices.price_currency,
            car_pdr_position_card_prices.buying_price,
            car_pdr_position_card_prices.selling_price,
            car_pdr_positions.user_id,
            car_pdr_position_cards.barcode,
            users.name as client_name,
            car_pdr_position_cards.comment')
            ->join('car_pdrs', 'car_pdrs.car_id', '=', 'cars.id')
            ->join('car_pdr_positions','car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
            ->join('car_pdr_position_cards', 'car_pdr_position_cards.car_pdr_position_id', '=', 'car_pdr_positions.id')
            ->leftJoin('users', 'users.id', '=', 'car_pdr_positions.user_id')
            ->join('car_pdr_position_card_prices', 'car_pdr_position_card_prices.car_pdr_position_card_id', '=', 'car_pdr_position_cards.id')
            ->where('cars.id', $car->id)
            ->whereNull('car_pdr_positions.deleted_at')
            ->get()->each(function($position) {
                $card = CarPdrPositionCard::with('images', 'createdBy', 'priceCard', 'partAttributesCard', 'comments', 'comments.createdBy')
                    ->find($position->id);
                $position->images = $card->images ?? [];
                $position->card = $card ?? null;
                $position->original_card = NomenclatureBaseItemPdrCard::where('ic_number', $card->ic_number)
                    ->where('description', $card->description)
                    ->where('name_eng', $card->name_eng)
                    ->first();
            });

        return $parts;
    }

    public function generateBarCode(): int
    {
        {
            $exist = true;
            $barcode = 0;
            while($exist) {
                $barcode = random_int(1000000, 6999999);
                $exist = CarPdrPositionCard::where('barcode', $barcode)->exists();
            }
            return $barcode;
        }
    }
}
