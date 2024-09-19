<?php

namespace App\Http\Controllers\Nomenclature;

use App\Http\Controllers\Controller;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureBaseItemPdrPositionPhoto;
use Illuminate\Http\Request;

class FileUploadsController extends Controller
{
    public function addPhotoToBaseItemPosition(Request $request, NomenclatureBaseItemPdrPosition $baseItemPdrPosition): \Illuminate\Http\JsonResponse
    {
        $result = false;
        if ($request->file('uploadBaseItemPositionFiles')) {
            $storage = \Storage::disk('s3');
            foreach($request->file('uploadBaseItemPositionFiles') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'nomenclature/pdr/items/position/' . $baseItemPdrPosition->id;
                $mime = $file->getMimeType();
                $fileExtension = '.' . $file->clientExtension();
                $savePath = $folderName . '/' . $fileName . $fileExtension;
                $result = $storage->put($savePath, $file->getContent(), 'public');
                if ($result) {
                    $baseItemPdrPosition->photos()->create([
                        'folder_name' => $folderName,
                        'file_name' => $fileName . '.' . $file->clientExtension(),
                        'original_file_name' => $originFileName . '.' . $file->clientExtension(),
                        'photo_url' => $storage->url($savePath),
                        'mime' => $mime,
                    ]);
                }
            }
            $baseItemPdrPosition->refresh();
            return response()->json([
                'uploaded' => $result,
                'photos' => $baseItemPdrPosition->photos,
            ], 201);
        }
        return response()->json([], 401);
    }

    public function deleteBaseItemPosition(NomenclatureBaseItemPdrPositionPhoto $baseItemPdrPositionPhoto): \Illuminate\Http\JsonResponse
    {
        $baseItemPdrPositionPhoto->delete();
        return response()->json([], 202);
    }
}
