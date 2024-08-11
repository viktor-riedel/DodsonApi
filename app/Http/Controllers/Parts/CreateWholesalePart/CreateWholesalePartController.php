<?php

namespace App\Http\Controllers\Parts\CreateWholesalePart;

use App\Actions\Parts\CreateWholesalePartsAction;
use App\Http\Controllers\Controller;
use App\Http\Traits\BaseCarTrait;
use App\Models\CarPdrPosition;
use App\Models\ContrAgent;
use App\Models\MediaFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateWholesalePartController extends Controller
{
    use BaseCarTrait;

    public function getAgents(): JsonResponse
    {
        $agents = ContrAgent::orderBy('name')->get();
        return response()->json($agents);
    }

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

    public function uploadPartImages(Request $request, CarPdrPosition $part): JsonResponse
    {
        if ($request->file('uploadPartPhotos')) {
            $storage = \Storage::disk('s3');
            foreach ($request->file('uploadPartPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'parts/' . $part->id;
                $mime = $file->getMimeType();
                $fileExtension = '.'.$file->clientExtension();
                $savePath = $folderName.'/'.$fileName.$fileExtension;
                $size = $file->getSize();
                $storage->put($savePath, $file->getContent(), 'public');
                $part->images()->create([
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
        return response()->json($part->images);
    }

    public function deletePartPhoto(Request $request, CarPdrPosition $part, MediaFile $photo): JsonResponse
    {
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($part->images);

    }
}
