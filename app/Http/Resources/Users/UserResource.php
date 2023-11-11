<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'last_login' => null,
            'user_information' => $this->userInformation ? new UserInformationResource($this->userInformation) : [],
            'roles' => [],
            'permissions' => [],
            'created' => $this->created_at->format('d/m/Y'),
            'inactive' => $this->deleted_at !== null,
        ];
    }
}
