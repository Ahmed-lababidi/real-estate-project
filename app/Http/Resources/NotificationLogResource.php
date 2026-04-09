<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'status' => $this->status,
            'response' => $this->response,
            'error_message' => $this->error_message,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
        ];
    }
}
