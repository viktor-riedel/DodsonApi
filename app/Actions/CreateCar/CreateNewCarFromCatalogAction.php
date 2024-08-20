<?php

namespace App\Actions\CreateCar;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;

class CreateNewCarFromCatalogAction
{
    use InnerIdTrait;

    public function handle(array $request, int $userId): int
    {
        $helper = new CatalogApiHelper();

        $mvr = $helper->loadModelMvr($request['model_id'], $request['mvr_id']);

        $chassis = $request['modification.chassis'];

        $baseCar = NomenclatureBaseItem::where('make', strtoupper(trim($request['make'])))
            ->where('model', strtoupper(trim($request['model'])))
            ->where('generation', trim($request['generation_num']))
            ->first();

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
        }

        $car = Car::create([
            'parent_inner_id' => $baseCar->inner_id,
            'make' => strtoupper(trim($request['make'])),
            'model' => strtoupper(trim($request['model'])),
            'generation' => trim($request['generation_num']),
            'chassis' => is_array($chassis) && isset($chassis[0]) ? ($chassis[0] . '-') : '',
            'created_by' => $userId,
            'contr_agent_name' => ucwords($request['contr_agent_name']),
        ]);

        $car->carFinance()->create([
           'purchase_price' => $request['car_price'] ?? null,
        ]);

        $car->carAttributes()->create([
            'chassis' => is_array($chassis) && isset($chassis[0]) ? ($chassis[0] . '-') : '',
            'engine' => $request['modification']['engine_name']
        ]);

        $modification = $car->modification()->create([
            'body_type' => $mvr['catalog_generation']['body_type'],
            'chassis' => is_array($chassis) && isset($chassis[0]) ? ($chassis[0] . '-') : '-',
            'generation' => trim($request['generation_num']),
            'engine_size' => $mvr['catalog_modification']['engine_size'],
            'drive_train' => $mvr['catalog_modification']['drive_train'],
            'header' => $mvr['mvr_header'],
            'month_from' => $mvr['month_start'],
            'month_to' => $mvr['year_stop'],
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

        $modification->refresh();
        $car->modifications()->create($modification->toArray());

        return $car->id;
    }
}
