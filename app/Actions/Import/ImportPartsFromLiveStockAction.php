<?php

namespace App\Actions\Import;

use App\Events\TradeMe\UpdateTradeMeListingEvent;
use App\Helpers\Consts;
use App\Http\Traits\BadgeGeneratorTrait;
use App\Http\Traits\InnerIdTrait;
use App\Http\Traits\SystemAccountTrait;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\CarPdrPosition;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureModification;
use App\Models\SellingMapItem;
use Str;

class ImportPartsFromLiveStockAction
{
    use InnerIdTrait, SystemAccountTrait, BadgeGeneratorTrait;

    private Car $car;

    public function handle(array $parts): void
    {
        foreach ($parts as $stockPart) {
            $this->createOrAddPart($stockPart);
        }
    }

    private function createOrAddPart(array $partData): void
    {
        $make = $partData['Manuname'];
        $model = Str::replace('(NZ ONLY)', '', $partData['Modelname']);
        $year = $partData['Invyear'];
        $mvr = $partData['Stock'];
        $mileage = $partData['Miles'];

        $partName = $partData['Itemname'];
        $partIcNumber = Str::replace('(NZ ONLY)', '', $partData['Ic']);
        $partIcDescription =  Str::replace('(NZ ONLY)', '', $partData['Description']);
        $partBarcode = $partData['Tag'];
        $partOemNumber = $partData['Oemnumber'];
        $partInnerId = $partData['inner_id'];
        $partPrice = $partData['Price'];

        $this->car = $this->createOrFindCar($make, $model, $year, $mvr, $mileage, $partIcNumber);
        $this->createPart($partName, $partIcDescription, $partBarcode,
            $partPrice, $partIcNumber, $partOemNumber, $partInnerId);
    }

    private function findBaseCar(string $make, string $model, string $year, string $partIcNumber = ''): NomenclatureBaseItem|null
    {
        $baseItem = null;
        //try to find by ic number
        if ($partIcNumber) {
            $nomenclatureItems =
                NomenclatureBaseItemPdrPosition::with('modifications')
                    ->where('ic_number', $partIcNumber)
                    ->get();
            $ids = [];
            foreach ($nomenclatureItems as $item) {
                if ($item->modifications()->count()) {
                    foreach ($item->modifications as $modification) {
                        $ids[] = $modification->inner_id;
                    }
                }
            }
            $baseIds = NomenclatureModification::where('modificationable_type', 'App\Models\NomenclatureBaseItem')
                ->whereIn('inner_id', $ids)
                ->get()
                ->pluck('modificationable_id')
                ->toArray();
            $baseItem = NomenclatureBaseItem::whereIn('id', $baseIds)
                ->where('make', $make)
                ->first();
        }

        //try to find by make and model
        if (!$baseItem) {
            $possibleCars = NomenclatureBaseItem::with('modifications')
                ->where('make', $make)
                ->where('model', $model)
                ->get();
            if ($possibleCars->count()) {
                foreach ($possibleCars as $baseCar) {
                    if ($baseCar->modifications->count()) {
                        foreach ($baseCar->modifications as $modification) {
                            if ($year >= $modification->year_from && $year <= $modification->year_to) {
                                //found base car
                                return $baseCar;
                            }
                        }
                    }
                }
            }
        }

        return $baseItem;
    }

