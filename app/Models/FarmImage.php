<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmImage extends Model
{
    protected $fillable = [
        'farm_id',
        'path',
        'disk',
        'alt_text',
        'sort_order',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
