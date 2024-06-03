<?php

namespace App\Http\Requests\CRM\Leads;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'lead_name' => ['required', 'string', 'min:3'],
            'from' => ['required', 'string', 'min:3'],
            'lead_type' => ['required'],
            'lead_status' => ['required'],
            'lead_description' => ['required', 'string', 'min:5'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
