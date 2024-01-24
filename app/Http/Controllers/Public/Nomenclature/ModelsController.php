<?php

namespace App\Http\Controllers\Public\Nomenclature;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\ModelsResource;
use App\Models\NomenclatureBaseItem;

class ModelsController extends Controller
{
    public function list(string $make): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $make = strtoupper($make);
        $models = NomenclatureBaseItem::query()
            ->where('make', $make)
            ->distinct()->orderBy('model')
            ->get('model')
            ->transform(function($model) use ($make) {
                $generations = NomenclatureBaseItem::query()
                    ->where('make', $make)
                    ->where('model', $model->model)
                    ->get('generation');
                return
                   [
                       'model' => $model->model,
                       'generations' => $generations
                   ];
            });

        return ModelsResource::collection($models);

    }
}
