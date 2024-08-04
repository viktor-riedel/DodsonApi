<?php

namespace App\Actions\Parts;

use App\Http\Traits\DefaultSellingMapTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\User;
use Illuminate\Http\Request;

class CreateWholesalePartsAction
{
    use InnerIdTrait, DefaultSellingMapTrait;

    private User $user;
    private const DODSON_USER = 135;

    public function handle(Request $request): bool
    {
        $make = $request->input('make');
        $model = $request->input('model');
        $generation = $request->input('generation');
        $modificationInnerId = $request->input('modification');
        $parts = $request->input('parts');
        $this->user = $request->user();

        $baseCar = NomenclatureBaseItem::where('make', $make)
            ->where('model', $model)
            ->where('generation', $generation)
            ->first();

        $modification = $baseCar->modifications()->where('inner_id', $modificationInnerId)->first();

        if (!$modification) {
            throw new \Exception('Nomenclature modification not found');
        }

        $car = Car::create([
            'parent_inner_id' => $baseCar->inner_id,
            'make' => $make,
            'model' => $model,
            'generation' => $generation,
            'chassis' => ($modification->chassis) . '-',
            'created_by' => $this->user->id,
            'contr_agent_name' => '',
            'virtual' => true,
        ]);

        $car->carFinance()->create([
            'purchase_price' => 0,
        ]);

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

        if (is_array($parts) && count($parts)) {
            $this->createParts($parts, $car);
        }

        return true;
    }

    private function createParts(array $parts, Car $car): void
    {
        foreach($parts as $part) {
            $folderName = $this->findPartParentName($part['item_name_eng']);
            $folder = CarPdr::where(
                [
                    'car_id' => $car->id,
                    'item_name_eng' => $folderName,
                    'is_folder' => true,
                ]
            )->first();
            if (!$folder) {
                $folder = $car->pdrs()->create([
                    'parent_id' => 0,
                    'item_name_eng' => $folderName,
                    'item_name_ru' => '',
                    'is_folder' => true,
                    'is_deleted' => false,
                    'parts_list_id' => null,
                    'created_by' => $this->user->id,
                ]);
            }

            $this->createPartCards($part, $folder);

        }
    }

    private function createPartCards(array $part, CarPdr $folder): void
    {
        $originalCard = NomenclatureBaseItemPdrCard::where('ic_number', $part['ic_number'])
            ->where('description', $part['ic_description'])
            ->where('name_eng', $part['item_name_eng'])
            ->first();

        $position = $folder->positions()->create([
            'item_name_ru' => $part['item_name_ru'] ?? '',
            'item_name_eng' => $part['item_name_eng'] ?? '',
            'ic_number' => $part['ic_number'] ?? '',
            'oem_number' => null,
            'ic_description' => $part['ic_description'] ?? '',
            'is_virtual' => false,
            'created_by' => $this->user->id,
            'user_id' => self::DODSON_USER,
        ]);
        $card = $position->card()->create([
            'parent_inner_id' => $this->generateInnerId(\Str::random(10) . now()),
            'name_eng' => $part['item_name_eng'] ?? '',
            'name_ru' => $part['item_name_ru'] ?? '',
            'comment' => null,
            'description' => null,
            'ic_number' => '',
            'oem_number' => null,
            'created_by' => $this->user->id,
            'barcode' => $this->generateNextBarcode(),
        ]);
        $card->priceCard()->create([
            'price_currency' => 'JPY',
            'price_nz_wholesale' => $originalCard?->price_nz_wholesale ?? 0,
            'price_nz_retail' => $originalCard?->price_nz_retail ?? 0,
            'price_ru_wholesale' => $originalCard?->price_ru_wholesale ?? 0,
            'price_ru_retail' => $originalCard?->price_ru_retail ?? 0,
            'price_mng_retail' => $originalCard?->price_mng_retail ?? 0,
            'price_mng_wholesale' => $originalCard?->price_mng_wholesale ?? 0,
            'price_jp_minimum_buy' => $originalCard?->price_jp_minimum_buy ?? 0,
            'price_jp_maximum_buy' => $originalCard?->price_jp_maximum_buy ?? 0,
            'minimum_threshold_nz_retail' => $originalCard?->minimum_threshold_nz_retail ?? 0,
            'minimum_threshold_nz_wholesale' => $originalCard?->minimum_threshold_nz_wholesale ?? 0,
            'minimum_threshold_ru_retail' => $originalCard?->minimum_threshold_ru_retail ?? 0,
            'minimum_threshold_ru_wholesale' => $originalCard?->minimum_threshold_ru_wholesale ?? 0,
            'delivery_price_nz' => $originalCard?->delivery_price_nz ?? 0,
            'delivery_price_ru' => $originalCard?->delivery_price_ru ?? 0,
            'pinnacle_price' => $originalCard?->pinnacle_price ?? 0,
            'minimum_threshold_jp_retail' => $originalCard?->minimum_threshold_jp_retail ?? 0,
            'minimum_threshold_jp_wholesale' => $originalCard?->minimum_threshold_jp_wholesale ?? 0,
            'minimum_threshold_mng_retail' => $originalCard?->minimum_threshold_mng_retail ?? 0,
            'minimum_threshold_mng_wholesale' => $originalCard?->minimum_threshold_mng_wholesale ?? 0,
            'selling_price' => null,
            'buying_price' => null,
        ]);
        $card->partAttributesCard()->create([
            'color' => null,
            'weight' => null,
            'volume' => null,
            'amount' => isset($part['amount']) ? (int) $part['amount'] : 1,
            'ordered_for_user_id' => null,
        ]);
    }
}
