<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RestorePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'guid' => ['string', 'uuid'],
            'new_password' => ['string', 'min:5'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
