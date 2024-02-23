<?php

namespace App\Actions\CreateCar;

use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class CreateNewCarAction
{
    public function handle(Request $request)
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
    }
}
