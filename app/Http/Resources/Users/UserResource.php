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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'roles' => $this->getRoleNames()->first(),
            'balance' => number_format($this->balance_sum_closing_balance),
            'last_login' => $this->last_login_at ? $this->last_login_at->format('d/m/Y H:i') : null,
            'inactive' => $this->trashed(),
            'card' => $this->whenLoaded('userCard', new UserCardResource($this->userCard), null),
            'available_roles' => Role::get(),
            'country_name' => findCountryByCode($this->country_code ?? ''),
            'country_code' => $this->country_code,
            'is_api_user' => (bool) $this->is_api_user,
            'parts_sale' => $this->userCard?->parts_sale_user,
            'wholesale' => $this->userCard?->wholesaler ?? false,
        ];
    }
}
