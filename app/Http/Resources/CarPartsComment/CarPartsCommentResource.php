<?php

namespace App\Http\Resources\CarPartsComment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarPartsCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->name,
            'created' => $this->created_at->format('d/m/Y H:i'),
            'comment' => $this->comment,
        ];
    }
}
