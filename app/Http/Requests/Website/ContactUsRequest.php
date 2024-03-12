<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'min:2'],
            'last_name' => ['required', 'min:2'],
            'user_email' => ['required', 'email'],
            'message' => ['required', 'min:10'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
