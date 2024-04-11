<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\User;
use Illuminate\Http\Request;

class CreateNewCarAction
{
    use BaseItemPdrTreeTrait;
    use InnerIdTrait;

    private User $user;
    private Request $request;

    public function handle(Request $request): int
    {
        $this->user = $request->user();
        $this->request = $request;
        $includeParts = $request->input('parts');

        $baseCar = NomenclatureBaseItem::where('make', strtoupper(trim($request->input('make'))))
            ->where('model', strtoupper(trim($request->input('model'))))
            ->where('generation', trim($request->input('generation')))
            ->first();

        $modification = $baseCar->modifications()->where('inner_id', $this->request->input('modification'))->first();
        //raise exception if modification not found
        if (!$modification) {
            throw new \Exception('Nomenclature modification not found');
        }

        $car = Car::create([
            'parent_inner_id' => $baseCar->inner_id,
            'make' => strtoupper(trim($request->input('make'))),
            'model' => strtoupper(trim($request->input('model'))),
            'generation' => trim($request->input('generation')),
            'chassis' => ($modification->chassis) . '-',
            'created_by' => $request->user()->id,
        ]);

        if (count($includeParts) && !$request->input('use_default_parts')) {
            $this->copyOriginalPdr($baseCar, $car, $includeParts);
        } else {
            $this->copyOriginalPdr($baseCar, $car);
        }

        $car->carAttributes()->create([
            'chassis' => ($modification->chassis) . '-',
            'engine' => $modification->engine_name,
        ]);
        $car->modification()->create([
            'body_type' => $modification->body_type,
            'chassis' => $modification->chassis,
            'generation' => $modification->generation,
            'engine_size' => $modification->engine_size,
            'drive_train' => $modification->drive_train,
            'header' => $modification->header,
            'month_from' => $modification->month_from,
            'month_to' => $modification->month_to,
            'restyle' => $modification->restyle,
            'doors' => $modification->doors,
            'transmission' => $modification->transmission,
            'year_from' => $modification->year_from,
            'year_to' => $modification->year_to,
            'years_string' => $modification->years_string,
        ]);

        //polymorph relation
        $car->modifications()->create($modification->toArray());

        if (is_array($request->photos) && count($request->photos)) {
            foreach($request->photos as $photo) {
                $car->images()->create([
                    'url' => $photo['uploaded_url'] ?? '',
                    'mime' => $photo['mime'] ?? '',
                    'original_file_name' => $photo['original_file_name'] ?? '',
                    'folder_name' => $photo['folder_name'] ?? '',
                    'extension' => $photo['ext'] ?? '',
                    'file_size' => $photo['size'] ?? '',
                    'special_flag' => null,
                    'created_by' => $request->user()->id
                ]);
            }
        }

        if (is_array($request->misc) && count($request->misc)) {
            $miscPdr = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => 'MISC',
                'item_name_ru' => 'ДРУГИЕ ЗАПЧАСТИ',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);

            // add misc parts under MISC folder
            foreach($request->misc as $misc_part) {
                $position = $miscPdr->positions()->create([
                    'item_name_ru' => $misc_part['part_name_eng'],
                    'item_name_eng' => $misc_part['part_name_ru'] ?? null,
                    'ic_number' => $misc_part['ic_number'] ?? '',
                    'oem_number' => null,
                    'ic_description' => $misc_part['description'],
                    'is_virtual' => false,
                    'created_by' => $this->user->id,
                ]);
                $card = $position->card()->create([
                    'parent_inner_id' => $this->generateInnerId(\Str::random(10) . now()),
                    'name_eng' => $misc_part['part_name_eng'],
                    'name_ru' => $misc_part['part_name_ru'] ?? null,
                    'comment' => $misc_part['comment'],
                    'description' => $misc_part['description'],
                    'ic_number' => $misc_part['ic_number'] ?? '',
                    'oem_number' => null,
                    'created_by' => $this->user->id,
                ]);
                $card->priceCard()->create([
                    'price_nz_wholesale' => null,
                    'price_nz_retail' => null,
                    'price_ru_wholesale' => null,
                    'price_ru_retail' => null,
                    'price_jp_minimum_buy' => null,
                    'price_jp_maximum_buy' => null,
                    'minimum_threshold_nz_retail' => null,
                    'minimum_threshold_nz_wholesale' => null,
                    'minimum_threshold_ru_retail' => null,
                    'minimum_threshold_ru_wholesale' => null,
                    'delivery_price_nz' => null,
                    'delivery_price_ru' => null,
                    'pinnacle_price' => null,
                ]);
                $card->partAttributesCard()->create([
                    'color' => null,
                    'weight' => null,
                    'volume' => null,
                    'amount' => (int) $misc_part['amount'],
                    'ordered_for_user_id' => $misc_part['ordered_for'] ?? null,
                ]);
            }
        }

        return $car->id;
    }

    private function copyOriginalPdr(NomenclatureBaseItem $baseItem, Car $car, array $includeCards = []): bool
    {
        $onlyIncludeIds = collect($includeCards)->pluck('id')->toArray();
        $pdr = $this->buildPdrTreeWithoutEmpty($baseItem->baseItemPDR, $onlyIncludeIds);
        $this->copyOriginalPdrWithCards($pdr, $car, 0, $includeCards);
        return true;
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
            'created_by' => $this->user->id,
        ]);

        $originalPositions = NomenclatureBaseItemPdrPosition::whereIn
            ('id', collect($part['nomenclature_base_item_pdr_positions'])->pluck('id')->toArray())
            ->get();

        foreach($originalPositions as $origin) {
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
                    'created_by' => $this->user->id,
                ]);
                $modification = $origin->modifications()->where('inner_id', $this->request->input('modification'))->first();
                if ($modification) {
                    $position->modification()->create($modification->toArray());
                }
            }

            if ($part['is_folder'] && $origin->is_virtual && $position) {
                $pdr->update(['car_pdr_position_id' => $position->id]);
            }

            if ($modificationMatch && $position) {
                $originCard = $origin->nomenclatureBaseItemPdrCard;
                $card = $position->card()->create([
                    'parent_inner_id' => $originCard->inner_id,
                    'name_eng' => $originCard->name_eng,
                    'name_ru' => $originCard->name_ru,
                    'comment' => $originCard->comment,
                    'description' => $originCard->description,
                    'ic_number' => $originCard->ic_number,
                    'oem_number' => $originCard->oem_number,
                    'created_by' => $this->user->id,
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
        $selectedModification = $this->request->input('modification');
        return $position->modifications()->where('inner_id', $selectedModification)->exists();
    }
}