    private function createOrFindCar(string $make, string $model, string $year, string $mvr, string $mileage, string $partIcNumber): Car
    {
        $car = Car::where('car_mvr', $mvr)->first();
        if (!$car) {
            $baseCar = $this->findBaseCar($make, $model, $year, $partIcNumber);
            $modification = null;
            $ignoreModification = $baseCar === null;

            if ($baseCar) {
                $monthStart = $baseCar->modifications->min("month_from");
                $monthEnd = $baseCar->modifications->max("month_to");
                $yearFrom = $baseCar->modifications->min("year_from");
                $yearTo = $baseCar->modifications->max("year_to");
                $yearsString = $monthStart.'.'.$yearFrom.'-'.$monthEnd.'.'.$yearTo;
                $modification = $baseCar->modifications()->first();
            } else {
                $monthStart = 1;
                $monthEnd = 12;
                $yearFrom = $year;
                $yearTo = $year;
                $yearsString = $monthStart.'.'.$yearFrom.'-'.$monthEnd.'.'.$yearTo;
            }

            //create car
            $car = Car::create([
                'car_mvr' => $mvr,
                'contr_agent_name' => 'RETAIL PARTS',
                'parent_inner_id' => $baseCar ?
                    $baseCar->inner_id :
                    $this->generateInnerId($make.$model.$mvr),
                'make' => $make,
                'model' => $model,
                'generation' => $baseCar?->generation ?? 'unknown',
                'chassis' => ($modification?->chassis).'-',
                'created_by' => $this->getSystemAccount()->id,
                'virtual' => true,
                'virtual_retail' => true,
                'ignore_modification' => $ignoreModification,
            ]);

            $car->carFinance()->create([
                'purchase_price' => 0,
            ]);

            $car->carAttributes()->create([
                'year' => $year,
                'color' => null,
                'mileage' => (int) $mileage,
                'chassis' => ($modification?->chassis).'-',
                'engine' => $modification?->engine_name,
            ]);

            $car->modification()->create([
                'gen_number' => $baseCar?->gen_number ?? -1,
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
                $car->modifications()->create($modification->toArray());
            } else {
                $car->modifications()->create($car->modification->toArray());
            }
        }

        return $car;
    }

    private function findPartParentName(string $itemNameEng): string
    {
        $groupItem = SellingMapItem::where('item_name_eng', $itemNameEng)->first();
        if ($groupItem) {
            $groupName = SellingMapItem::where('id', $groupItem->parent_id)
                ->first()?->item_name_eng;
            if ($groupName) {
                return $groupName;
            }
        }
        return 'Other Parts';
    }


    private function createPart(
        string $partName,
        string $partIsDescription = '',
        string $partBarcode = '',
        int $partPrice = 0,
        string $partIcNumber = '',
        string $partOemNumber = '',
        string $partInnerId = ''
    ): void {
        //check if exists
        $part = $this->findPart($partIcNumber, $partName, $partIsDescription);
        if (!$part) {
            //create if not exists
            $folderName = $this->findPartParentName($partName);
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
                    'created_by' => $this->getSystemAccount()->id,
                ]);
            }
            $this->createPartCards($folder, $partName, $partIsDescription, $partBarcode,
                $partPrice, $partIcNumber, $partOemNumber, $partInnerId);
        } else {
            //update part
            $part->card()->update([
                'description' => $partIsDescription,
                'ic_number' => $partIcNumber,
            ]);
            if ($part->card->priceCard !== $partPrice) {
                $part->card->priceCard()->update([
                    'selling_price' => $partPrice,
                ]);
                if ($part->tradeMeListing) {
                    // update trademe if price changed
                    event (new UpdateTradeMeListingEvent($part->tradeMeListing));
                }
            }
        }
    }

    private function createPartCards(
        CarPdr $folder,
        string $partName,
        string $partIsDescription = '',
        string $partBarcode = '',
        int $partPrice = 0,
        string $partIcNumber = '',
        string $partOemNumber = '',
        string $partInnerId = ''): void
    {
        $originalCard = NomenclatureBaseItemPdrPosition::where('ic_number', $partIcNumber)->first();
        $position = $folder->positions()->create([
            'item_name_ru' => '',
            'item_name_eng' => $partName,
            'ic_number' => $partIcNumber,
            'oem_number' => $originalCard && $originalCard->oem_number ? $originalCard->oem_number : $partOemNumber,
            'ic_description' => $partIsDescription,
            'is_virtual' => false,
            'created_by' => $this->getSystemAccount()->id,
            'user_id' => Consts::getPartsSaleUserId(),
        ]);
        $barcode = (int) $partBarcode;
        if ($barcode === 0) {
            $barcode = $this->generateNextBarcode();
        }
        $card = $position->card()->create([
            'parent_inner_id' => $this->generateInnerId(\Str::random(10) . now()),
            'name_eng' => $originalCard?->item_name_eng ?? $partName,
            'name_ru' => '',
            'comment' => null,
            'description' => $partIsDescription,
            'ic_number' => $partIcNumber,
            'oem_number' => $originalCard && $originalCard->oem_number ? $originalCard->oem_number : $partOemNumber,
            'created_by' => $this->getSystemAccount()->id,
            'barcode' => $barcode,
        ]);
        $card->priceCard()->create([
            'price_currency' => 'NZD',
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
            'pinnacle_price' => $originalCard?->pinnacle_price ?? $partPrice,
            'minimum_threshold_jp_retail' => $originalCard?->minimum_threshold_jp_retail ?? 0,
            'minimum_threshold_jp_wholesale' => $originalCard?->minimum_threshold_jp_wholesale ?? 0,
            'minimum_threshold_mng_retail' => $originalCard?->minimum_threshold_mng_retail ?? 0,
            'minimum_threshold_mng_wholesale' => $originalCard?->minimum_threshold_mng_wholesale ?? 0,
            'selling_price' => $partPrice,
            'standard_price' => $originalCard?->price_nz_retail ?? $partPrice,
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

    private function findPart(string $icNumber, string $partName, string $icDescription): CarPdrPosition | null
    {
        $parts = $this->car->positions;
        if ($parts && $parts->count()) {
            foreach ($parts as $part) {
                if ($part->ic_number === $icNumber && $part->item_name_eng === $partName && $part->ic_description === $icDescription) {
                    return $part;
                }
            }
        }
        return null;
    }
}
