<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'exists:projects,id'],
            'tower_id' => ['nullable', 'exists:towers,id'],
            'apartment_id' => ['nullable', 'exists:apartments,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],

            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],

            'type' => ['nullable', 'in:general,project_inquiry,tower_inquiry,apartment_inquiry,reservation_followup'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
