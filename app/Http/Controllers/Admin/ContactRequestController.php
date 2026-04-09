<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactRequestStatusRequest;
use App\Http\Resources\ContactRequestResource;
use App\Models\ContactRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $items = ContactRequest::query()
            ->with(['project', 'tower', 'apartment'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('phone'), fn ($q) => $q->where('phone', 'like', "%{$request->phone}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ContactRequestResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Contact requests fetched successfully');
    }

    public function show(ContactRequest $lead)
    {
        $lead->load(['project', 'tower', 'apartment']);

        return $this->successResponse(
            new ContactRequestResource($lead),
            'Contact request fetched successfully'
        );
    }

    public function updateStatus(UpdateContactRequestStatusRequest $request, ContactRequest $lead)
    {
        $status = $request->validated()['status'];

        $lead->update([
            'status' => $status,
            'contacted_at' => $status === 'contacted' ? now() : $lead->contacted_at,
            'closed_at' => $status === 'closed' ? now() : $lead->closed_at,
        ]);

        return $this->successResponse(
            new ContactRequestResource($lead->fresh(['project', 'tower', 'apartment'])),
            'Contact request updated successfully'
        );
    }

    public function destroy(ContactRequest $lead)
    {
        $lead->delete();

        return $this->successResponse(null, 'Contact request deleted successfully');
    }
}
