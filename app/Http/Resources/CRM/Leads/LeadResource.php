<?php

namespace App\Http\Resources\CRM\Leads;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'lead_name' => $this->lead_name,
            'lead_type' => $this->lead_type_string,
            'lead_status' => $this->lead_status_string,
            'lead_description' => $this->lead_description,
            'accepted_by' => $this->whenLoaded('acceptedBy',
                $this->accepted_by ? $this->acceptedBy->name : null,
                null)
        ];
    }
}
