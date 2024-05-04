<?php

namespace App\Http\Controllers\Nomenclature;

use App\Http\Controllers\Controller;
use App\Models\NomenclatureBaseItem;

class BaseItemsSearchController extends Controller
{
    private const TABLE_NAME = 'nomenclature_base_items';

    public function makes(): \Illuminate\Http\JsonResponse
    {
        $makes = \DB::table(self::TABLE_NAME)
                    ->selectRaw('distinct(make)')
                    ->where('make', '!=', '')
                    ->whereNull('deleted_at')
                    ->whereNotNull('make')
                    ->orderBy('make')
                    ->get();
        return response()->json($makes);
    }

    public function models(?string $make = null): \Illuminate\Http\JsonResponse
    {
        $models = \DB::table(self::TABLE_NAME)
            ->selectRaw('distinct(model)')
            ->when($make, function($q) use ($make) {
                $q->where('make', $make);
            })
            ->orderBy('model')
            ->get();
        return response()->json($models);
    }

    public function generations(?string $make = null, ?string $model = null): \Illuminate\Http\JsonResponse
    {
        $generations = \DB::table(self::TABLE_NAME)
            ->selectRaw('distinct(generation)')
            ->when($make, function($q) use ($make) {
                $q->where('make', $make);
            })
            ->when($model, function($q) use ($model) {
                $q->where('model', $model);
            })
            ->orderBy('generation')
            ->get();
        return response()->json($generations);
    }

    public function headers(?string $make = null, ?string $model = null, ?string $generation = null): \Illuminate\Http\JsonResponse
    {
        $headers = \DB::table(self::TABLE_NAME)
            ->selectRaw('distinct(header)')
            ->when($make, function($q) use ($make) {
                $q->where('make', $make);
            })
            ->when($model, function($q) use ($model) {
                $q->where('model', $model);
            })
            ->when($generation, function($q) use ($generation) {
                $q->where('generation', $generation);
            })
            ->orderBy('header')
            ->get();
        return response()->json($headers);
    }

}
