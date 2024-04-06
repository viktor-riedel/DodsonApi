<?php

namespace App\Actions\BaseItem;

use App\Http\Traits\InnerIdTrait;
use App\Http\Traits\SyncBaseItemModificationsTrait;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Http\Request;

class BaseItemModificationsGlobalUpdateAction
{
    use InnerIdTrait, SyncBaseItemModificationsTrait;

    public function handle(Request $request, NomenclatureBaseItem $nomenclatureBaseItem): bool
    {
        if (count($request->input('ic_list'))) {
            foreach($request->input('ic_list') as $icNumber) {
                $icNum = NomenclatureBaseItemPdrPosition::with('nomenclatureBaseItemModifications')->find($icNumber['id']);
                    $icNum->nomenclatureBaseItemModifications()->delete();
                    $icNum->modifications()->delete();
                    if (count($request->input('modifications'))) {
                        foreach($request->input('modifications') as $modification) {
                            $mod = $icNum->nomenclatureBaseItemModifications()->create([
                                'inner_id' => $this->generateInnerId(
                                    $modification['header'] .
                                          $modification['generation'] .
                                          $modification['chassis']
                                ),
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
                            $icNum->modifications()->create($mod->toArray());
                        }
                    }
                }

                $this->syncBaseItemModifications($nomenclatureBaseItem);
            }
        return true;
    }
}
