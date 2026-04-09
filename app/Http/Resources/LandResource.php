<?php

namespace App\Http\Resources;

use App\Enums\LandStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LandResource extends JsonResource
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
            'description' => $this->description,
            'description_en' => $this->description_en,

            'location_within_project' => $this->location_within_project,
            'location_within_project_en' => $this->location_within_project_en,

            'type' => $this->type,

            'area' => $this->area,
            'price' => $this->price,

            'status' => $this->status,
            'status_label' => match ($this->status) {
                LandStatus::AVAILABLE => 'متاحة',
                LandStatus::RESERVED => 'محجوزة',
                LandStatus::SOLD => 'مباعة',
                default => $this->status,
            },

            'is_active' => $this->is_active,

            'cover_image_url' => $this->cover_image
                ? Storage::url($this->cover_image)
                : null,

            'images' => LandImageResource::collection($this->whenLoaded('images')),

            'has_active_reservation' => $this->when(
                $this->relationLoaded('reservations'),
                fn() => $this->reservations->contains(fn($r) => $r->status === 'pending' && $r->expires_at?->isFuture())
            ),

            'created_at' => $this->created_at,
        ];
    }
}
