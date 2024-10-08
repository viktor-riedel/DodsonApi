<?php

namespace App\Http\Controllers\EditCar;

use App\Actions\CreateCar\AddListPartsAction;
use App\Actions\CreateCar\AddMiscPartsAction;
use App\Actions\CreateCar\AddPartsFromModificationListAction;
use App\Actions\CreateCar\AddPartsFromSellingListAction;
use App\Actions\CreateCar\ChangeModificationAction;
use App\Actions\CreateCar\SetDefaultPriceCategoryAction;
use App\Actions\CreateCar\UpdateIcNumberAction;
use App\Exports\Excel\CreatedCarPartsExcelExport;
use App\Exports\Excel\CreatedCarPriceExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\CarPartsComment\CarPartsCommentResource;
use App\Http\Resources\Cart\LinkResource;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Traits\BadgeGeneratorTrait;
use App\Http\Traits\CarPdrTrait;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Http\Traits\SyncPartsPricesTrait;
use App\Http\Traits\SyncPartWithOrderTrait;
use App\Jobs\Sync\SendCarToBotJob;
use App\Jobs\Sync\SendDoneCarJob;
use App\Models\Car;
use App\Models\CarPartsComment;
use App\Models\CarPdrPositionCard;
use App\Models\CarPdrPositionCardAttribute;
use App\Models\CarPdrPositionCardPrice;
use App\Models\Link;
use App\Models\MediaFile;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class EditCarController extends Controller
{
    use CarPdrTrait, SyncPartWithOrderTrait, DefaultSellingMapTrait, BadgeGeneratorTrait, SyncPartsPricesTrait;

    public function edit(Car $car): JsonResponse
    {
        $car->load('images',
            'links',
            'carAttributes',
            'modification',
            'modifications',
            'createdBy',
            'latestSyncData',
            'markets',
            'carFinance');
        $parts = [];
        $defaultSellingParts = $this->getDefaultSellingMap();
        $partsList = $this->getPartsList($car);
        $clients = User::withoutRole('ADMIN')
            ->where('is_api_user', 0)
            ->orderBy('name')
            ->get()
            ->transform(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            });
        $car->unsetRelation('pdrs');

        if ($car->markets->count()) {
            $car->markets->transform(function($market) {
               return [
                 'name' => findCountryByCode($market->country_code),
                 'country_code' => $market->country_code,
               ];
            });
        }

        return response()->json([
           'car_info' => $car,
           'parts_tree' => $parts,
           'selling_parts' => SellingMapItemResource::collection($defaultSellingParts),
           'parts_list' => $partsList,
           'car_statuses' => Car::getStatusesJson(),
           'clients' => $clients,
        ]);
    }

    public function delete(Request $request, Car $car): JsonResponse
    {
        $orderExists = OrderItem::where('car_id', $car->id)->exists();
        if (!$orderExists) {
            $car->update(['deleted_by' => $request->user()->id]);
            $car->delete();
            return response()->json([], 202);
        }
        abort(403, 'Car has order created!');
    }

    public function uploadCarPhoto(Request $request, Car $car): JsonResponse
    {
        if ($request->file('uploadCarPhotos')) {
            $storage = \Storage::disk('s3');
            foreach ($request->file('uploadCarPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file?->getFilename();
                $folderName = 'cars/' . $car->id . '/photos';
                $mime = $file?->getMimeType();
                $fileExtension = '.' . $file?->clientExtension() ?? 'jpg';
                $savePath = $folderName . '/' . $fileName . $fileExtension;
                $size = $file->getSize();
                if ($size) {
                    $storage->put($savePath, $file->getContent(), 'public');
                    $car->images()->create([
                        'url' => $storage->url($savePath),
                        'mime' => $mime,
                        'original_file_name' => $originFileName,
                        'folder_name' => $folderName,
                        'extension' => $fileExtension,
                        'file_size' => $size,
                        'special_flag' => null,
                        'created_by' => $request->user()->id,
                    ]);
                }
            }
        }
        return response()->json($car->images);
    }

    public function deleteCarPhoto(Request $request, Car $car, MediaFile $photo): JsonResponse
    {
        $photo = $car->images()->where('id', $photo->id)->first();
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($car->images);
    }

    public function updateCar(Request $request, Car $car): JsonResponse
    {
        $car->update([
            'generation' => strtoupper(trim($request->input('generation'))),
            'car_mvr' => trim($request->input('car_mvr')),
            'comment' => trim($request->input('comment')),
            'contr_agent_name' => ucwords(trim($request->input('contr_agent_name'))),
            'chassis' => strtoupper(trim($request->input('chassis'))),
        ]);
        $car->carAttributes()->update([
            'color' => strtoupper(trim($request->input('color'))),
            'mileage' => $request->integer('mileage'),
            'engine' => strtoupper(trim($request->input('engine'))),
            'chassis' => strtoupper(trim($request->input('chassis'))),
            'year' => $request->integer('year'),
        ]);
        $car->update([
            'generation' => strtoupper(trim($request->input('generation'))),
            'chassis' => strtoupper(trim($request->input('chassis'))),
        ]);

        $car->markets()->delete();
        foreach($request->input('markets') as $market) {
            $car->markets()->create([
                'country_code' => $market['country_code'],
            ]);
        }

        //send to bot
        if ($request->input('car_is_for_sale') && !$car->carFinance->car_is_for_sale && config('app.env') === 'production') {
            SendCarToBotJob::dispatch($car);
        }

        $car->carFinance()->update([
            'price_with_engine_nz' => $request->integer('price_with_engine_nz'),
            'price_without_engine_nz' => $request->integer('price_without_engine_nz'),
            'price_without_engine_ru' => $request->integer('price_without_engine_ru'),
            'price_with_engine_ru' => $request->integer('price_with_engine_ru'),
            'price_with_engine_mn' => $request->integer('price_with_engine_mn'),
            'price_without_engine_mn' => $request->integer('price_without_engine_mn'),
            'price_with_engine_jp' => $request->integer('price_with_engine_jp'),
            'price_without_engine_jp' => $request->integer('price_without_engine_jp'),
            'purchase_price' => $request->integer('purchase_price'),
            'car_is_for_sale' => (bool) $request->input('car_is_for_sale'),
            'parts_for_sale' => false, //(bool) $request->input('parts_for_sale'),
        ]);


        return response()->json([], 202);
    }

    public function updateCarStatus(Request $request, Car $car): JsonResponse
    {
        $car->load('positions', 'positions.card', 'positions.card.priceCard');

        $status = (int) $request->input('car_status');
        if (($status === 3 || $status === 4 || $status === 2) && !$car->car_mvr) {
            return response()->json(['error' => 'MVR not set'], 403);
        }

        $sum = $car->positions->sum('card.priceCard.selling_price');
        if ($status === 2 && !$car->carFinance->purchase_price && !$sum) {
            return response()->json(['error' => 'One of prices should be set'], 403);
        }

        $car->statusLogs()->create([
            'old_status' => $car->car_status,
            'new_status' => (int) $request->input('car_status'),
            'user_id' => $request->user()->id,
        ]);

        $car->update(['car_status' => (int) $request->input('car_status')]);
        return response()->json([], 202);
    }

    public function syncCar(Request $request, Car $car): JsonResponse
    {
        if ($car->car_status === 2) {
            SendDoneCarJob::dispatch($car, $request->user());
            return response()->json([], 202);
        }
        return response()->json(['error' => 'Car status is not DONE'], 403);
    }

    public function generateDismantlingBadges(Request $request, Car $car): JsonResponse
    {
        $needsRefresh = false;
        $partsToPrint = collect($request->toArray())->pluck('card.car_pdr_position_id')->toArray();
        $partsList = $this->getPartsList($car, $partsToPrint);
        foreach($partsList as $part) {
            if (!$part->barcode) {
                $needsRefresh = true;
                $part = CarPdrPositionCard::find($part->id);
                $part->update(['barcode' => $this->generateNextBarcode()]);
            }
        }
        if ($needsRefresh) {
            $partsList = $this->getPartsList($car);
        }
        $pdf = Pdf::loadView('exports.pdf.dismantling-badges', [
            'parts' => $partsList,
            'car' => $car,
        ])->stream();
        $storage = \Storage::disk('s3');
        $folderName = 'cars/' . $car->id . '/documents';
        $fileName = $car->make . '_' . $car->model . '_' . \Str::replace('-', '', $car->chassis) . '_dismantling';
        $savePath = $folderName.'/'.$fileName . '.pdf';
        $storage->put($savePath, $pdf, 'public');
        $url = \Storage::disk('s3')->url($savePath);
        return response()->json(['link' => $url]);
    }

    public function generateDismantlingDocument(Request $request, Car $car): JsonResponse
    {
        $partsList = $this->getPartsList($car);
        $pdf = Pdf::loadView('exports.pdf.dismantling-document', [
            'parts' => $partsList,
            'car' => $car,
        ])->stream();
        $storage = \Storage::disk('s3');
        $folderName = 'cars/' . $car->id . '/documents';
        $fileName = $car->make . '_' . $car->model . '_' . \Str::replace('-', '', $car->chassis) . '_dismantling';
        $savePath = $folderName.'/'.$fileName . '.pdf';
        $storage->put($savePath, $pdf, 'public');
        $url = \Storage::disk('s3')->url($savePath);
        return response()->json(['link' => $url]);
    }

    public function deletePart(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $card->update(['deleted_by' => $request->user()->id]);
        $card->images()->update(['deleted_by' => $request->user()->id]);
        $card->position()->update(['deleted_by' => $request->user()->id]);

        //sync with order
        if ($card->position->client) {
            $this->deletePartFromOrder($car, $card->position);
        }

        $carPdr = $card->position->carPdr;
        $card->position()->delete();

        //delete folder is empty
        if (!$carPdr->positions()->count()) {
            $carPdr->delete();
        }
        $card->delete();

        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');
        return response()->json($partsList);
    }

    public function deleteParts(Request $request, Car $car): JsonResponse
    {
        if (count($request->all())) {
            foreach($request->all() as $position) {
                $card = CarPdrPositionCard::find($position['card_id']);
                if ($card) {
                    $card->update(['deleted_by' => $request->user()->id]);
                    $card->images()->update(['deleted_by' => $request->user()->id]);
                    $card->position()->update(['deleted_by' => $request->user()->id]);

                    $this->deletePartFromOrder($car, $card->position);

                    $carPdr = $card->position->carPdr;
                    $card->position()->delete();

                    //delete folder is empty
                    if (!$carPdr->positions()->count()) {
                        $carPdr->delete();
                    }
                    $card->delete();
                }
            }
        }
        return response()->json([], 202);
    }


    public function uploadPartPhoto(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        if ($request->file('uploadPartPhotos')) {
            $storage = \Storage::disk('s3');
            foreach ($request->file('uploadPartPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'cars/' . $car->id . '/parts/' . $card->id;
                $mime = $file->getMimeType();
                $fileExtension = '.'.$file->clientExtension();
                $savePath = $folderName.'/'.$fileName.$fileExtension;
                $size = $file->getSize();
                $storage->put($savePath, $file->getContent(), 'public');
                $card->images()->create([
                    'url' => $storage->url($savePath),
                    'mime' => $mime,
                    'original_file_name' => $originFileName,
                    'folder_name' => $folderName,
                    'extension' => $fileExtension,
                    'file_size' => $size,
                    'special_flag' => null,
                    'created_by' => $request->user()->id,
                ]);
            }
        }
        return response()->json($card->images);
    }

    public function deletePartPhoto(Request $request, Car $car, CarPdrPositionCard $card, MediaFile $photo): JsonResponse
    {
        $photo = $card->images()->where('id', $photo->id)->first();
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($card->images);
    }

    public function updateAttributes(Request $request, Car $car, CarPdrPositionCardAttribute $card): JsonResponse
    {
        $card->update($request->except('car_pdr_position_id', 'id'));
        return response()->json([], 202);
    }

    public function updatePriceCard(Request $request, Car $car, CarPdrPositionCardPrice $card): JsonResponse
    {
        $card->update($request->except('car_pdr_position_card_id', 'id'));
        return response()->json([], 202);
    }

    public function updateOriginalPriceCard(Request $request, Car $car, NomenclatureBaseItemPdrCard $card): JsonResponse
    {
        //TO DO send 1C update request receive back needs
        $card->update($request->except('id'));
        return response()->json([], 202);
    }

    public function addMiscParts(Request $request, Car $car): JsonResponse
    {
        app()->make(AddMiscPartsAction::class)->handle($car, $request->user()->id, $request->all());
        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');
        return response()->json($partsList);
    }

    public function addListParts(Request $request, Car $car): JsonResponse
    {
        app()->make(AddListPartsAction::class)->handle($car, $request->user()->id, $request->all());
        return response()->json([], 201);
    }

    public function parts(Car $car): JsonResponse
    {
        $pdr = $this->buildDefaultPdrTreeByCar($car);
        return response()->json($pdr);
    }

    public function addModListParts(Request $request, Car $car): JsonResponse
    {
        app()->make(AddPartsFromModificationListAction::class)->handle($car, $request->all(), $request->user()->id);
        return response()->json([], 201);
    }

    public function addSellingListParts(Request $request, Car $car): JsonResponse
    {
        app()->make(AddPartsFromSellingListAction::class)->handle($car, $request->all(), $request->user()->id);
        return response()->json([], 201);
    }

    public function exportPartsListToExcel(Car $car): JsonResponse
    {
        $filename = 'exports/parts/'. $car->id . '/' . $car->car_mvr . '_' . $car->make . '_' .
            $car->model . '_' . $car->generation . '.xlsx';
        $partsList = $this->getPartsList($car);
        Excel::store(new CreatedCarPartsExcelExport($car, $partsList), $filename, 's3', null, ['visibility' => 'public']);
        $url = \Storage::disk('s3')->url($filename);
        return response()->json(['link' => $url]);
    }

    public function exportPriceListToExcel(Car $car): JsonResponse
    {
        $filename = 'exports/prices/'. $car->id . '/' . $car->car_mvr . '_' . $car->make . '_' .
            $car->model . '_' . $car->generation . '-pricing.xlsx';
        $partsList = $this->getPartsList($car);
        Excel::store(new CreatedCarPriceExcelExport($car, $partsList), $filename, 's3', null, ['visibility' => 'public']);
        $url = \Storage::disk('s3')->url($filename);
        return response()->json(['link' => $url]);
    }

    public function updateModification(Request $request, Car $car): JsonResponse
    {
        $result = app()->make(ChangeModificationAction::class)->handle($request, $car, $request->user());
        return response()->json(['result' => $result]);
    }

    public function setPartsUser(Request $request, Car $car, User $user): JsonResponse
    {
        $car->load('positions', 'positions.card');
        if ($car->positions->count()) {
            foreach($car->positions as $position) {
                $this->deletePartFromOrder($car, $position);
                $position->update(['user_id' => $user->id]);
                //sync with order if any
                $this->addPartToOrder($car, $user->id, $position);
            }
        }
        return response()->json([], 202);
    }

    public function updateICNumber(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $result = app()->make(UpdateIcNumberAction::class)->handle($request, $car, $card);
        return response()->json([
            'original_card' => $result['original_card'],
            'price_card' => $result['price_card'],
            'card' => $result['card'],
        ], 202);
    }

    public function updatePriceCurrency(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $card->priceCard()->update([
            'price_currency' => strtoupper(trim($request->input('price_currency')))
        ]);
        return response()->json([], 204);
    }

    public function updateBuyingPrice(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $currency = $card->priceCard->price_currency ?: 'JPY';
        $card->priceCard()->update([
            'buying_price' => (int) $request->input('buying_price'),
            'price_currency' => $currency,
        ]);
        if ($card->position->client) {
            $this->updatePriceForPartInOrder($car, $card->position->client->id, $card->position);
        }
        return response()->json([], 204);
    }

    public function updateSellingPrice(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $currency = $card->priceCard->price_currency ?: 'JPY';
        $card->priceCard()->update([
            'selling_price' => (int) $request->input('selling_price'),
            'price_currency' => $currency,
        ]);
        return response()->json([], 204);
    }

    public function updateSellingBuyingPrices(Request $request, Car $car): JsonResponse
    {
        if (count($request->all())) {
            foreach ($request->all() as $position) {
                $card = CarPdrPositionCard::with('priceCard')->find($position['card_id']);
                if ($card) {
                    $sellingPrice = $position['selling_price'] ? (int) $position['selling_price'] : null;
                    $card->priceCard()->update([
                        'selling_price' => $sellingPrice,
                        'buying_price' => $position['buying_price'] ? (int) $position['buying_price'] : null,
                        'price_currency' => 'JPY',
                    ]);
                    if ($position['user_id']) {
                        $this->syncPartPrice($card, $sellingPrice ?? 0, $position['user_id']);
                    }
                }
            }
        }
        return response()->json([], 204);
    }

    public function setDefaultPriceCategory(Request $request, Car $car): JsonResponse
    {
        app()->make(SetDefaultPriceCategoryAction::class)->handle($car, $request->input('category'));
        return response()->json([], 202);
    }

    public function updateComment(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $card->comments()->create([
           'comment' => trim($request->input('comment')),
           'user_id' => $request->user()->id,
        ]);
        return response()->json(['comments' => $card->comments()->with('createdBy')->get()], 201);
    }

    public function deleteComments(Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $card->comments()->delete();
        return response()->json([]);
    }

    public function updateIcDescription(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $card->update([
            'description' => strtoupper(trim($request->input('ic_description')))
        ]);
        $card->position()->update([
            'ic_description' => strtoupper(trim($request->input('ic_description')))
        ]);

        return response()->json([], 204);
    }

    public function setPartsPrice(Request $request, Car $car): JsonResponse
    {
        $partIds = $request->input('partIds', []);
        $price = $request->integer('price', []);
        if (count($partIds)) {
            CarPdrPositionCard::with('priceCard')->whereIn('id', $partIds)
                ->get()->each(function ($card) use ($price) {
                    $currency = $card->priceCard->price_currency ?: 'JPY';
                    $card->priceCard()->update([
                        'selling_price' => $price,
                        'price_currency' => $currency,
                    ]);
                });
        }
        return response()->json($partIds, 202);
    }

    public function setClient(Request $request, Car $car, CarPdrPositionCard $card): JsonResponse
    {
        $this->deletePartFromOrder($car, $card->position);
        $card->position->update([
            'user_id' => $request->input('client_id'),
        ]);
        //sync with order if any
        $this->addPartToOrder($car, $request->input('client_id'), $card->position);
        return response()->json([], 202);
    }

    public function setPartsClient(Request $request, Car $car): JsonResponse
    {
        if (count($request->all())) {
            foreach($request->all() as $position) {
                $card = CarPdrPositionCard::with('position')->find($position['card_id']);
                    $this->deletePartFromOrder($car, $card->position);
                    $card->position->update([
                        'user_id' => $position['user_id'],
                    ]);
                    //sync with order if any
                    $this->addPartToOrder($car, $position['user_id'], $card->position);
            }
        }
        return response()->json([], 202);
    }

    public function linksList(Request $request, Car $car): AnonymousResourceCollection
    {
        return LinkResource::collection($car->links()->with('createdBy')->get());
    }

    public function addLink(Request $request, Car $car): AnonymousResourceCollection
    {
        $car->links()->create([
            'url' => $request->input('url'),
            'type' => $request->input('type'),
            'created_by' => $request->user()->id,
        ]);
        return LinkResource::collection($car->links()->with('createdBy')->get());
    }

    public function deleteLink(Request $request, Car $car, Link $link): AnonymousResourceCollection
    {
        $link->update(['deleted_by' => $request->user()->id]);
        $link->delete();
        return LinkResource::collection($car->links()->with('createdBy')->get());
    }

    public function partsCommentsList(Car $car): AnonymousResourceCollection
    {
        $comments = CarPartsComment::with('user')
            ->where('car_id', $car->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return CarPartsCommentResource::collection($comments);
    }

    public function addComment(Request $request, Car $car): AnonymousResourceCollection
    {
        CarPartsComment::create([
            'comment' => $request->input('comment'),
            'user_id' => $request->user()->id,
            'car_id' => $car->id,
        ]);
        $comments = CarPartsComment::with('user')
            ->where('car_id', $car->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return CarPartsCommentResource::collection($comments);
    }
}
