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
        ])->first();
        abort_if($item !== null, 400, 'Item already exist');

        //create base item
        $nomenclatureBaseItem = NomenclatureBaseItem::create([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'generation' => $request->input('generation'),
            'preview_image' => $request->input('preview_image'),
            'created_by' => $request->user()->id,
            'deleted_by' => null,
        ]);
        $innerId =  $nomenclatureBaseItem->generateInnerId(
            $nomenclatureBaseItem->make .
            $nomenclatureBaseItem->model .
            $nomenclatureBaseItem->generation .
            $nomenclatureBaseItem->created_at
        );
        $nomenclatureBaseItem->setInnerId($innerId);
        return $nomenclatureBaseItem->id;
    }
}
