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
            'generation' => $request->input('generation'),
            'restyle' => $request->input('restyle') ? (bool) $request->input('restyle') : null,
        ])->first();
        abort_if($item !== null, 400, 'Item already exist');

        //create base item
        $nomenclatureBaseItem = NomenclatureBaseItem::create([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'generation' => $request->input('generation'),
            'generation_number' => $request->input('generation_number'),
            'preview_image' => $request->input('preview_image'),
            'restyle' => $request->input('restyle') ? (bool) $request->input('restyle') : null,
            'not_restyle' => $request->input('not_restyle') ? (bool) $request->input('not_restyle') : null,
            'created_by' => $request->user()->id,
            'deleted_by' => null,
        ]);
        return $nomenclatureBaseItem->id;
    }
}
