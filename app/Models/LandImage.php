<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandImage extends Model
{
    protected $fillable = [
        'land_id',
        'path',
        'disk',
        'alt_text',
        'sort_order',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function land()
    {
        return $this->belongsTo(Land::class);
    }
}
