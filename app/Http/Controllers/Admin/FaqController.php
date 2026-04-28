<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use Illuminate\Http\Request;
use App\Models\FrequentlyQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Http\Resources\FaqResource;
use App\Traits\ApiResponse;

class FaqController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFaqRequest $request)
    {
        $faq = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $faq = FrequentlyQuestion::create($data);
            return $faq;
        });

        return $this->successResponse(
            new FaqResource($faq),
            'Faq created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(FrequentlyQuestion $faq)
    {
        return $this->successResponse(
            new FaqResource($faq),
            'Faq fetched successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaqRequest $request, FrequentlyQuestion $faq)
    {
        $updatedFaq = DB::transaction(function () use ($request, $faq) {
            $data = $request->validated();
            $faq->update($data);
            return $faq->fresh();
        });

        return $this->successResponse(
            new FaqResource($updatedFaq),
            'Faq updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FrequentlyQuestion $faq)
    {
        try {
            $faq->delete();

            return $this->successResponse(
                null,
                'Faq deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ في حذف السؤال الشائع: ' . $e->getMessage(), 500, $e->getTraceAsString());
        }
    }
}
