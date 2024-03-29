<?php

namespace App\Actions\BaseItem;

use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Http\Request;

class BaseItemPositionModificationUpdateAction
{
    public function handle(Request $request, NomenclatureBaseItemPdrPosition $nomenclatureBaseItemPosition): bool
    {
        $nomenclatureBaseItemPosition->nomenclatureBaseItemModifications()->delete();
        if (count($request->toArray()) > 0) {
            foreach($request->toArray() as $modification) {
                $nomenclatureBaseItemPosition->nomenclatureBaseItemModifications()->create([
                    'header' => $modification['header'],
                    'generation' => $modification['generation'],
                    'modification' => $modification['modification'],
                    'engine_name' => $modification['engine_name'],
                    'engine_type' => $modification['engine_type'],
                    'engine_size' => $modification['engine_size'],
                    'engine_power' => $modification['engine_power'],
                    'doors' => $modification['doors'],
                    'transmission' => $modification['transmission'],
                    'drive_train' => $modification['drive_train'],
                    'chassis' => $modification['chassis'],
                    'body_type' => $modification['body_type'],
                    'image_url' => $modification['image_url'],
                    'restyle' => $modification['restyle'],
                    'not_restyle' => $modification['not_restyle'],
                    'month_from' => $modification['month_from'],
                    'month_to' => $modification['month_to'],
                    'year_from' => $modification['year_from'],
                    'year_to' => $modification['year_to'],
                ]);
            }

        }
        return true;
    }
}
