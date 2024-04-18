<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Support\Collection;

class AddPartsFromModificationListAction
{
    use BaseItemPdrTreeTrait;
    use InnerIdTrait;

    public int $userId = 0;
    public string $innerId = '';
    public Collection $choseParts;

    public function handle(Car $car, array $parts, int $user): void
    {
        $this->userId = $user;
        $this->innerId = $car->modifications->inner_id;
        $this->choseParts = collect($parts);
        $partIds = $this->choseParts->pluck('id')->toArray();
        $baseCar = NomenclatureBaseItem::where('make', $car->make)
            ->where('model', $car->model)
            ->where('generation', $car->generation)
            ->first();
        if ($baseCar) {
            $pdr = $this->buildPdrTreeWithoutEmpty($baseCar->baseItemPDR, $partIds);
            $this->copyOriginalPdrWithCards($pdr, $car, 0, $partIds);
        }
    }

    private function copyOriginalPdrWithCards(array $pdrs, Car $car, $parentId = 0, array $includePositions = []): void
    {
        foreach ($pdrs as $pdr) {
            $id = $this->recursiveCopyPdrWithCards($pdr, $car, $parentId);
            if (isset($pdr['children']) && count($pdr['children'])) {
                $this->copyOriginalPdrWithCards($pdr['children'], $car, $id);
            }
        }
    }

    private function recursiveCopyPdrWithCards($part, Car $car, $parentId = 0): int
    {
        $pdr = $car->pdrs()->create([
            'parent_id' => $parentId,
            'item_name_eng' => $part['item_name_eng'],
            'item_name_ru' => $part['item_name_ru'],
            'is_folder' => $part['is_folder'],
            'is_deleted' => false,
            'parts_list_id' => $part['id'],
            'created_by' => $this->userId,
        ]);

        $originalPositions = NomenclatureBaseItemPdrPosition::whereIn
        ('id', collect($part['nomenclature_base_item_pdr_positions'])->pluck('id')->toArray())
            ->get();

        foreach($originalPositions as $origin) {
            //check exists
            $exists = $pdr->positions()
                ->where('ic_number', $origin->ic_number)
                ->where('ic_description', $origin->ic_description)
                ->exists();
            if ($exists) {
                continue;
            }
            //check match modification
            $modificationMatch = $this->modificationMatch($origin);
            $position = null;
            if ($origin->is_virtual || $modificationMatch) {
                $position = $pdr->positions()->create([
                    'item_name_ru' => $origin->item_name_ru,
                    'item_name_eng' => $origin->item_name_eng,
                    'ic_number' => $origin->ic_number,
                    'oem_number' => $origin->oem_number,
                    'ic_description' => $origin->ic_description,
                    'is_virtual' => $origin->is_virtual,
                    'created_by' => $this->userId,
                ]);
                $modification = $origin->modifications()->where('inner_id', $this->innerId)->first();
                if ($modification) {
                    $position->modification()->create($modification->toArray());
                }
            }

            if ($part['is_folder'] && $origin->is_virtual && $position) {
                $pdr->update(['car_pdr_position_id' => $position->id]);
            }

            if ($modificationMatch && $position) {
                $originCard = $origin->nomenclatureBaseItemPdrCard;
                //find a comment if provided
                $comment = $this->choseParts->where('id', $origin->id)->first();
                $card = $position->card()->create([
                    'parent_inner_id' => $originCard->inner_id,
                    'name_eng' => $originCard->name_eng,
                    'name_ru' => $originCard->name_ru,
                    'comment' => $comment ? $comment['comment'] : $originCard->comment,
                    'description' => $originCard->description,
                    'ic_number' => $originCard->ic_number,
                    'oem_number' => $originCard->oem_number,
                    'created_by' => $this->userId,
                ]);
                if ($modification) {
                    $card->modification()->create($modification->toArray());
                }
                $card->priceCard()->create([
                    'price_nz_wholesale' => $originCard->price_nz_wholesale,
                    'price_nz_retail' => $originCard->price_nz_retail,
                    'price_ru_wholesale' => $originCard->price_ru_wholesale,
                    'price_ru_retail' => $originCard->price_ru_retail,
                    'price_jp_minimum_buy' => $originCard->price_jp_minimum_buy,
                    'price_jp_maximum_buy' => $originCard->price_jp_maximum_buy,
                    'minimum_threshold_nz_retail' => $originCard->minimum_threshold_nz_retail,
                    'minimum_threshold_nz_wholesale' => $originCard->minimum_threshold_nz_wholesale,
                    'minimum_threshold_ru_retail' => $originCard->minimum_threshold_ru_retail,
                    'minimum_threshold_ru_wholesale' => $originCard->minimum_threshold_ru_wholesale,
                    'delivery_price_nz' => $originCard->delivery_price_nz,
                    'delivery_price_ru' => $originCard->delivery_price_ru,
                    'pinnacle_price' => $originCard->pinnacle_price,
                ]);
                $card->partAttributesCard()->create([
                    'color' => $originCard->color,
                    'weight' => $originCard->weight,
                    'volume' => $originCard->volume,
                    'trademe' => $originCard->trademe,
                    'drom' => $originCard->drom,
                    'avito' => $originCard->avito,
                    'dodson' => $originCard->dodson,
                ]);
            }
        }

        return $pdr->id;
    }

    private function modificationMatch(NomenclatureBaseItemPdrPosition $position): bool
    {
        return $position->modifications()->where('inner_id', $this->innerId)->exists();
    }
}
