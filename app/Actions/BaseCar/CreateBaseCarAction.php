<?php

namespace App\Actions\BaseCar;

use App\Models\BaseCar;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class CreateBaseCarAction
{
    public function handle(Request $request): int
    {
        $nomenclatureItem = NomenclatureBaseItem::where(
            [
                'make' => $request->input('make'),
                'model' => $request->input('model'),
                'generation' => $request->input('generation_number'),
                'restyle' => !$request->input('restyle') ? null : 1,
            ]
        )->first();

        if (!$nomenclatureItem) {
            abort(404, 'Nomenclature base item not found');
        }

        $baseCar = $nomenclatureItem->baseCars()->create([
            'make' => mb_strtoupper($request->input('make')),
            'model' => mb_strtoupper($request->input('model')),
            'header' => $request->input('header'),
            'generation' => mb_strtoupper($request->input('generation')),
            'generation_number' => (int) $request->input('generation_number'),
            'body_type' => mb_strtoupper($request->input('body_type')),
            'doors' => (int) $request->input('doors'),
            'month_start' => (int) $request->input('month_start'),
            'month_stop' => (int) $request->input('month_stop'),
            'year_start' => (int) $request->input('year_start'),
            'year_stop' => (int) $request->input('year_stop'),
            'restyle' => $request->input('restyle') ?? false,
            'not_restyle' => $request->input('not_restyle') ?? false,
        ]) ;

        return $baseCar->id;
    }
}
