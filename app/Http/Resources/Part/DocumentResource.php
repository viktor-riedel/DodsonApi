<?php

namespace App\Http\Resources\Part;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'document_number' => $this->car_mvr,
            'created_by' => User::withTrashed()->find($this->created_by)?->name,
            'parts_count' => $this->parts_count,
            'created' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'contr_agent_name' => $this->contr_agent_name,
        ];
    }
}
