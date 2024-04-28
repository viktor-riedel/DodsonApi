<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\CarPdrPositionCard;
use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureModification;

class AddListPartsAction
{
    use InnerIdTrait;

    private int $userId = 0;
    private NomenclatureModification $modification;

    public function handle(Car $car, int $userId, array $parts = []): void
    {
        $this->userId = $userId;
        $this->modification = $car->modifications;
        $this->createPartsRecursive($parts, $car);
    }

    private function createPartsRecursive(array $parts, Car $car, int $parentId = 0): void
    {
        foreach ($parts as $part) {
            if (isset($part['nomenclature_base_item_pdr_positions'])) {
                $pdr = $car->pdrs()->create([
                    'parent_id' => $parentId,
                    'item_name_eng' => $part['item_name_eng'],
                    'item_name_ru' => $part['item_name_ru'],
                    'is_folder' => $part['is_folder'],
                    'is_deleted' => false,
                    'parts_list_id' => $part['id'],
                    'created_by' => $this->userId,
                ]);
                if (count($part['nomenclature_base_item_pdr_positions'])) {
                    foreach ($part['nomenclature_base_item_pdr_positions'] as $nom_position) {
                        if (!$nom_position['is_virtual']) {
                            if ($pdr) {
                                $position = $pdr->positions()->create([
                                    'item_name_ru' => $nom_position['item_name_ru'] ?? $part['item_name_ru'],
                                    'item_name_eng' => $nom_position['item_name_eng'] ?? $part['item_name_eng'],
                                    'ic_number' => $nom_position['is_virtual'] ? '' : $nom_position['ic_number'],
                                    'oem_number' => $nom_position['is_virtual'] ? '' : $nom_position['oem_number'] ?? '',
                                    'ic_description' => $nom_position['is_virtual'] ? '' : $nom_position['ic_description'] ?? '',
                                    'is_virtual' => $nom_position['is_virtual'],
                                    'created_by' => $this->userId,
                                ]);
                            } else {
                                $position = CarPdrPosition::create([
                                    'car_pdr_id' => $parentId,
                                    'item_name_ru' => $nom_position['item_name_ru'] ?? $part['item_name_ru'],
                                    'item_name_eng' => $nom_position['item_name_eng'] ?? $part['item_name_eng'],
                                    'ic_number' => $nom_position['is_virtual'] ? '' : $nom_position['ic_number'],
                                    'oem_number' => $nom_position['is_virtual'] ? '' : $nom_position['oem_number'] ?? '',
                                    'ic_description' => $nom_position['is_virtual'] ? '' : $nom_position['ic_description'] ?? '',
                                    'is_virtual' => $nom_position['is_virtual'],
                                    'created_by' => $this->userId,
                                ]);
                            }
                            $position->modification()->create($this->modification->toArray());
                            $originCard = NomenclatureBaseItemPdrCard::where('nomenclature_base_item_pdr_position_id', $nom_position['id'])
                                ->first();
                            $card = $position->card()->create([
                                'car_pdr_position_id' => $position->id,
                                'parent_inner_id' => $originCard?->inner_id,
                                'name_eng' => $originCard->name_eng ?? $part['item_name_eng'],
                                'name_ru' => $originCard->name_ru ?? $part['item_name_ru'],
                                'comment' => $originCard->comment ?? null,
                                'description' => $originCard?->description,
                                'ic_number' => $originCard?->ic_number,
                                'oem_number' => $originCard?->oem_number,
                                'created_by' => $this->userId,
                            ]);
                            $this->createCardStructures($card, $originCard);
                        }
                    }
                } else {
                    $position = CarPdrPosition::create([
                        'car_pdr_id' => $parentId,
                        'item_name_ru' => $part['item_name_ru'],
                        'item_name_eng' => $part['item_name_eng'],
                        'ic_number' => null,
                        'oem_number' => null,
                        'ic_description' => null,
                        'is_virtual' => false,
                        'created_by' => $this->userId,
                    ]);
                    $originCard = NomenclatureBaseItemPdrCard::where('id', $part['id'])
                        ->first();
                    $card = $position->card()->create([
                        'car_pdr_position_id' => $position->id,
                        'parent_inner_id' => null,
                        'name_eng' => $originCard->name_eng ?? $part['item_name_eng'],
                        'name_ru' => $originCard->name_ru ?? $part['item_name_ru'],
                        'comment' => $originCard->comment ?? null,
                        'description' => $originCard?->description,
                        'ic_number' => $originCard?->ic_number,
                        'oem_number' => $originCard?->oem_number,
                        'created_by' => $this->userId,
                    ]);
                    $this->createCardStructures($card, $originCard);
                }
            } else {
                $pdr = $car->pdrs()->create([
                    'parent_id' => $parentId,
                    'item_name_eng' => $part['item_name_eng'],
                    'item_name_ru' => $part['item_name_ru'] ?? '',
                    'is_folder' => $part['is_folder'],
                    'is_deleted' => false,
                    'parts_list_id' => $part['id'],
                    'created_by' => $this->userId,
                ]);
                $position = $pdr->positions()->create([
                    'item_name_ru' => $part['item_name_ru'] ?? '',
                    'item_name_eng' => $part['item_name_eng'],
                    'ic_number' => '',
                    'oem_number' => '',
                    'ic_description' => '',
                    'is_virtual' => false,
                    'created_by' => $this->userId,
                ]);
                $card = $position->card()->create([
                    'car_pdr_position_id' => $position->id,
                    'parent_inner_id' => null,
                    'name_eng' => $part['item_name_eng'],
                    'name_ru' => $part['item_name_ru'] ?? '',
                    'comment' => null,
                    'description' => '',
                    'ic_number' => '',
                    'oem_number' => '',
                    'created_by' => $this->userId,
                ]);
                $this->createCardStructures($card);
                $position->modification()->create($this->modification->toArray());
            }
            if (isset($part['children'])) {
                $this->createPartsRecursive($part['children'], $car, $pdr->id ?? $parentId);
            }
        }
    }

