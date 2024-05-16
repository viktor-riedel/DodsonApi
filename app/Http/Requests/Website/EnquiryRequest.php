<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'order' =>['required', 'min:10'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
