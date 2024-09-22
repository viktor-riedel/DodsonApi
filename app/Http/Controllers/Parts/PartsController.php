<?php

namespace App\Http\Controllers\Parts;

use App\Actions\Import\ImportFromPinnacleCsvAction;
use App\Actions\TradeMe\TradeMeListingAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Part\MakeResource;
use App\Http\Resources\Part\ModelResource;
use App\Http\Resources\Part\PartGroupResource;
use App\Http\Resources\Part\PartNameResource;
use App\Http\Resources\Part\PartResource;
use App\Http\Resources\Part\YearResource;
use App\Models\Part;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PartsController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make', '');
        $model = $request->get('model', '');
        $years = $request->get('years', '');
        $text = $request->get('text', '');
        $itemNames = $request->get('part_names', '');
        $groupNames = $request->get('part_groups', '');

        $names = [];
        $groups = [];

        if ($itemNames) {
            $names = explode(',', $itemNames);
        }

        if ($groupNames) {
            $groups = explode(',', $groupNames);
        }

        $parts = Part::with('images', 'modifications')
            ->when($make, function($query) use ($make) {
                return $query->where('make', $make);
            })
            ->when($model, function($query) use ($model) {
                return $query->where('model', $model);
            })
            ->when($years, function($query) use ($years) {
                return $query->where('year', $years);
            })
            ->when(count($names), function($query) use ($names) {
                return $query->whereIn('item_name_eng', $names);
            })
            ->when(count($groups), function($query) use ($groups) {
                return $query->whereIn('part_group', $groups);
            })
            ->where(function($query) use ($text) {
                return $query->when($text, function($query) use ($text) {
                return $query->where('stock_number', 'like', "%$text%")
                    ->orWhere('item_name_eng', 'like', "%$text%")
                    ->orWhere('ic_number', 'like', "%$text%")
                    ->orWhere('ic_description', 'like', "%$text%");
                });
            })
            ->orderBy('stock_number')
            ->orderBy('make')
            ->orderBy('model')
            ->paginate(50);
        return PartResource::collection($parts);
    }

    public function delete(Part $part): JsonResponse
    {
        $part->delete();
        return response()->json(null, 204);
    }

    public function get(Part $part): PartResource
    {
        return new PartResource($part);
    }

    public function update(Request $request, Part $part): PartResource
    {
        $part->update([
            'stock_number' => $request->input('stock_number'),
            'ic_number' => $request->input('ic_number'),
            'ic_description' => $request->input('ic_description'),
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'year' => $request->input('year'),
            'mileage' => $request->integer('mileage'),
            'item_name_eng' => $request->input('item_name_eng'),
            'item_name_ru' => $request->input('item_name_ru'),
            'item_name_jp' => $request->input('item_name_jp'),
            'item_name_mng' => $request->input('item_name_mng'),
            'actual_price_nzd' => $request->integer('price_nzd'),
            'standard_price_nzd' => $request->integer('standard_price_nzd'),
        ]);
        $part->refresh();
        return new PartResource($part);
    }

    public function partNames(): AnonymousResourceCollection
    {
        $partNames = DB::table('parts')
            ->selectRaw('distinct item_name_eng')
            ->orderBy('item_name_eng')
            ->get();
        return PartNameResource::collection($partNames);
    }

    public function partGroups(): AnonymousResourceCollection
    {
        $partGroups = DB::table('parts')
            ->selectRaw('distinct part_group')
            ->orderBy('part_group')
            ->get();
        return PartGroupResource::collection($partGroups);
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
            ->where('model', $model)
            ->where('model', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('model')
            ->orderBy('year')
            ->get();

        return YearResource::collection($years);
    }

    public function importFromPinnacle(Request $request): JsonResponse
    {
        if ($request->file('uploadPartsPinnacle')) {
            app()->make(ImportFromPinnacleCsvAction::class)->handle($request);
        }
        return response()->json(['loaded' => 100]);
    }

    public function importFromOneC(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function tradeMeListing(Part $part): JsonResponse
    {
        $listing = app()->make(TradeMeListingAction::class)->handle($part);
        return response()->json($listing);
    }
}
