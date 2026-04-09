<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectDetailesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

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
            'code' => $this->code,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'location_text' => $this->location_text,
            'location_text_en' => $this->location_text_en,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'starting_price' => $this->starting_price,
            'delivery_date' => $this->delivery_date?->format('Y-m-d'),
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,

            'cover_image_url' => $this->cover_image
                ? Storage::url($this->cover_image)
                : null,

            'images' => ProjectImageResource::collection($this->whenLoaded('images')),

            'towers_count' => $this->whenCounted('towers'),
            'lands_count' => $this->whenCounted('lands'),
            'farms_count' => $this->whenCounted('farms'),

            'towers' => ProjectTowerMiniResource::collection($this->whenLoaded('towers')),
            'lands' => ProjectLandMiniResource::collection($this->whenLoaded('lands')),
            'farms' => ProjectFarmMiniResource::collection($this->whenLoaded('farms')),

            'created_at' => $this->created_at,
        ];
    }
}
