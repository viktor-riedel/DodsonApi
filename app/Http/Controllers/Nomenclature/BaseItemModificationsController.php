<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemModificationsListAction;
use App\Actions\BaseItem\BaseItemPositionModificationUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CatalogApiHelper;
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
}
