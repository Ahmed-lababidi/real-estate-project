<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'project' => $this->whenLoaded('project', fn() => [
                'id' => $this->project?->id,
                'name' => $this->project?->name,
                'name_en' => $this->project?->name_en,
                'slug' => $this->project?->slug,
            ]),

            'tower' => $this->whenLoaded('tower', fn() => [
                'id' => $this->tower?->id,
                'name' => $this->tower?->name,
                'name_en' => $this->tower?->name_en,
                'slug' => $this->tower?->slug,
            ]),

            'apartment' => $this->whenLoaded('apartment', fn() => [
                'id' => $this->apartment?->id,
                'name' => $this->apartment?->name,
                'name_en' => $this->apartment?->name_en,
                'slug' => $this->apartment?->slug,
                'unit_number' => $this->apartment?->unit_number,
            ]),

            'farm' => $this->whenLoaded('farm', fn() => [
                'id' => $this->farm?->id,
                'name' => $this->farm?->name,
                'name_en' => $this->farm?->name_en,
            ]),

            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'type' => $this->type,
            'message' => $this->message,
            'status' => $this->status,

            'contacted_at' => $this->contacted_at,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at,
        ];
    }
}
