<?php

namespace App\Http\Resources\CRM\TradeMe;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeMeAuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'access_token' => $this->access_token ?? null,
            'access_token_secret' => $this->access_token_secret ?? null,
            'oauth_token' => $this->oauth_token ?? null,
            'oauth_token_secret' => $this->oauth_token_secret ?? null,
            'oauth_verifier' => $this->oauth_verifier ?? null,
            'redirect_url' => $this->redirect_url ?? null,
            'authorized_by' => $this?->user?->name ?? null,
            'authorized' => $this->authorized ?? false,
            'environment' => $this->environment ?? config('trademe.environment'),
            'authorized_at' => $this->created_at ?? null,
        ];
    }
}
