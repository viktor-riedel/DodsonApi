<?php

namespace App\Actions\CsvParsers;

use App\Http\Traits\BadgeGeneratorTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureModification;
use App\Models\Part;
use App\Models\SellingMapItem;

class ParsePinnacleCsvLineAction
{
    use BadgeGeneratorTrait, InnerIdTrait;

    public function handle(array $csv_string): void
    {
        $ic_description = explode(':', $csv_string[6]);

        $make = $csv_string[23];
        $model = $csv_string[1];
        $year = $csv_string[2];
        $item_name_eng = $csv_string[4];
        $original_barcode = $csv_string[12];
        $price = (int) $csv_string[9];
        $comment = $csv_string[5];
        $stock = $csv_string[0];

        $ic_number = $ic_description[0] ?? null;
        $ic_description = $ic_description[1] ?? null;

        $part = Part::where('ic_number')
            ->where('make', $make)
            ->where('model', $model)
            ->where('item_name_eng', $item_name_eng)
            ->when(isset($ic_description), function ($query) use ($ic_description) {
                return $query->where('ic_description', $ic_description);
            })
            ->first();
        if (!$part) {
            $part = Part::create([
                'inner_id' => '',
                'stock_number' => $stock,
                'ic_number' => $ic_number,
                'ic_description' => $ic_description,
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'color' => null,
                'mileage' => '',
                'amount' => '',
                'item_name_eng' => $item_name_eng,
                'item_name_ru' => '',
                'item_name_jp' => '',
                'item_name_mng' => '',
                'original_barcode' => $original_barcode,
                'generated_barcode' => '',
                'standard_price_nzd' => 0,
                'actual_price_nzd' => $price,
                'price_nzd' => 0,
                'comment' => $comment
            ]);
            $part->update(['inner_id' => $this->generateInnerId(
                $part->make . $part->model . $part->ic_number . $part->year . $part->created_at)
            ]);
            //find part group
            $groupItem = SellingMapItem::where('item_name_eng', $item_name_eng)->first();
            if ($groupItem) {
                $groupName = SellingMapItem::where('id', $groupItem->parent_id)
                    ->first()?->item_name_eng;
                if ($groupName) {
                    $part->update(['part_group' => $groupName]);
                }
            }
            //find generation
            $nomenclatureItems =
                NomenclatureBaseItemPdrPosition::with('modifications')
                ->where('ic_number', $ic_number)
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
            if ($baseItem) {
                $part->update(['generation' => $baseItem->generation]);
            }
        } else {
            $part->update([
                'stock_number' => $stock,
                'ic_number' => $ic_number,
                'ic_description' => $ic_description,
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'mileage' => '',
                'amount' => '',
                'item_name_eng' => $item_name_eng,
                'item_name_ru' => '',
                'item_name_jp' => '',
                'item_name_mng' => '',
                'original_barcode' => $original_barcode,
                'generated_barcode' => '',
                'standard_price_nzd' => 0,
                'actual_price_nzd' => $price,
                'price_nzd' => 0,
                'comment' => $comment
            ]);
            // if price changes fire trademe relist update
        }
    }
}
