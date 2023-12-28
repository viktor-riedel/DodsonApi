<?php

namespace App\Actions\BaseItemPosition;

use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureCard;
use Illuminate\Http\Request;

class CreateBaseItemPositionAction
{
    public function handle(Request $request, NomenclatureBaseItemPdr $baseItemPdr): NomenclatureBaseItemPdrPosition
    {
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
        $default_card = NomenclatureCard::firstOrCreate();
        $itemPosition->nomenclatureBaseItemPdrCard()->create([
            'name_eng' => $itemPosition->nomenclatureBaseItemPdr->item_name_eng,
            'name_ru' => $itemPosition->nomenclatureBaseItemPdr->item_name_ru,
            'default_price' => $default_card->default_price,
            'default_retail_price' => $default_card->default_retail_price,
            'default_wholesale_price' => $default_card->default_wholesale_price,
            'default_special_price' => $default_card->default_special_price,
            'wholesale_rus_price' => $default_card->wholesale_rus_price,
            'wholesale_nz_price' => $default_card->wholesale_nz_price,
            'retail_rus_price' => $default_card->retail_rus_price,
            'retail_nz_price' => $default_card->retail_nz_price,
            'special_rus_price' => $default_card->special_rus_price,
            'special_nz_price' => $default_card->special_nz_price,
            'comment' => $default_card->comment,
            'description' => $itemPosition->ic_description,
            'status' => $default_card->status,
            'condition' => $default_card->condition,
            'tag' => $default_card->tag,
            'yard' => $default_card->yard,
            'bin' => $default_card->bin,
            'is_new' => $default_card->is_new,
            'is_scrap' => $default_card->is_scrap,
            'ic_number' => $itemPosition->ic_number,
            'oem_number' => $itemPosition->oem_number,
            'inner_number' => $default_card->inner_number,
            'color' => $default_card->color,
            'weight' => $default_card->weight,
            'extra' => $default_card->extra,
            'created_by' => null,
            'deleted_by' => null,
        ]);
    }

    public function createReusedPosition(NomenclatureBaseItemPdrPosition $itemPosition, int $reuse_id): void
    {
        $reusePosition = NomenclatureBaseItemPdrPosition::with('photos')->find($reuse_id);
        if ($reusePosition) {
            $itemPosition->update([
                'name_eng' => $reusePosition->item_name_eng,
                'name_ru' => $reusePosition->item_name_ru,
                'default_price' => $reusePosition->default_price,
                'default_retail_price' => $reusePosition->default_retail_price,
                'default_wholesale_price' => $reusePosition->default_wholesale_price,
                'default_special_price' => $reusePosition->default_special_price,
                'wholesale_rus_price' => $reusePosition->wholesale_rus_price,
                'wholesale_nz_price' => $reusePosition->wholesale_nz_price,
                'retail_rus_price' => $reusePosition->retail_rus_price,
                'retail_nz_price' => $reusePosition->retail_nz_price,
                'special_rus_price' => $reusePosition->special_rus_price,
                'special_nz_price' => $reusePosition->special_nz_price,
                'comment' => $reusePosition->comment,
                'description' => $itemPosition->ic_description,
                'status' => $reusePosition->status,
                'condition' => $reusePosition->condition,
                'tag' => $reusePosition->tag,
                'yard' => $reusePosition->yard,
                'bin' => $reusePosition->bin,
                'is_new' => $reusePosition->is_new,
                'is_scrap' => $reusePosition->is_scrap,
                'ic_number' => $reusePosition->ic_number,
                'oem_number' => $itemPosition->oem_number,
                'inner_number' => $reusePosition->inner_number,
                'color' => $reusePosition->color,
                'weight' => $reusePosition->weight,
                'extra' => $reusePosition->extra,
                'created_by' => null,
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
