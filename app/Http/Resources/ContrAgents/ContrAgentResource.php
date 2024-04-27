<?php

namespace App\Http\Resources\ContrAgents;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContrAgentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'person_name' => $this->person_name,
            'country' => $this->country,
            'email' => $this->email,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'address' => $this->address,
        ];
    }
}
