<?php

namespace App\Http\Controllers\StockParts;

use App\Http\Controllers\Controller;
use App\Http\Resources\Part\MakeResource;
use App\Http\Resources\Part\ModelResource;
use App\Http\Resources\Part\PartResource;
use App\Http\Resources\Part\YearResource;
use App\Models\Part;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockPartsController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make', null);
        $model = $request->get('model', null);
        $year = $request->get('year', null);
        $searchText = $request->get('search', null);
        $sortByMake = $request->get('sortByMake', null);
        $sortByModel = $request->get('sortByModel', null);
        $sortByYear = $request->get('sortByYear', null);
        $sortByPrice = $request->get('sortByPrice', null);
        $country = $request->get('country', null);

        if ($searchText) {
            $searchText = implode('|', explode(' ', $searchText));
        }

        $parts = Part::with('images', 'modifications')
            ->when($make, function($query) use ($make) {
                return $query->where('make', $make);
            })
            ->when($model, function($query) use ($model) {
                return $query->where('model', $model);
            })
            ->when($year, function($query) use ($year) {
                return $query->where('year', $year);
            })
            ->when($sortByMake, function ($query, $sortByMake) {
                return $query->orderBy('make', $sortByMake);
            })
            ->when($sortByModel, function ($query, $sortByModel) {
                return $query->orderBy('model', $sortByModel);
            })
            ->when($sortByYear, function ($query, $sortByYear) {
                return $query->orderBy('year', $sortByYear);
            })
            ->when($sortByPrice, function ($query, $sortByPrice) {
                return $query->orderBy('price_jpy', $sortByPrice);
            })
            ->where(function($query) use ($searchText) {
                return $query->when($searchText, function($query) use ($searchText) {
                    return $query->where('stock_number', 'REGEXP', $searchText)
                        ->orWhere('make', 'REGEXP', $searchText)
                        ->orWhere('model', 'REGEXP', $searchText)
                        ->orWhere('item_name_eng', 'REGEXP', $searchText)
                        ->orWhere('ic_number', 'REGEXP', $searchText);
                });
            })->where('price_jpy', '>', 0)
            ->orderBy('stock_number')
            ->orderBy('make')
            ->orderBy('model')
            ->paginate(50);
        return PartResource::collection($parts);
    }

    public function get(Part $part): PartResource
    {
        $part->load('images', 'modifications');
        return new PartResource($part);
    }

    public function makes(): AnonymousResourceCollection
    {
        $makes = DB::table('parts')
            ->selectRaw('distinct(make)')
            ->where('make', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('make')
            ->orderBy('make')
            ->get();

        return MakeResource::collection($makes);

    }

    public function models(string $make): AnonymousResourceCollection
    {
        $models = DB::table('parts')
            ->selectRaw('distinct(model)')
            ->where('make', '=', $make)
            ->where('model', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('model')
            ->orderBy('model')
            ->get();

        return ModelResource::collection($models);
    }

    public function years(string $make, string $model): AnonymousResourceCollection
    {
        $years = DB::table('parts')
            ->selectRaw('distinct(year)')
            ->where('make', '=', $make)
            ->where('model', '!=', '')
            ->where('model', '=', $model)
            ->where('year', '>', 0)
            ->whereNull('deleted_at')
            ->whereNotNull('model')
            ->orderBy('year')
            ->get();

        return YearResource::collection($years);
    }
}
