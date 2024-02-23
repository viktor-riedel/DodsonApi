<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use Illuminate\Http\Request;

class CreateNewCarAction
{
    use BaseItemPdrTreeTrait;

    public function handle(Request $request): int
    {
        $baseCar = NomenclatureBaseItem::where('make', strtoupper(trim($request->input('make'))))
            ->where('model', strtoupper(trim($request->input('model'))))
            ->where('generation', trim($request->input('generation')))
            ->first();

//        $basePdr = $baseCar->baseItemPDR;
//        foreach($basePdr as $pdrPosition) {
//            //nomenclatureBaseItemModifications
//            $pdrPosition->load('nomenclatureBaseItemPdrPositions');
//            foreach($pdrPosition->nomenclatureBaseItemPdrPositions as $position) {
//                $position->load('nomenclatureBaseItemModifications');
//                $filtered = $position->nomenclatureBaseItemModifications->filter(function($mod) use ($request) {
//                    return  $mod->body_type === $request->input('modification.body_type') &&
//                        $mod->chassis === $request->input('modification.chassis') &&
//                        $mod->generation === $request->input('modification.generation') &&
//                        $mod->drive_train === $request->input('modification.drive_train') &&
//                        $mod->header === $request->input('modification.header') &&
//                        $mod->month_from === (int) $request->input('modification.month_from') &&
//                        $mod->month_to === (int) $request->input('modification.month_to') &&
//                        $mod->restyle === (bool) $request->input('modification.restyle') &&
//                        $mod->doors === (int) $request->input('modification.doors') &&
//                        $mod->transmission === $request->input('modification.transmission') &&
//                        $mod->year_from === (int) $request->input('modification.year_from') &&
//                        $mod->year_to === (int) $request->input('modification.year_to');
//                });
//                if ($filtered->count()) {
//                    ray($filtered);
//                }
//            }
//        }
//        //ray($basePdr);
//        return -1;

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
            'chassis' => $request->input('modification.chassis'),
            'generation' => $request->input('modification.generation'),
            'engine_size' => $request->input('modification.engine_size'),
            'drive_train' => $request->input('modification.drive_train'),
            'header' => $request->input('modification.header'),
            'month_from' => (int) $request->input('modification.month_from'),
            'month_to' => (int) $request->input('modification.month_to'),
            'restyle' => (bool) $request->input('modification.restyle'),
            'doors' => (int) $request->input('modification.doors'),
            'transmission' => $request->input('modification.transmission'),
            'year_from' => (int) $request->input('modification.year_from'),
            'year_to' => (int) $request->input('modification.year_to'),
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
