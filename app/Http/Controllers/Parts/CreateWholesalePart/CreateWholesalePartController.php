<?php

namespace App\Http\Controllers\Parts\CreateWholesalePart;

use App\Actions\Parts\CreateWholesalePartsAction;
use App\Http\Controllers\Controller;
use App\Http\Traits\BaseCarTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateWholesalePartController extends Controller
{
    use BaseCarTrait;

    public function getMakes(): JsonResponse
    {
        return $this->makes();
    }

    public function getModels(string $make): JsonResponse
    {
        return $this->models($make);
    }

    public function getGenerations(string $make, string $model): JsonResponse
    {
        return $this->generations($make, $model);
    }

    public function getModifications(string $make, string $model, string $generation): JsonResponse
    {
        return $this->modifications($make, $model, $generation);
    }

    public function getParts(string $modification): JsonResponse
    {
        return $this->partsListByModification($modification);
    }

    public function createParts(Request $request): JsonResponse
    {
        $result = app()->make(CreateWholesalePartsAction::class)->handle($request);
        return response()->json(['created' => $result]);
    }
}
