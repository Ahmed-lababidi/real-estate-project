<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'subtitle',
        'subtitle_en',
        'description',
        'description_en',
        'image',
        'project_category_id',
        'mobile_image',
        'button_text',
        'button_link',
        'target_type',
        'target_id',
        'is_active',
        'is_featured',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }
}
