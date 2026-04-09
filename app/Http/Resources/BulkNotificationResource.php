<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BulkNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'topic' => $this->topic,
            'target_type' => $this->target_type,
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'data' => $this->data,
            'is_sent' => $this->is_sent,
            'sent_at' => $this->sent_at,
            'is_scheduled' => $this->is_scheduled,
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
            'attempts' => $this->attempts,
            'last_error' => $this->last_error,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'logs_count' => $this->whenCounted('logs'),
            'logs' => NotificationLogResource::collection($this->whenLoaded('logs')),
        ];
    }
}
