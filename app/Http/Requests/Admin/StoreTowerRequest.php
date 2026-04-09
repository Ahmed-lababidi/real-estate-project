<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTowerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'tower_category_id' => ['required', 'exists:tower_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:towers,slug'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'number_of_floors' => ['nullable', 'integer', 'min:1'],
            'location_within_project' => ['nullable', 'string', 'max:255'],
            'location_within_project_en' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'model_3d' => ['nullable', 'file', 'extensions:glb,gltf,obj', 'max:51200'],
        ];
    }
}
