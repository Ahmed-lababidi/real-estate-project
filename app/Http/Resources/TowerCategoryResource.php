<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TowerCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'towers_count' => $this->whenCounted('towers'),
            'created_at' => $this->created_at,
        ];
    }
}
