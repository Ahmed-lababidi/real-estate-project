<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkNotification extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'title',
        'body',
        'topic',
        'image',
        'target_type',
        'data',
        'is_sent',
        'sent_at',
        'is_scheduled',
        'scheduled_at',
        'status',
        'attempts',
        'last_error',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
        'is_sent' => 'boolean',
        'is_scheduled' => 'boolean',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function scopePendingScheduled($query)
    {
        return $query->where('is_scheduled', true)
            ->where('is_sent', false)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }
}
