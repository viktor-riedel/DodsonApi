<?php

namespace App\Exports\Excel;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class CreatedCarPartsExcelExport implements FromView
{
    public Collection $parts;

    public function __construct(Collection $parts)
    {
        $this->parts = $parts;
    }

    public function view(): View
    {
        return view('exports.excel.created-car-parts', ['parts' => $this->parts]);
    }
}
