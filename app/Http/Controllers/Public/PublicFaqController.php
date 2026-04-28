<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrequentlyQuestion;
use App\Http\Resources\FaqResource;
use App\Traits\ApiResponse;


class PublicFaqController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        try {

            $Faq = FrequentlyQuestion::active()
                ->paginate($request->integer('per_page', 15));

            return $this->successResponse([
                'items' => FaqResource::collection($Faq->items()),
                'pagination' => [
                    'current_page' => $Faq->currentPage(),
                    'last_page' => $Faq->lastPage(),
                    'per_page' => $Faq->perPage(),
                    'total' => $Faq->total(),
                ],
            ], 'Frequently asked questions fetched successfully');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الأسئلة الشائعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(FrequentlyQuestion $faq)
    {
        return $this->successResponse(
            new FaqResource($faq),
            'Faq fetched successfully'
        );
    }
}
