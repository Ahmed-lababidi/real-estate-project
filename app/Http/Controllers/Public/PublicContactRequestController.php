<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactRequestRequest;
use App\Http\Resources\ContactRequestResource;
use App\Models\ContactRequest;
use App\Traits\ApiResponse;

class PublicContactRequestController extends Controller
{
    use ApiResponse;

    public function store(StoreContactRequestRequest $request)
    {
        $lead = ContactRequest::create($request->validated());

        return $this->successResponse(
            new ContactRequestResource($lead),
            'Contact request submitted successfully',
            201
        );
    }
}
