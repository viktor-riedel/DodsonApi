<?php

namespace App\Actions\BaseItemPosition;

use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\User;
use Illuminate\Http\Request;

class CreateBaseItemPositionAction
{
    private User $user;

    public function handle(Request $request, NomenclatureBaseItemPdr $baseItemPdr): NomenclatureBaseItemPdrPosition
    {
        $exist = NomenclatureBaseItemPdrPosition::where('ic_number', $request->input('ic_number'))->first();
        if ($exist && !$request->input('reuse_id')) {
            abort(403, 'IC Number already exists');
        }
        $this->user = $request->user();
        $itemPosition = $baseItemPdr->nomenclatureBaseItemPdrPositions()->create($request->toArray());
        $itemPosition->load('nomenclatureBaseItemPdr');
        if (!$request->input('reuse_id')) {
            $this->createCleanPosition($itemPosition);
        } else {
            $this->createReusedPosition($itemPosition, $request->input('reuse_id'));
        }
        return $itemPosition;
    }

    public function createCleanPosition(NomenclatureBaseItemPdrPosition $itemPosition): void
    {
        $itemPosition->nomenclatureBaseItemPdrCard()->create([
            'name_eng' => $itemPosition->nomenclatureBaseItemPdr->item_name_eng,
            'name_ru' => $itemPosition->nomenclatureBaseItemPdr->item_name_ru,
            'description' => $itemPosition->ic_description,
            'ic_number' => $itemPosition->ic_number,
            'oem_number' => $itemPosition->oem_number,
            'created_by' => $this->user->id,
            'deleted_by' => null,
        ]);
    }

    public function createReusedPosition(NomenclatureBaseItemPdrPosition $itemPosition, int $reuse_id): void
    {
        $reusePosition = NomenclatureBaseItemPdrPosition::with('photos')->find($reuse_id);
        if ($reusePosition) {
            $itemPosition->nomenclatureBaseItemPdrCard()->create([
                'name_eng' => $reusePosition->item_name_eng,
                'name_ru' => $reusePosition->item_name_ru,
                'price_nz_wholesale' => $reusePosition->price_nz_wholesale,
                'price_nz_retail' => $reusePosition->price_nz_retail,
                'price_ru_wholesale' => $reusePosition->price_ru_wholesale,
                'price_ru_retail' => $reusePosition->price_ru_retail,
                'price_jp_minimum_buy' => $reusePosition->price_jp_minimum_buy,
                'price_jp_maximum_buy' => $reusePosition->price_jp_maximum_buy,
                'volume' => $reusePosition->volume,
                'trademe' => $reusePosition->trademe ?? false,
                'drom' => $reusePosition->drom ?? false,
                'avito' => $reusePosition->avito ?? false,
                'dodson' => $reusePosition->dodson ?? false,
                'nova' => $reusePosition->nova ?? false,
                'ic_number' => $reusePosition->ic_number,
                'oem_number' => $reusePosition->oem_number,
                'color' => $reusePosition->color,
                'weight' => $reusePosition->weight,
                'comment' => $reusePosition->comment,
                'description' => $itemPosition->ic_description,
                'extra' => $reusePosition->extra,
                'created_by' => $this->user->id,
                'deleted_by' => null,
            ]);
            if ($reusePosition->photos) {
                foreach($reusePosition->photos as $photo) {
                    $itemPosition->photos()->create($photo->toArray());
                }
            }
            $reusePosition->relatedPositions()->attach($itemPosition);
        }
    }
}
