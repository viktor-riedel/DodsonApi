<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemPositionPhotosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomenclature_base_item_pdr_position_id' => $this->nomenclature_base_item_pdr_position_id,
            'image_url' => '',
            'folder_name' => $this->folder_name,
            'file_name' => $this->file_name,
            'photo_url' => $this->photo_url,
            'mime' => $this->mime,
            'main_photo' => $this->main_photo,
            'is_video' => $this->is_video,
            'video_url' => $this->video_url,
        ];
    }
}
