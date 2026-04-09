<?php

namespace App\Http\Resources;

use App\Enums\ApartmentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'tower' => $this->whenLoaded('tower', function () {
                return [
                    'id' => $this->tower?->id,
                    'name' => $this->tower?->name,
                    'name_en' => $this->tower?->name_en,
                    'slug' => $this->tower?->slug,
                    'project_id' => $this->tower?->project_id,
                ];
            }),

            'orientation' => $this->whenLoaded('orientation', function () {
                return [
                    'id' => $this->orientation?->id,
                    'name' => $this->orientation?->name,
                    'name_en' => $this->orientation?->name_en,
                    'slug' => $this->orientation?->slug,
                ];
            }),

            'name' => $this->name,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'unit_number' => $this->unit_number,
            'code' => $this->code,
            'description' => $this->description,
            'description_en' => $this->description_en,

            'floor_number' => $this->floor_number,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'rooms_number' => $this->rooms_number,

            'area' => $this->area,
            'price' => $this->price,

            'status' => $this->status,
            'status_label' => match ($this->status) {
                ApartmentStatus::AVAILABLE => 'متاحة',
                ApartmentStatus::RESERVED => 'محجوزة',
                ApartmentStatus::SOLD => 'مباعة',
                default => $this->status,
            },

            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,

            'cover_image_url' => $this->cover_image
                ? Storage::url($this->cover_image)
                : null,

            'images' => ApartmentImageResource::collection($this->whenLoaded('images')),

            'has_active_reservation' => $this->when(
                $this->relationLoaded('reservations'),
                fn () => $this->reservations->contains(fn ($r) => $r->status === 'pending' && $r->expires_at?->isFuture())
            ),

            'created_at' => $this->created_at,
        ];
    }
}
