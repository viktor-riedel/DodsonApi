<?php

namespace App\Exports\Excel;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CreatedCarsOrdersExcelExport implements FromView, ShouldAutoSize
{
    public Collection $orders;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
    }

    public function view(): View
    {
        return view('exports.excel.created-orders', [
            'orders' =>  $this->orders,
        ]);
    }
}
