<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'title_en' => $this->title_en,
            'subtitle' => $this->subtitle,
            'subtitle_en' => $this->subtitle_en,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'project_category_id' => $this->project_category_id,

            'image_url' => $this->image ? Storage::url($this->image) : null,
            'mobile_image_url' => $this->mobile_image ? Storage::url($this->mobile_image) : null,

            'target_type' => $this->target_type,

            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,

            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ];
    }
}
