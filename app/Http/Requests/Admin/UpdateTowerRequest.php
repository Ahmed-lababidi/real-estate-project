<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTowerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $towerId = $this->route('tower')?->id ?? $this->route('tower');

        return [
            'project_id' => ['nullable', 'exists:projects,id'],
            'tower_category_id' => ['nullable', 'exists:tower_categories,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('towers', 'slug')->ignore($towerId)],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'number_of_floors' => ['nullable', 'integer', 'min:1'],
            'location_within_project' => ['nullable', 'string', 'max:255'],
            'location_within_project_en' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'deleted_image_ids' => ['nullable', 'array'],
            'deleted_image_ids.*' => ['integer', 'exists:tower_images,id'],
        ];
    }
}
