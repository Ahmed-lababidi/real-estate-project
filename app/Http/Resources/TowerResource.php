<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TowerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project?->id,
                    'name' => $this->project?->name,
                    'name_en' => $this->project?->name_en,
                    'slug' => $this->project?->slug,
                ];
            }),

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category?->id,
                    'name' => $this->category?->name,
                    'name_en' => $this->category?->name_en,
                    'slug' => $this->category?->slug,
                ];
            }),

            'name' => $this->name,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'number_of_floors' => $this->number_of_floors,
            'location_within_project' => $this->location_within_project,
            'location_within_project_en' => $this->location_within_project_en,
            'is_active' => $this->is_active,

            'cover_image_url' => $this->cover_image
                ? Storage::url($this->cover_image)
                : null,

            'model_3d_url' => $this->model_3d_path ? Storage::url($this->model_3d_path) : null,

            'images' => TowerImageResource::collection($this->whenLoaded('images')),

            'apartments_count' => $this->whenCounted('apartments'),
            'created_at' => $this->created_at,
        ];
    }
}
