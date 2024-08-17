<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'mobile_phone' => $this->mobile_phone,
            'landline_phone' => $this->landline_phone,
            'company_name' => $this->company_name,
            'trading_name' => $this->trading_name,
            'address' => $this->address,
            'country' => $this->country,
            'comment' => $this->comment,
            'wholesaler' => $this->wholesaler,
            'parts_sale_user' => $this->parts_sale_user,
        ];
    }
}
