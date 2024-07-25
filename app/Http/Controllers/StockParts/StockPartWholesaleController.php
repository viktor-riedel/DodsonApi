<?php

namespace App\Http\Controllers\StockParts;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Models\Car;
use DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockPartWholesaleController extends Controller
{
    use DefaultSellingMapTrait;

    public function list(Request $request)
    {
        $make = $request->get('make');
        $model = $request->get('model');
        $year = $request->get('year');
        $engine = $request->get('engine');
        $parts = $request->get('parts');
        $generation = $request->get('generation');
        $engine = $request->get('engine');

        $sellingPartNames = null;

        if ($parts) {
            $partsIds = explode(',', $parts);
            $sellingPartNames = $this->getPartsNamesByIds($partsIds);
        }



        return response()->json([]);
    }

    public function defaultPartsList(): JsonResponse
    {
        return response()->json(SellingMapItemResource::collection($this->getDefaultSellingMap()));
    }

    public function makes(): JsonResponse
    {
        $makes = DB::table('cars')
            ->selectRaw('distinct(make)')
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('car_pdr_positions.user_id', 135)
            ->orderBy('cars.make')
            ->get();

        return response()->json($makes);
    }

    public function models(string $make): JsonResponse
    {
        $models = DB::table('cars')
            ->selectRaw('distinct(model)')
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('car_pdr_positions.user_id', 135)
            ->where('cars.make', $make)
            ->orderBy('cars.model')
            ->get();

        return response()->json($models);
    }

    public function years(string $make): JsonResponse
    {
        $years = DB::table('cars')
            ->selectRaw('distinct(car_attributes.year)')
            ->join('car_attributes', function (JoinClause $join) {
                $join->on('car_attributes.car_id', '=', 'cars.id');
            })
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('car_pdr_positions.user_id', 135)
            ->where('cars.make', $make)
            ->orderBy('cars.model')
            ->get();

        return response()->json($years);
    }

    public function engines(string $make, string $model): JsonResponse
    {
        $carsIds = DB::table('cars')
            ->selectRaw('distinct(cars.id)')
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('car_pdr_positions.user_id', 135)
            ->where('cars.make', $make)
            ->where('cars.model', $model)
            ->get()
            ->pluck('id')
            ->toArray();

        $modifications = Car::with('modifications')->whereIn('id', $carsIds)
            ->get()
            ->pluck('modifications');

        return response()->json($modifications);
    }
}
