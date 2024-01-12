<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemModificationsListAction;
use App\Actions\BaseItem\BaseItemModificationsUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Resources\BaseItem\BaseItemPdrPositionModificationResource;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Http\Request;

class BaseItemModificationsController extends Controller
{
    public function index(NomenclatureBaseItem $nomenclatureBaseItem, CatalogApiHelper $apiHelper): \Illuminate\Http\JsonResponse
    {
        $data = app()->make(BaseItemModificationsListAction::class)->handle($nomenclatureBaseItem, $apiHelper);
        return response()->json($data);
    }

    public function modifications(NomenclatureBaseItemPdrPosition $nomenclatureBaseItemPdrPosition): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $modifications = $nomenclatureBaseItemPdrPosition->nomenclatureBaseItemModifications;
        return BaseItemPdrPositionModificationResource::collection($modifications);
    }

    public function update(Request $request, NomenclatureBaseItem $nomenclatureBaseItem, CatalogApiHelper $apiHelper): \Illuminate\Http\JsonResponse
    {
        app()->make(BaseItemModificationsUpdateAction::class)->handle($request, $nomenclatureBaseItem);
        $data = app()->make(BaseItemModificationsListAction::class)->handle($nomenclatureBaseItem, $apiHelper);
        return response()->json($data);
    }
}
