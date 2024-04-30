<?php

namespace App\Http\Controllers\EditCar;

use App\Actions\CreateCar\AddListPartsAction;
use App\Actions\CreateCar\AddMiscPartsAction;
use App\Actions\CreateCar\AddPartsFromModificationListAction;
use App\Exports\Excel\CreatedCarPartsExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\CarPdrTrait;
use App\Models\Car;
use App\Models\CarPdrPositionCard;
use App\Models\CarPdrPositionCardAttribute;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EditCarController extends Controller
{
    use CarPdrTrait;

    public function edit(Car $car): \Illuminate\Http\JsonResponse
    {
        $car->load('images', 'carAttributes', 'modification', 'modifications', 'createdBy');
        $parts = $this->buildPdrTreeWithoutEmpty($car, false);
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');

        return response()->json([
           'car_info' => $car,
           'parts_tree' => $parts,
           'parts_list' => $partsList,
           'car_statuses' => Car::getStatusesJson(),
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

        return response()->json([], 202);
    }

    public function updateCarStatus(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        if ((int) $request->input('car_status') >= 0) {
            $car->statusLogs()->create([
                'old_status' => $car->car_status,
                'new_status' => (int) $request->input('car_status'),
                'user_id' => $request->user()->id,
            ]);
            $car->update(['car_status' => (int) $request->input('car_status')]);
            return response()->json([], 202);
        }
        return response()->json(['error' => 'status not found'], 402);
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


    public function uploadPartPhoto(Request $request, Car $car, CarPdrPositionCard $card)
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

    public function updateAttributes(Request $request, Car $car, CarPdrPositionCardAttribute $card): \Illuminate\Http\JsonResponse
    {
        $card->update($request->except('car_pdr_position_id', 'id'));
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
        $card->position()->update(['ic_number' => strtoupper(trim($request->input('ic_number')))]);
        return response()->json([], 204);
    }

    public function updatePriceCurrency(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->priceCard()->update([
            'price_currency' => strtoupper(trim($request->input('price_currency')))
        ]);
        return response()->json([], 204);
    }

    public function updateApproxPrice(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $currency = $card->priceCard->price_currency ?: 'JPY';
        $card->priceCard()->update([
            'approximate_price' => (int) $request->input('approx_price'),
            'price_currency' => $currency,
        ]);
        return response()->json([], 204);
    }

    public function updateRealPrice(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $currency = $card->priceCard->price_currency ?: 'JPY';
        $card->priceCard()->update([
            'real_price' => (int) $request->input('real_price'),
            'price_currency' => $currency,
        ]);
        return response()->json([], 204);
    }

    public function updateComment(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->update(['comment' => trim($request->input('comment'))]);
        return response()->json([], 204);
    }

    public function updateIcDescription(Request $request, Car $car, CarPdrPositionCard $card): \Illuminate\Http\JsonResponse
    {
        $card->update(['description' => strtoupper(trim($request->input('ic_description')))]);
        $card->position()->update(['ic_description' => strtoupper(trim($request->input('ic_description')))]);
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
                        'real_price' => $price,
                        'price_currency' => $currency,
                    ]);
                });
        }
        return response()->json($partIds, 202);
    }
}
