<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\DefaultNomenclatureCardResource;
use App\Models\NomenclatureCard;
use Illuminate\Http\Request;

class NomenclatureCardController extends Controller
{
    public function index(): DefaultNomenclatureCardResource
    {
        $card = NomenclatureCard::get()->first();
        if (!$card) {
            //create a new one on request
            $card = NomenclatureCard::create();
        }
        return new DefaultNomenclatureCardResource($card);
    }

    public function update(Request $request): DefaultNomenclatureCardResource
    {
        $card = NomenclatureCard::get()->first();
        $card?->update(['deleted_by' => 1]);
        $card?->delete();
        $card = NomenclatureCard::create([
            'name_eng' => $request->input('name_eng'),
            'name_ru' => $request->input('name_ru'),
            'default_price' => $request->input('default_price'),
            'default_wholesale_price' => $request->input('default_wholesale_price'),
            'default_retail_price' => $request->input('default_retail_price'),
            'default_special_price' => $request->input('default_special_price'),
            'wholesale_rus_price' => $request->input('wholesale_rus_price'),
            'wholesale_nz_price' => $request->input('wholesale_nz_price'),
            'retail_rus_price' => $request->input('retail_rus_price'),
            'retail_nz_price' => $request->input('retail_nz_price'),
            'special_rus_price' => $request->input('special_rus_price'),
            'special_nz_price' => $request->input('special_nz_price'),
            'comment' => $request->input('comment'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'condition' => $request->input('condition'),
            'tag' => $request->input('tag'),
            'yard' => $request->input('yard'),
            'bin' => $request->input('bin'),
            'is_new' => (bool) $request->input('is_new'),
            'is_scrap' => (bool) $request->input('is_scrap'),
            'ic_number' => $request->input('ic_number'),
            'oem_number' => $request->input('oem_number'),
            'inner_number' => $request->input('inner_number'),
            'color' => $request->input('color'),
            'weight' => $request->input('weight'),
            'extra' => $request->input('extra'),
            'created_by' => 1, //temp
            'deleted_by' => null,
        ]);
        return new DefaultNomenclatureCardResource($card);
    }
}
