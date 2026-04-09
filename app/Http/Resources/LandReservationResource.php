<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_code' => $this->reservation_code,
            'land_id' => $this->land_id,

            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'customer_national_id' => $this->customer_national_id,
            'notes' => $this->notes,

            'status' => $this->status,
            'reserved_at' => $this->reserved_at,
            'expires_at' => $this->expires_at,
            'confirmed_at' => $this->confirmed_at,
            'cancelled_at' => $this->cancelled_at,

            'confirmed_by' => $this->whenLoaded('confirmedBy', fn() => [
                'id' => $this->confirmedBy?->id,
                'name' => $this->confirmedBy?->name,
                'email' => $this->confirmedBy?->email,
            ]),

            'cancelled_by' => $this->whenLoaded('cancelledBy', fn() => [
                'id' => $this->cancelledBy?->id,
                'name' => $this->cancelledBy?->name,
                'email' => $this->cancelledBy?->email,
            ]),

            'created_at' => $this->created_at,
        ];
    }
}
