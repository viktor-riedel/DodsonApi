<?php

namespace App\Events\StockCars;

use App\Models\Car;
use Illuminate\Foundation\Events\Dispatchable;

class SyncWishedCarEvent
{
    use Dispatchable;

    public Car $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }
}
