<?php

namespace App\Http\Controllers\Parts;

use App\Actions\Import\ImportFromPinnacleCsvAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Part\MakeResource;
use App\Http\Resources\Part\ModelResource;
use App\Http\Resources\Part\PartResource;
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
        $text = $request->get('text', '');
        $parts = Part::with('images', 'modifications')
            ->when($make, function($query) use ($make) {
                return $query->where('make', $make);
            })
            ->when($model, function($query) use ($model) {
                return $query->where('model', $model);
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
            'price_jpy' => $request->integer('price_jpy'),
            'price_nzd' => $request->integer('price_nzd'),
            'price_mng' => $request->integer('price_mng'),
        ]);
        $part->refresh();
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
}