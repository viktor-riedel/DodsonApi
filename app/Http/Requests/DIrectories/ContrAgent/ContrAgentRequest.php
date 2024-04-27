<?php

namespace App\Http\Requests\DIrectories\ContrAgent;

use Illuminate\Foundation\Http\FormRequest;

class ContrAgentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
