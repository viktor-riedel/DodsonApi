<?php

namespace App\Actions\CreateCar;

use App\Http\Controllers\SellingPartsMap\SellingPartsMapController;
use App\Http\Traits\BadgeGeneratorTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\CarPdr;

class AddPartsFromSellingListAction
{
    use InnerIdTrait, BadgeGeneratorTrait;

    private array $engine = [];
    private array $front = [];
    private array $exterior = [];
    private array $interior = [];
    private array $frontSuspension = [];
    private array $rearSuspension = [];
    private array $other = [];
    private int $userId;
    private Car $car;

    public function handle(Car $car, array $data, int $userId): void
    {
        $this->userId = $userId;
        $this->engine = $data['engine'];
        $this->front = $data['front'];
        $this->exterior = $data['exterior'];
        $this->interior = $data['interior'];
        $this->frontSuspension = $data['frontSuspension'];
        $this->rearSuspension = $data['rearSuspension'];
        $this->other = $data['other'];
        $this->car = $car;

        $this->createPartsEntries();
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
                'created_by' => $this->userId,
                'user_id' => null,
            ]);
            $card = $position->card()->create([
                'parent_inner_id' => $this->generateInnerId(\Str::random(10) . now()),
                'name_eng' => $part['item_name_eng'] ?? '',
                'name_ru' => $part['item_name_ru'] ?? '',
                'comment' => null,
                'description' => null,
                'ic_number' => '',
                'oem_number' => null,
                'created_by' => $this->userId,
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
                'created_by' => $this->userId,
            ]);
        }
        return $folder;
    }
}
