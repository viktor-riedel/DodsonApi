<?php

namespace App\Http\Controllers\EditCar;

use App\Http\Controllers\Controller;
use App\Http\Traits\CarPdrTrait;
use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\CarPdrPositionCard;
use App\Models\CarPdrPositionCardAttribute;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class EditCarController extends Controller
{
    use CarPdrTrait;

    public function edit(Car $car): \Illuminate\Http\JsonResponse
    {
        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $parts = $this->buildPdrTreeWithoutEmpty($car);
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');

        return response()->json([
           'car_info' => $car,
           'parts_tree' => $parts,
           'parts_list' => $partsList,
           'car_statuses' => Car::getStatusesJson(),
        ]);
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
        $photo = $car->images->where('id', $photo->id)->first();
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($car->images);
    }

    public function updateCar(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        $car->carAttributes()->update([
            'color' => strtoupper(trim($request->input('color'))),
            'mileage' => $request->integer('mileage'),
            'engine' => strtoupper(trim($request->input('engine'))),
            'chassis' => strtoupper(trim($request->input('chassis'))),
            'year' => $request->integer('chassis'),
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
}
