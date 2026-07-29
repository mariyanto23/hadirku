<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'attendance_date',
        'attendance_time',
        'status',
        'confidence_score',
        'match_threshold_used',
        'notes',
        'attachment_path',
        'attachment_name',
        'approval_status',
        'requested_by_user_id',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path
            ? Storage::disk('public')->url($this->attachment_path)
            : null;
    }

    public function attachmentDisplayName(): string
    {
        return $this->attachment_name ?: basename((string) $this->attachment_path);
    }

    public function attachmentIsImage(): bool
    {
        $extension = strtolower(pathinfo((string) $this->attachment_path, PATHINFO_EXTENSION));

        return in_array($extension, [
            'jpg',
            'jpeg',
            'png',
            'webp',
        ], true);
    }
}
