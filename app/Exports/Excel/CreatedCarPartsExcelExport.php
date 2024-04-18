<?php

namespace App\Exports\Excel;

use App\Models\Car;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class CreatedCarPartsExcelExport implements FromView
{
    public Collection $parts;
    public Car $car;

    public function __construct(Car $car, Collection $parts)
    {
        $this->parts = $parts;
        $this->car = $car;
    }

    public function view(): View
    {
        return view('exports.excel.created-car-parts', [
            'car' =>  $this->car,
            'parts' => $this->parts
        ]);
    }
}
