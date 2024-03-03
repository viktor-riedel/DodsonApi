<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\User;
use Illuminate\Http\Request;

class CreateNewCarAction
{
    use BaseItemPdrTreeTrait;

    private User $user;
    private Request $request;

    public function handle(Request $request): int
    {
        $this->user = $request->user();
        $this->request = $request;

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

        $this->copyOriginalPdr($baseCar, $car);

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

        return $car->id;
    }

    private function copyOriginalPdr(NomenclatureBaseItem $baseItem, Car $car): bool
    {
        $pdr = $this->buildPdrTreeWithoutEmpty($baseItem->baseItemPDR);
        $this->copyOriginalPdrWithCards($pdr, $car);
        return true;
    }

    private function copyOriginalPdrWithCards(array $parts, Car $car, $parentId = 0): void
    {
        foreach ($parts as $part) {
            $id = $this->recursiveCopyPdrWithCards($part, $car, $parentId);
            if (isset($part['children']) && count($part['children'])) {
                $this->copyOriginalPdrWithCards($part['children'], $car, $id);
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

        $originalPositions = NomenclatureBaseItemPdrPosition::where('nomenclature_base_item_pdr_id', $part['id'])
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
            if ($mod->body_type === $this->request->input('modification.body_type') &&
                $mod->chassis === $this->request->input('modification.chassis') &&
                $mod->generation === $this->request->input('modification.generation') &&
                $mod->engine_size === $this->request->input('modification.engine_size') &&
                $mod->drive_train === $this->request->input('modification.drive_train') &&
                $mod->header === $this->request->input('modification.header') &&
                $mod->month_from === $this->request->input('modification.month_from') &&
                $mod->month_to === $this->request->input('modification.month_to') &&
                $mod->restyle === $this->request->input('modification.restyle') &&
                $mod->doors === $this->request->input('modification.doors') &&
                $mod->transmission === $this->request->input('modification.transmission') &&
                $mod->year_from === $this->request->input('modification.year_from') &&
                $mod->year_to === $this->request->input('modification.year_to') &&
                $mod->restyle ===  $this->request->input('modification.restyle')) {
                $modificationMatch = true;
            }
        }
        return $modificationMatch;
    }
}
