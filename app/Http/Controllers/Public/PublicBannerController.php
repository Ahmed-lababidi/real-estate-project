<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PublicBannerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $items = Banner::query()
            ->with(['projectCategory'])
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('is_featured'), fn ($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->filled('project_category_id'), fn ($q) => $q->where('project_category_id', $request->integer('project_category_id')))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => BannerResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Banners fetched successfully');
    }

    public function show(Banner $banner)
    {
        return $this->successResponse(
            new BannerResource($banner),
            'Banner fetched successfully'
        );
    }

}
