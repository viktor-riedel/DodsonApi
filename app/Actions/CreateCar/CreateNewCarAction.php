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

        $car = Car::create([
            'parent_inner_id' => $baseCar->inner_id,
            'make' => strtoupper(trim($request->input('make'))),
            'model' => strtoupper(trim($request->input('model'))),
            'generation' => trim($request->input('generation')),
            'created_by' => $request->user()->id,
        ]);

        if (count($includeParts)) {
            $this->copyOriginalPdr($baseCar, $car, $includeParts);
        }

        $car->carAttributes()->create([]);
        $car->modification()->create([
            'body_type' => $this->request->input('modification.body_type'),
            'chassis' => $this->request->input('modification.chassis'),
            'generation' => $this->request->input('modification.generation'),
            'engine_size' => $this->request->input('modification.engine_size'),
            'drive_train' => $this->request->input('modification.drive_train'),
            'header' => $this->request->input('modification.header'),
            'month_from' => (int) $this->request->input('modification.month_from'),
            'month_to' => (int) $this->request->input('modification.month_to'),
            'restyle' => (bool) $this->request->input('modification.restyle'),
            'doors' => (int) $this->request->input('modification.doors'),
            'transmission' => $this->request->input('modification.transmission'),
            'year_from' => (int) $this->request->input('modification.year_from'),
            'year_to' => (int) $this->request->input('modification.year_to'),
            'years_string' => $this->request->input('modification.years_string'),
        ]);

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
                'item_name_eng' => 'MISC PARTS',
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
        if (count($includePositions)) {
            foreach ($pdrs as $pdr) {
                $id = $this->recursiveCopyPdrWithCards($pdr, $car, $parentId);
                if (isset($pdr['children']) && count($pdr['children'])) {
                    $this->copyOriginalPdrWithCards($pdr['children'], $car, $id);
                }
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
        $modificationMatch = false;
        foreach($position->nomenclatureBaseItemModifications as $mod)
        {
        if (
                $mod->restyle === $this->request->input('modification.restyle') &&
                strcasecmp($mod->body_type, $this->request->input('modification.body_type')) === 0 &&
                strcasecmp($mod->chassis, $this->request->input('modification.chassis')) === 0 &&
                strcasecmp($mod->generation, $this->request->input('modification.generation')) === 0 &&
                strcasecmp($mod->engine_size, $this->request->input('modification.engine_size')) === 0 &&
                strcasecmp($mod->drive_train, $this->request->input('modification.drive_train')) === 0 &&
                strcasecmp($mod->header, $this->request->input('modification.header')) === 0 &&
                strcasecmp($mod->month_from, $this->request->input('modification.month_from')) === 0 &&
                strcasecmp($mod->month_to, $this->request->input('modification.month_to')) === 0 &&
                strcasecmp($mod->doors, $this->request->input('modification.doors')) === 0 &&
                strcasecmp($mod->transmission, $this->request->input('modification.transmission')) === 0&&
                strcasecmp($mod->year_from, $this->request->input('modification.year_from')) === 0&&
                strcasecmp($mod->year_to, $this->request->input('modification.year_to')) === 0) {
                $modificationMatch = true;
            }
        }
        return $modificationMatch;
    }
}
