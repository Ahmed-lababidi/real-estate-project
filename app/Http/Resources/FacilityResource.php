<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FacilityResource extends JsonResource
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

            'name' => $this->name,
            'name_en' => $this->name_en,
            'type' => $this->type,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'area' => $this->area,
            'location_within_project' => $this->location_within_project,
            'location_within_project_en' => $this->location_within_project_en,
            'is_active' => $this->is_active,

            'cover_image_url' => $this->cover_image
                ? Storage::url($this->cover_image)
                : null,

            'images' => FacilityImageResource::collection($this->whenLoaded('images')),

            'created_at' => $this->created_at,
        ];
    }
}
