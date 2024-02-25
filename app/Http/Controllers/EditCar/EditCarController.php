<?php

namespace App\Http\Controllers\EditCar;

use App\Http\Controllers\Controller;
use App\Http\Traits\CarPdrTrait;
use App\Models\Car;

class EditCarController extends Controller
{
    use CarPdrTrait;

    public function edit(Car $car)
    {
        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $parts = $this->buildPdrTreeWithoutEmpty($car);
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');

        return response()->json([
           'car_info' => $car,
           'parts_tree' => $parts,
           'parts_list' => $partsList,
           'car_statuses' => Car::CAR_STATUSES,
        ]);
    }
}
