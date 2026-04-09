<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],

            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],

            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'location_within_project' => ['nullable', 'string', 'max:255'],
            'location_within_project_en' => ['nullable', 'string', 'max:255'],

            'area' => ['required', 'numeric', 'min:1'],

            'is_active' => ['nullable', 'boolean'],

            'type' => ['nullable', 'in:garden,pool,farm,court,land,hospital,school,office,other'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
