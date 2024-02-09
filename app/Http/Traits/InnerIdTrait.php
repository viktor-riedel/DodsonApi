<?php

namespace App\Http\Traits;

trait InnerIdTrait
{
    public function setInnerId(string $innerId): void
    {
        $this->inner_id = $innerId;
        $this->save();
    }

    public function generateInnerId(string $value): string
    {
        return md5($value);
    }
}
