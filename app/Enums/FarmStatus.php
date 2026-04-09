<?php

namespace App\Enums;

enum FarmStatus : string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case SOLD = 'sold';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'متاحة',
            self::RESERVED => 'محجوزة',
            self::SOLD => 'مباعة',
        };
    }
}
