<?php

namespace App\Actions\BaseItem;

use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class BaseItemCreateAction
{
    public function handle(Request $request): int
    {
        $item = NomenclatureBaseItem::where([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'header' => $request->input('header'),
            'generation' => $request->input('generation'),
            'year_start' => $request->input('year_start'),
            'year_stop' => $request->input('year_stop'),
            'month_start' => $request->input('month_start'),
            'month_stop' => $request->input('month_stop'),
        ])->first();
        abort_if($item !== null, 400, 'Item already exist');

        //create base item
        $nomenclatureBaseItem = NomenclatureBaseItem::create([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'header' => $request->input('header'),
            'generation' => $request->input('generation'),
            'generation_number' => $request->input('generation_number'),
            'year_start' => $request->input('year_start'),
            'year_stop' => $request->input('year_stop'),
            'month_start' => $request->input('month_start'),
            'month_stop' => $request->input('month_stop'),
            'preview_image' => $request->input('preview_image'),
            'restyle' => (bool) $request->input('restyle'),
            'not_restyle' => (bool) $request->input('not_restyle'),
            'doors' => $request->input('doors'),
            'body_type' => $request->input('body_type'),
            'engine_name' => $request->input('engine_name'),
            'engine_type' => $request->input('engine_type'),
            'engine_size' => $request->input('engine_size'),
            'engine_power' => $request->input('engine_power'),
            'transmission_type' => $request->input('transmission_type'),
            'drive_train' => $request->input('drive_train'),
            'chassis' => $request->input('chassis'),
            'created_by' => null,
            'deleted_by' => null,
        ]);
        return $nomenclatureBaseItem->id;
    }
}
