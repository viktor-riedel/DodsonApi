<?php

namespace App\Http\Controllers\CreateCar;

use App\Actions\CreateCar\CreateNewCarAction;
use App\Http\Controllers\Controller;
use App\Http\Traits\BaseCarTrait;
use Illuminate\Http\Request;

class CreateCarController extends Controller
{
    use BaseCarTrait;

    public function uploadPhoto(Request $request): \Illuminate\Http\JsonResponse
    {
        $uploaded = [];
        if ($request->file('uploadNewCarPhotos')) {
            $storage = \Storage::disk('s3');
            foreach($request->file('uploadNewCarPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'cars/new/' . \Str::random();
                $mime = $file->getMimeType();
                $fileExtension = '.' . $file->clientExtension();
                $savePath = $folderName . '/' . $fileName . $fileExtension;
                $size = $file->getSize();
                $result = $storage->put($savePath, $file->getContent(), 'public');
                $uploaded[] = [
                    'file_name' => $fileName,
                    'original_file_name' => $originFileName,
                    'folder_name' => $folderName,
                    'size' => $size,
                    'mime' => $mime,
                    'ext' => $fileExtension,
                    'name' => $fileName . $fileExtension,
                    'uploaded_url' => $storage->url($savePath),
                ];
            }
        }
        return response()->json(['files' => $uploaded], 201);
    }

    public function createNewCar(Request $request): \Illuminate\Http\JsonResponse
    {
        $carId = app()->make(CreateNewCarAction::class)->handle($request);
        return response()->json(['success' => $carId]);
    }
}
