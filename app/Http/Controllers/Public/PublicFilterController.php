<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentOrientationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TowerResource;
use App\Models\Apartment;
use App\Models\ApartmentOrientation;
use App\Models\Project;
use App\Models\Tower;
use App\Traits\ApiResponse;

class PublicFilterController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $projects = Project::query()
            ->where('is_active', true)
            ->with(['category', 'images'])
            ->get();

        $towers = Tower::query()
            ->where('is_active', true)
            ->with(['project', 'category', 'images'])
            ->get();

        $orientations = ApartmentOrientation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse([
            'projects' => ProjectResource::collection($projects),
            'towers' => TowerResource::collection($towers),
            'orientations' => ApartmentOrientationResource::collection($orientations),

            'meta' => [
                'price_min' => Apartment::min('price'),
                'price_max' => Apartment::max('price'),
                'area_min' => Apartment::min('area'),
                'area_max' => Apartment::max('area'),
                'max_floor' => Apartment::max('floor_number'),
                'max_bedrooms' => Apartment::max('bedrooms'),
                'max_bathrooms' => Apartment::max('bathrooms'),
            ],
        ], 'Filters fetched successfully');
    }
}
