<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tower_id' => ['required', 'exists:towers,id'],
            'apartment_orientation_id' => ['nullable', 'exists:apartment_orientations,id'],

            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:apartments,slug'],
            'unit_number' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', 'unique:apartments,code'],

            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'floor_number' => ['required', 'integer', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'rooms_number' => ['nullable', 'integer', 'min:0'],

            'area' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],

            'status' => ['nullable', 'in:available,reserved,sold'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