    private function createCardStructures(CarPdrPositionCard $card, NomenclatureBaseItemPdrCard $originCard = null): void
    {
            $card->modification()->create($this->modification->toArray());
            $card->priceCard()->create([
                'price_currency' => 'JPY',
                'price_nz_wholesale' => $originCard?->price_nz_wholesale,
                'price_nz_retail' => $originCard?->price_nz_retail,
                'price_ru_wholesale' => $originCard?->price_ru_wholesale,
                'price_ru_retail' => $originCard?->price_ru_retail,
                'price_jp_minimum_buy' => $originCard?->price_jp_minimum_buy,
                'price_jp_maximum_buy' => $originCard?->price_jp_maximum_buy,
                'minimum_threshold_nz_retail' => $originCard?->minimum_threshold_nz_retail,
                'minimum_threshold_nz_wholesale' => $originCard?->minimum_threshold_nz_wholesale,
                'minimum_threshold_ru_retail' => $originCard?->minimum_threshold_ru_retail,
                'minimum_threshold_ru_wholesale' => $originCard?->minimum_threshold_ru_wholesale,
                'delivery_price_nz' => $originCard?->delivery_price_nz,
                'delivery_price_ru' => $originCard?->delivery_price_ru,
                'pinnacle_price' => $originCard?->pinnacle_price,
            ]);
            $card->partAttributesCard()->create([
                'color' => $originCard?->color,
                'weight' => $originCard?->weight,
                'volume' => $originCard?->volume,
                'trademe' => $originCard?->trademe ?? false,
                'drom' => $originCard?->drom ?? false,
                'avito' => $originCard?->avito ?? false,
                'dodson' => $originCard?->dodson ?? false,
            ]);
    }
}
