<?php

namespace App\Http\Controllers\EditCar;

use App\Http\Controllers\Controller;
use App\Http\Traits\CarPdrTrait;
use App\Models\Car;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class EditCarController extends Controller
{
    use CarPdrTrait;

    public function edit(Car $car)
    {
        $car->load('images', 'carAttributes', 'modification', 'createdBy');
        $parts = $this->buildPdrTreeWithoutEmpty($car);
        $partsList = $this->getPartsList($car);
        $car->unsetRelation('pdrs');

        return response()->json([
           'car_info' => $car,
           'parts_tree' => $parts,
           'parts_list' => $partsList,
           'car_statuses' => Car::CAR_STATUSES,
        ]);
    }

    public function uploadCarPhoto(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        if ($request->file('uploadCarPhotos')) {
            $storage = \Storage::disk('s3');
            foreach ($request->file('uploadCarPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'cars/new/'.\Str::random();
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
}
