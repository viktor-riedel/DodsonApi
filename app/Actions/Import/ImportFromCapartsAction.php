<?php

namespace App\Actions\Import;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class ImportFromCapartsAction
{
    use InnerIdTrait;

    public function handle(Request $request, int $userId): int
    {
        $helper = new CatalogApiHelper();
        $mvr = $helper->loadPDRWithModificationsByMvrId($request->input('mvr')['catalog_mvr_id']);
        $id = $request->input('id');

        $exist = Car::whereHas("importItem", function ($q) use ($id) {
            $q->where("imported_id", $id);
        })->first();

        if ($exist) {
            return 0;
        }

        if ($request->input('mvr') && is_array($request->input('mvr'))) {
            $make = $request->input('mvr.make');
            $model = $request->input('mvr.model');
            $generation = $request->input('mvr.generation_number');
            $chassis = $request->input('bid_info')['chassis'];

            if ($generation) {
                $baseCar = NomenclatureBaseItem::where([
                    'make' => $make,
                    'model' => $model,
                    'generation' => $generation
                ])->first();

                if (!$baseCar) {
                    $baseCar = NomenclatureBaseItem::create([
                        'make' => strtoupper(trim($request['make'])),
                        'model' => strtoupper(trim($request['model'])),
                        'generation' => $mvr['catalog_header']['generation_number'],
                        'preview_image' => $mvr['catalog_header']['model_image_url'],
                    ]);

                    $inner_id = $baseCar->make .
                        $baseCar->model .
                        $baseCar->generation .
                        $baseCar->created_at;
                    $baseCar->setInnerId($inner_id);

                    $car = Car::create([
                        'parent_inner_id' => $baseCar->inner_id,
                        'make' => $make,
                        'model' => $model,
                        'generation' => $generation,
                        'chassis' => $chassis,
                        'created_by' => $userId,
                    ]);

                    $car->importItem()->create([
                        'imported_id' => $request->input('id'),
                        'imported_from' => 'Caparts',
                        'imported_by' => $userId,
                    ]);

                    $car->carAttributes()->create([
                        'chassis' => $chassis,
                        'engine' => $request->input('mvr.engine_type'),
                        'year' => $request->input('mvr.year'),
                        'mileage' => $request->input('mvr.mileage'),
                        'color' => $request->input('mvr.color'),
                    ]);

                    $modification = $car->modification()->create([
                        'body_type' => $request->input('mvr.configuration'),
                        'chassis' => $mvr['catalog_modification']['chassis'][0],
                        'generation' => $request->input('mvr.generation_number'),
                        'engine_size' => $request->input('mvr.engine_size'),
                        'drive_train' => $request->input('mvr.drive'),
                        'header' => $mvr['mvr_header'],
                        'month_from' => $mvr['month_start'],
                        'month_to' => $mvr['month_stop'],
                        'restyle' => $mvr['catalog_generation']['restyle'],
                        'doors' => $mvr['catalog_generation']['doors'],
                        'transmission' => $mvr['catalog_modification']['transmission_type'],
                        'year_from' => $mvr['year_start'],
                        'year_to' => $mvr['year_stop'],
                        'years_string' => $mvr['years_string'],
                    ]);

                    $modification->update(['inner_id' => $this->generateInnerId(
                        $modification->header . $modification->generation . $modification->chassis
                    )]);

                    if (is_array($request->input('images')) && count($request->input('images'))) {
                        foreach ($request->input('images') as $image) {
                            $car->images()->create([
                               'url' => $image,
                                'created_by' => $userId,
                            ]);
                        }
                    }

                    $car->modifications()->create($modification->toArray());

                    return $car->id;
                }
            }
        }
        return -1;
    }
}
