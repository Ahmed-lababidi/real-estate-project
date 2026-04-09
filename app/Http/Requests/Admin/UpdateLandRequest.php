<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $landId = $this->route('land')?->id ?? $this->route('land');

        return [
            'project_id' => ['nullable', 'exists:projects,id'],

            'name' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],

            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'location_within_project' => ['nullable', 'string', 'max:255'],
            'location_within_project_en' => ['nullable', 'string', 'max:255'],

            'type' => ['nullable', 'in:agricultural,residential,commercial,other'],

            'area' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],

            'status' => ['nullable', 'in:available,reserved,sold'],
            'is_active' => ['nullable', 'boolean'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'deleted_image_ids' => ['nullable', 'array'],
            'deleted_image_ids.*' => ['integer', 'exists:land_images,id'],
        ];
    }
}
