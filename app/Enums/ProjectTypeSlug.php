<?php

namespace App\Enums;

enum ProjectTypeSlug: string
{
    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';
    case SERVICE = 'service';
    case RESORT = 'resort';
}
