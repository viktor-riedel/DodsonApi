<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'min:2'],
            'last_name' => ['required', 'min:2'],
            'user_email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'repeat_password' => ['required', 'min:6', 'same:password'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
