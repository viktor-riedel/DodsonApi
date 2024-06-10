<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\CarPdrPositionCard;

class AddMiscPartsAction
{
    use InnerIdTrait;

    public function handle(Car $car, int $userId, array $parts = []): void
    {
        if (count($parts)) {
            $folder = $car->pdrs()->where("item_name_eng", "like", "%misc%")->first();
            if (!$folder) {
                $folder = $car->pdrs()->create([
                    'parent_id' => 0,
                    'item_name_eng' => 'MISC',
                    'item_name_ru' => 'ДРУГИЕ ЗАПЧАСТИ',
                    'is_folder' => true,
                    'is_deleted' => false,
                    'parts_list_id' => null,
                    'created_by' => $userId,
                ]);
            }

            foreach ($parts as $part) {
                $name_eng = '';
                $name_ru = '';
                if (isset($part['part_name_eng'])) {
                    $name_eng = $part['part_name_eng'];
                }
                if (isset($part['part_name_ru'])) {
                    $name_ru = $part['part_name_ru'];
                }
                if (!$name_eng && isset($part['item_name_eng'])) {
                    $name_eng = $part['item_name_eng'];
                }
                if (!$name_ru && isset($part['item_name_ru'])) {
                    $name_ru = $part['item_name_ru'];
                }
                $position = $folder->positions()->create([
                    'item_name_ru' => $name_eng,
                    'item_name_eng' => $name_ru,
                    'ic_number' => $part['ic_number'] ?? '',
                    'oem_number' => null,
                    'ic_description' => $part['description'] ?? null,
                    'is_virtual' => false,
                    'created_by' => $userId,
                ]);
                $card = $position->card()->create([
                    'parent_inner_id' => $this->generateInnerId(\Str::random(10) . now()),
                    'name_eng' => $name_eng,
                    'name_ru' => $name_ru,
                    'comment' => $part['comment'] ?? null,
                    'description' => $part['description'] ?? null,
                    'ic_number' => $part['ic_number'] ?? '',
                    'oem_number' => null,
                    'created_by' => $userId,
                    'barcode' => $this->generateBarCode(),
                ]);
                if (isset($part['comment'])) {
                    $card->comments()->create([
                        'comment' => $part['comment'],
                        'user_id' => $userId,
                    ]);
                }
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
                ]);
                $card->partAttributesCard()->create([
                    'color' => null,
                    'weight' => null,
                    'volume' => null,
                    'amount' => isset($part['amount']) ? (int) $part['amount'] : 1,
                    'ordered_for_user_id' => $misc_part['ordered_for'] ?? null,
                ]);
            }
        }
    }

    private function generateBarCode(): int
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
