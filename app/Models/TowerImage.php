<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TowerImage extends Model
{
    protected $fillable = [
        'tower_id',
        'path',
        'disk',
        'alt_text',
        'sort_order',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }
}
