<?php

namespace App\Actions\CreateCar;

use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class CreateNewCarAction
{
    public function handle(Request $request): int
    {
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

        $car->carAttributes()->create([]);
        $car->modification()->create([
            'body_type' => $request->input('modification.body_type'),
            'chassis' => $request->input('=modification.chassis'),
            'generation' => $request->input('modification.generation'),
            'drive_train' => $request->input('modification.drive_train'),
            'header' => $request->input('modification.header'),
            'month_from' => $request->input('modification.month_from'),
            'month_to' => $request->input('modification.month_to'),
            'restyle' => (bool) $request->input('modification.restyle'),
            'transmission' => $request->input('modification.transmission'),
            'year_from' => $request->input('modification.year_from'),
            'year_to' => $request->input('modification.year_to'),
            'years_string' => $request->input('modification.years_string'),
        ]);

        if (is_array($request->photos)) {
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
}
