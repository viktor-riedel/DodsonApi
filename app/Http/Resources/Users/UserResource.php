<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->getRoleNames()->first(),
            'last_login' => $this->last_login_at ? $this->last_login_at->format('d/m/Y H:i') : null,
            'inactive' => $this->trashed(),
            'card' => $this->whenLoaded('userCard', new UserCardResource($this->userCard), null),
            'available_roles' => Role::get(),
        ];
    }
}
