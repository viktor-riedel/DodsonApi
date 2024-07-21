<?php

namespace App\Actions\CsvParsers;

use App\Http\Traits\BadgeGeneratorTrait;
use App\Http\Traits\InnerIdTrait;
use App\Models\Part;

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


        $part = Part::where('ic_number', $ic_description[0])
            ->where('make', $make)
            ->where('model', $model)
            ->where('item_name_eng', $item_name_eng)
            ->when(isset($ic_description[1]), function ($query) use ($ic_description) {
                return $query->where('ic_description', $ic_description[1]);
            })
            ->first();
        if (!$part) {
            $part = Part::create([
                'inner_id' => '',
                'stock_number' => $stock,
                'ic_number' => isset($ic_description[0]) ?: null,
                'ic_description' => isset($ic_description[1]) ?: null,
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
                'price_jpy' => $price,
                'price_nzd' => 0,
                'price_mng' => 0,
                'comment' => $comment
            ]);
            $part->update(['inner_id' => $this->generateInnerId(
                $part->make . $part->model . $part->ic_number . $part->year . $part->created_at)
            ]);
        } else {
            $part->update([
                'stock_number' => $stock,
                'ic_number' => isset($ic_description[0]) ?: null,
                'ic_description' => isset($ic_description[1]) ?: null,
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
                'price_jpy' => $price,
                'price_nzd' => 0,
                'price_mng' => 0,
                'comment' => $comment
            ]);
        }
    }
}
