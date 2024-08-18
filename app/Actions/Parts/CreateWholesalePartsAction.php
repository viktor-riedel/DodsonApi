<?php

namespace App\Actions\Parts;

use App\Helpers\Consts;
use App\Http\Controllers\SellingPartsMap\SellingPartsMapController;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\NomenclatureBaseItem;
use App\Models\User;
use Illuminate\Http\Request;

class CreateWholesalePartsAction
{
    use InnerIdTrait, DefaultSellingMapTrait;

    private User $user;
    private array $engine = [];
    private array $front = [];
    private array $exterior = [];
    private array $interior = [];
    private array $frontSuspension = [];
    private array $rearSuspension = [];
    private array $other = [];
    private Car $car;


    public function handle(Request $request): bool
    {
        $make = $request->input('make');
        $model = $request->input('model');
        $generation = $request->input('generation');
        $modificationInnerId = $request->input('modification');
        $parts = $request->input('parts');
        $this->user = $request->user();
        //selling parts
        $data = $request->input('default_parts');
        $this->engine = $data['engine'];
        $this->front = $data['front'];
        $this->exterior = $data['exterior'];
        $this->interior = $data['interior'];
        $this->frontSuspension = $data['frontSuspension'];
        $this->rearSuspension = $data['rearSuspension'];
        $this->other = $data['other'];
        $ignoreModification = !$modificationInnerId;


        $baseCar = NomenclatureBaseItem::with('modifications')
            ->where('make', $make)
            ->where('model', $model)
            ->where('generation', $generation)
            ->first();


        $modification = $baseCar->modifications()->where('inner_id', $modificationInnerId)->first();

        if (!$modification && !$ignoreModification) {
            throw new \Exception('Nomenclature modification not found');
        }

        $monthStart = $baseCar->modifications->min("month_from");
        $monthEnd = $baseCar->modifications->max("month_to");
        $yearFrom = $baseCar->modifications->min("year_from");
        $yearTo = $baseCar->modifications->max("year_to");
        $yearsString = $monthStart . '.' . $yearFrom . '-' . $monthEnd . '.' . $yearTo;


        $this->car = Car::create([
            'car_mvr' => strtoupper($request->input('mvr.mvr')),
            'contr_agent_name' => $request->input('mvr.contr_agent'),
            'parent_inner_id' => $baseCar->inner_id,
            'make' => $make,
            'model' => $model,
            'generation' => $generation,
            'chassis' => ($modification?->chassis) . '-',
            'created_by' => $this->user->id,
            'virtual' => true,
            'ignore_modification' => $ignoreModification,
        ]);

        $this->car->carFinance()->create([
            'purchase_price' => $request->integer('mvr.purchase_price'),
        ]);

        $this->car->carAttributes()->create([
            'color' => null,
            'chassis' => ($modification?->chassis) . '-',
            'engine' => $modification?->engine_name,
        ]);

        $this->car->modification()->create([
            'gen_number' => $baseCar->gen_number,
            'body_type' => $modification?->body_type,
            'chassis' => $modification?->chassis,
            'generation' => $modification?->generation,
            'engine_size' => $modification?->engine_size,
            'drive_train' => $modification?->drive_train,
            'header' => $modification?->header,
            'month_from' => $ignoreModification ? $monthStart : $modification?->month_from,
            'month_to' => $ignoreModification ? $monthEnd : $modification?->month_to,
            'restyle' => $modification?->restyle,
            'doors' => $modification?->doors,
            'transmission' => $modification?->transmission,
            'year_from' => $ignoreModification ? $yearFrom : $modification?->year_from,
            'year_to' => $ignoreModification ? $yearTo : $modification?->year_to,
            'years_string' => $ignoreModification ? $yearsString : $modification?->years_string,
        ]);

        //polymorph relation
        if (!$ignoreModification) {
            $this->car->modifications()->create($modification->toArray());
        } else {
            $this->car->modifications()->create($this->car->modification->toArray());
        }

        if (is_array($parts) && count($parts)) {
            $this->createParts($parts);
        }

        $this->createPartsEntries();

        return true;
    }

