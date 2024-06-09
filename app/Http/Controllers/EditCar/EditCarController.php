<?php

namespace App\Http\Controllers\EditCar;

use App\Actions\CreateCar\AddListPartsAction;
use App\Actions\CreateCar\AddMiscPartsAction;
use App\Actions\CreateCar\AddPartsFromModificationListAction;
use App\Exports\Excel\CreatedCarPartsExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\LinkResource;
use App\Http\Traits\CarPdrTrait;
use App\Jobs\Sync\SendDoneCarJob;
use App\Models\Car;
use App\Models\CarPdrPositionCard;
use App\Models\CarPdrPositionCardAttribute;
use App\Models\CarPdrPositionCardPrice;
use App\Models\Link;
use App\Models\MediaFile;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class EditCarController extends Controller
{
    use CarPdrTrait;

    public function edit(Car $car): \Illuminate\Http\JsonResponse
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
        $parts = $this->buildPdrTreeWithoutEmpty($car, false);
        $partsList = $this->getPartsList($car);
        $clients = User::withoutRole('ADMIN')
            ->where('is_api_user', 0)
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
           'parts_list' => $partsList,
           'car_statuses' => Car::getStatusesJson(),
           'clients' => $clients,
        ]);
    }

    public function delete(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        $car->update(['deleted_by' => $request->user()->id]);
        $car->delete();
        return response()->json([], 202);
    }

    public function uploadCarPhoto(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        if ($request->file('uploadCarPhotos')) {
            $storage = \Storage::disk('s3');
            foreach ($request->file('uploadCarPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'cars/' . $car->id . '/photos';
                $mime = $file->getMimeType();
                $fileExtension = '.'.$file->clientExtension();
                $savePath = $folderName.'/'.$fileName.$fileExtension;
                $size = $file->getSize();
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
        return response()->json($car->images);
    }

    public function deleteCarPhoto(Request $request, Car $car, MediaFile $photo): \Illuminate\Http\JsonResponse
    {
        $photo = $car->images()->where('id', $photo->id)->first();
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($car->images);
    }

    public function updateCar(Request $request, Car $car): \Illuminate\Http\JsonResponse
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
        ]);

        return response()->json([], 202);
    }

    public function updateCarStatus(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        $car->load('positions', 'positions.card', 'positions.card.priceCard');
        $sum = $car->positions->sum('card.priceCard.selling_price');
        $status = (int) $request->input('car_status');
        if (($status === 3 || $status === 4) && !$car->car_mvr) {
            return response()->json(['error' => 'MVR not set'], 403);
        }
        $notAllIc = $car->positions->filter(function ($position) {
           return $position->ic_number === null || $position->ic_number === '';
        });

        if ($status === 2 && $notAllIc->count() > 0) {
            return response()->json(['error' => 'Not all IC set'], 403);
        }
        if ($status === 2 && $sum === 0) {
            return response()->json(['error' => 'Spare parts sum is 0'], 403);
        }
        $car->statusLogs()->create([
            'old_status' => $car->car_status,
            'new_status' => (int) $request->input('car_status'),
            'user_id' => $request->user()->id,
        ]);

        $car->update(['car_status' => (int) $request->input('car_status')]);
        return response()->json([], 202);
    }

    public function syncCar(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        if ($car->car_status === 2) {
            SendDoneCarJob::dispatch($car, $request->user());
            return response()->json([], 202);
        }
        return response()->json(['error' => 'Car status is not DONE'], 403);
    }

    public function generateDismantlingDocument(Request $request, Car $car): \Illuminate\Http\JsonResponse
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

    public function deletePart(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->update(['deleted_by' => $request->user()->id]);
        $card->images()->update(['deleted_by' => $request->user()->id]);
        $card->position()->update(['deleted_by' => $request->user()->id]);

        $card->position()->delete();
        $card->delete();

        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');
        return response()->json($partsList);
    }


    public function uploadPartPhoto(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
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

    public function deletePartPhoto(Request $request, Car $car, CarPdrPositionCard $card, MediaFile $photo): \Illuminate\Http\JsonResponse
    {
        $photo = $card->images()->where('id', $photo->id)->first();
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($card->images);
    }

    public function updateAttributes(Request $request, Car $car, CarPdrPositionCardAttribute $card): \Illuminate\Http\JsonResponse
    {
        $card->update($request->except('car_pdr_position_id', 'id'));
        return response()->json([], 202);
    }

    public function updatePriceCard(Request $request, Car $car, CarPdrPositionCardPrice $card): \Illuminate\Http\JsonResponse
    {
        $card->update($request->except('car_pdr_position_card_id', 'id'));
        return response()->json([], 202);
    }

    public function addMiscParts(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        app()->make(AddMiscPartsAction::class)->handle($car, $request->user()->id, $request->all());
        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');
        return response()->json($partsList);
    }

    public function addListParts(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        dd(1);
        app()->make(AddListPartsAction::class)->handle($car, $request->user()->id, $request->all());
        return response()->json([], 201);
    }

    public function parts(Car $car): \Illuminate\Http\JsonResponse
    {
        $pdr = $this->buildDefaultPdrTreeByCar($car);
        return response()->json($pdr);
    }

    public function addModListParts(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        app()->make(AddPartsFromModificationListAction::class)->handle($car, $request->all(), $request->user()->id);
        return response()->json([], 201);
    }

    public function exportPartsListToExcel(Car $car): \Illuminate\Http\JsonResponse
    {
        $filename = 'exports/parts/'. $car->id . '/' .$car->make . '_' .
            $car->model . '_' . $car->generation . '_' .
            $car->created_at->toDateTimeString() . '.xlsx';
        $partsList = $this->getPartsList($car);
        Excel::store(new CreatedCarPartsExcelExport($car, $partsList), $filename, 's3', null, ['visibility' => 'public']);
        $url = \Storage::disk('s3')->url($filename);
        return response()->json(['link' => $url]);
    }

    public function updateICNumber(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->update(['ic_number' => strtoupper(trim($request->input('ic_number')))]);
        $baseCard = NomenclatureBaseItemPdrCard::where('ic_number', strtoupper(trim($request->input('ic_number'))))
            ->where('description', $card->description)
            ->first();

        $card->update([
            'parent_inner_id' => $baseCard ? $baseCard->inner_id : $card->parent_inner_id,
        ]);

        $card->position()->update([
            'ic_number' => strtoupper(trim($request->input('ic_number')))
        ]);

        $card->priceCard()->update([
            'price_nz_wholesale' => $baseCard?->price_nz_wholesale,
            'price_nz_retail' => $baseCard?->price_nz_retail,
            'price_ru_wholesale' => $baseCard?->price_ru_wholesale,
            'price_ru_retail' => $baseCard?->price_ru_retail,
            'price_jp_minimum_buy' => $baseCard?->price_jp_maximum_buy,
            'price_jp_maximum_buy' => $baseCard?->price_jp_minimum_buy,
            'minimum_threshold_nz_retail' => $baseCard?->minimum_threshold_nz_retail,
            'minimum_threshold_nz_wholesale' => $baseCard?->minimum_threshold_nz_wholesale,
            'minimum_threshold_ru_retail' => $baseCard?->minimum_threshold_ru_retail,
            'minimum_threshold_ru_wholesale' => $baseCard?->minimum_threshold_ru_wholesale,
            'minimum_threshold_jp_retail' => $baseCard?->minimum_threshold_jp_retail,
            'minimum_threshold_jp_wholesale' => $baseCard?->minimum_threshold_jp_wholesale,
            'minimum_threshold_mng_retail' => $baseCard?->minimum_threshold_mng_retail,
            'minimum_threshold_mng_wholesale' => $baseCard?->minimum_threshold_mng_wholesale,
            'delivery_price_nz' => $baseCard?->delivery_price_nz,
            'delivery_price_ru' => $baseCard?->delivery_price_ru,
            'pinnacle_price' => $baseCard?->pinnacle_price,
            'price_currency' => 'JPY',
            'price_mng_wholesale' => $baseCard?->price_mng_wholesale,
            'price_mng_retail' => $baseCard?->price_mng_retail,
            'price_jp_retail' => $baseCard?->price_jp_retail,
            'price_jp_wholesale' => $baseCard?->price_jp_wholesale,
            'nz_team_price' => $baseCard?->nz_team_price,
            'nz_team_needs' => $baseCard?->nz_team_needs,
            'nz_needs' => $baseCard?->nz_needs,
            'ru_needs' => $baseCard?->ru_needs,
            'jp_needs' => $baseCard?->jp_needs,
            'mng_needs' => $baseCard?->mng_needs,
            'needs' => $baseCard?->needs,
        ]);

        $clientCountryCode = $card->position->client?->country_code;
        $isWholeSeller = $card->position->client ? $card->position->client->userCard->wholesaler : false;

        //update selling and buying prices
        if ($clientCountryCode) {
            switch ($clientCountryCode) {
                case 'RU':
                    $card->priceCard()->update([
                        'buying_price' => $isWholeSeller ? $baseCard?->price_ru_wholesale : $baseCard?->price_ru_retail,
                        'selling_price' => 0,
                        //'price_currency' => 'RUB',
                    ]);
                    break;
                case 'NZ':
                    $card->priceCard()->update([
                        'buying_price' => $isWholeSeller ? $baseCard?->price_nz_wholesale : $baseCard?->price_nz_retail,
                        'selling_price' => 0,
                        //'price_currency' => 'NZD',
                    ]);
                    break;
                case 'MN':
                    $card->priceCard()->update([
                        'buying_price' => $isWholeSeller ? $baseCard?->price_mng_wholesale : $baseCard?->price_mng_retail,
                        'selling_price' => 0,
                        //'price_currency' => 'MNT',
                    ]);
                    break;
                case 'JP':
                    $card->priceCard()->update([
                        'buying_price' => $isWholeSeller ? $baseCard?->price_jp_wholesale : $baseCard?->price_jp_retail,
                        'selling_price' => 0,
                        //'price_currency' => 'JPY',
                    ]);
                    break;
                default:
                    break;
            }
        }

        $card->refresh();
        return response()->json([
            'price_card' => $card->priceCard,
            'card' => $card,
        ], 202);
    }

    public function updatePriceCurrency(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->priceCard()->update([
            'price_currency' => strtoupper(trim($request->input('price_currency')))
        ]);
        return response()->json([], 204);
    }

    public function updateBuyingPrice(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $currency = $card->priceCard->price_currency ?: 'JPY';
        $card->priceCard()->update([
            'buying_price' => (int) $request->input('buying_price'),
            'price_currency' => $currency,
        ]);
        return response()->json([], 204);
    }

    public function updateSellingPrice(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $currency = $card->priceCard->price_currency ?: 'JPY';
        $card->priceCard()->update([
            'selling_price' => (int) $request->input('selling_price'),
            'price_currency' => $currency,
        ]);
        return response()->json([], 204);
    }

    public function updateComment(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->comments()->create([
           'comment' => trim($request->input('comment')),
           'user_id' => $request->user()->id,
        ]);
        return response()->json(['comments' => $card->comments()->with('createdBy')->get()], 201);
    }

    public function updateIcDescription(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->update([
            'description' => strtoupper(trim($request->input('ic_description')))
        ]);
        $card->position()->update([
            'ic_description' => strtoupper(trim($request->input('ic_description')))
        ]);

        return response()->json([], 204);
    }

    public function setPartsPrice(Request $request, Car $car): \Illuminate\Http\JsonResponse
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

    public function setClient(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->position->update([
            'user_id' => $request->input('client_id'),
        ]);
        return response()->json([], 204);
    }

    public function linksList(Request $request, Car $car): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return LinkResource::collection($car->links()->with('createdBy')->get());
    }

    public function addLink(Request $request, Car $car): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $car->links()->create([
            'url' => $request->input('url'),
            'type' => $request->input('type'),
            'created_by' => $request->user()->id,
        ]);
        return LinkResource::collection($car->links()->with('createdBy')->get());
    }

    public function deleteLink(Request $request, Car $car, Link $link): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $link->update(['deleted_by' => $request->user()->id]);
        $link->delete();
        return LinkResource::collection($car->links()->with('createdBy')->get());
    }
}
