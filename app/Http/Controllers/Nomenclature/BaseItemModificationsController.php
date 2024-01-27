<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemModificationListViewAction;
use App\Actions\BaseItem\BaseItemModificationsGlobalAction;
use App\Actions\BaseItem\BaseItemModificationsGlobalUpdateAction;
use App\Actions\BaseItem\BaseItemModificationsListAction;
use App\Actions\BaseItem\BaseItemPositionModificationUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Resources\ItemPdrPositionListResource;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Http\Request;

class BaseItemModificationsController extends Controller
{

    public function modifications(NomenclatureBaseItemPdrPosition $nomenclatureBaseItemPdrPosition,CatalogApiHelper $apiHelper): \Illuminate\Http\JsonResponse
    {
        $data = app()->make(BaseItemModificationsListAction::class)->handle($nomenclatureBaseItemPdrPosition, $apiHelper);
        return  response()->json($data);
    }

    public function update(Request $request, NomenclatureBaseItemPdrPosition $nomenclatureBaseItemPosition): \Illuminate\Http\JsonResponse
    {
        $result = app()->make(BaseItemPositionModificationUpdateAction::class)->handle($request, $nomenclatureBaseItemPosition);
        return response()->json($result);
    }

    public function icList(NomenclatureBaseItem $nomenclatureBaseItem, CatalogApiHelper $apiHelper): \Illuminate\Http\JsonResponse
    {
        $result = app()->make(BaseItemModificationsGlobalAction::class)->handle($nomenclatureBaseItem, $apiHelper);
        return response()->json($result);
    }

    public function updateModifications(Request $request, NomenclatureBaseItem $nomenclatureBaseItem): \Illuminate\Http\JsonResponse
    {
        $result = app()->make(BaseItemModificationsGlobalUpdateAction::class)->handle($request, $nomenclatureBaseItem);
        return response()->json($result);
    }

    public function icListView(NomenclatureBaseItem $nomenclatureBaseItem)
    {
        $list = app()->make(BaseItemModificationListViewAction::class)->handle($nomenclatureBaseItem);
        return ItemPdrPositionListResource::collection($list);
    }
}
