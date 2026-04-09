<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],

            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],

            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'rooms_number' => ['nullable', 'integer', 'min:0'],

            'location_within_project' => ['nullable', 'string', 'max:255'],
            'location_within_project_en' => ['nullable', 'string', 'max:255'],

            'area' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],

            'status' => ['nullable', 'in:available,reserved,sold'],
            'is_active' => ['nullable', 'boolean'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