    private function createParts(array $parts): void
    {
        foreach($parts as $part) {
            $folderName = $this->findPartParentName($part['item_name_eng']);
            $folder = CarPdr::where(
                [
                    'car_id' => $this->car->id,
                    'item_name_eng' => $folderName,
                    'is_folder' => true,
                ]
            )->first();
            if (!$folder) {
                $folder = $this->car->pdrs()->create([
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
        $originalCard = $part['card'];

        $position = $folder->positions()->create([
            'item_name_ru' => $part['item_name_ru'] ?? '',
            'item_name_eng' => $part['item_name_eng'] ?? '',
            'ic_number' => $part['ic_number'] ?? '',
            'oem_number' => null,
            'ic_description' => $part['ic_description'] ?? '',
            'is_virtual' => false,
            'created_by' => $this->user->id,
            'user_id' => Consts::getPartsSaleUserId(),
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
            'price_nz_wholesale' => $originalCard['price_nz_wholesale'] ?? 0,
            'price_nz_retail' => $originalCard['price_nz_retail'] ?? 0,
            'price_ru_wholesale' => $originalCard['price_ru_wholesale'] ?? 0,
            'price_ru_retail' => $originalCard['price_ru_retail'] ?? 0,
            'price_mng_retail' => $originalCard['price_mng_retail'] ?? 0,
            'price_mng_wholesale' => $originalCard['price_mng_wholesale'] ?? 0,
            'price_jp_minimum_buy' => $originalCard['price_jp_minimum_buy'] ?? 0,
            'price_jp_maximum_buy' => $originalCard['price_jp_maximum_buy'] ?? 0,
            'minimum_threshold_nz_retail' => $originalCard['minimum_threshold_nz_retail'] ?? 0,
            'minimum_threshold_nz_wholesale' => $originalCard['minimum_threshold_nz_wholesale'] ?? 0,
            'minimum_threshold_ru_retail' => $originalCard['minimum_threshold_ru_retail'] ?? 0,
            'minimum_threshold_ru_wholesale' => $originalCard['minimum_threshold_ru_wholesale'] ?? 0,
            'delivery_price_nz' => $originalCard['delivery_price_nz'] ?? 0,
            'delivery_price_ru' => $originalCard['delivery_price_ru'] ?? 0,
            'pinnacle_price' => $originalCard['pinnacle_price'] ?? 0,
            'minimum_threshold_jp_retail' => $originalCard['minimum_threshold_jp_retail'] ?? 0,
            'minimum_threshold_jp_wholesale' => $originalCard['minimum_threshold_jp_wholesale'] ?? 0,
            'minimum_threshold_mng_retail' => $originalCard['minimum_threshold_mng_retail'] ?? 0,
            'minimum_threshold_mng_wholesale' => $originalCard['minimum_threshold_mng_wholesale'] ?? 0,
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

    private function createPartsEntries(): void
    {
        if (count($this->engine)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[0]);
            $this->createCards($folder, $this->engine);
        }
        if (count($this->front)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[1]);
            $this->createCards($folder, $this->front);
        }
        if (count($this->exterior)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[2]);
            $this->createCards($folder, $this->exterior);
        }
        if (count($this->interior)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[3]);
            $this->createCards($folder, $this->interior);
        }
        if (count($this->frontSuspension)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[4]);
            $this->createCards($folder, $this->frontSuspension);
        }
        if (count($this->rearSuspension)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[5]);
            $this->createCards($folder, $this->rearSuspension);
        }
        if (count($this->other)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[6]);
            $this->createCards($folder, $this->other);
        }
    }

    private function createCards(CarPdr $folder, array $parts): void
    {
        foreach ($parts as $part) {
            $position = $folder->positions()->create([
                'item_name_ru' => $part['item_name_ru'] ?? '',
                'item_name_eng' => $part['item_name_eng'] ?? '',
                'ic_number' => null,
                'oem_number' => null,
                'ic_description' => null,
                'is_virtual' => false,
                'created_by' => $this->user->id,
                'user_id' => Consts::getPartsSaleUserId(),
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
                'minimum_threshold_jp_retail' => null,
                'minimum_threshold_jp_wholesale' => null,
                'minimum_threshold_mng_retail' => null,
                'minimum_threshold_mng_wholesale' => null,
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

    private function resolveFolder(string $folderName): CarPdr
    {
        $folder = CarPdr::where(
            [
                'car_id' => $this->car->id,
                'item_name_eng' => $folderName,
                'is_folder' => true,
            ]
        )->first();

        if (!$folder) {
            $folder = $this->car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => $folderName,
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
        }
        return $folder;
    }
}
