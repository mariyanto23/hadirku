<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AcademicHoliday extends Model
{
    public const TYPE_NATIONAL = 'national';
    public const TYPE_SEMESTER = 'semester';
    public const TYPE_SCHOOL = 'school';
    public const TYPE_EVENT = 'event';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'title',
        'type',
        'start_date',
        'end_date',
        'allow_attendance',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'allow_attendance' => 'boolean',
    ];

    public static function types(): array
    {
        return [
            self::TYPE_NATIONAL => 'Libur Nasional',
            self::TYPE_SEMESTER => 'Libur Semester',
            self::TYPE_SCHOOL => 'Libur Sekolah',
            self::TYPE_EVENT => 'Kegiatan Sekolah',
            self::TYPE_OTHER => 'Lainnya',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type] ?? 'Lainnya';
    }

    public function scopeOverlapping(Builder $query, CarbonInterface|string $start, CarbonInterface|string $end): Builder
    {
        return $query
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start);
    }

    public static function forDate(CarbonInterface|string $date): ?self
    {
        return self::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->orderBy('start_date')
            ->first();
    }

    public static function blockingAttendanceOn(CarbonInterface|string $date): ?self
    {
        return self::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('allow_attendance', false)
            ->orderBy('start_date')
            ->first();
    }

    public static function allowingAttendanceOn(CarbonInterface|string $date): ?self
    {
        return self::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('allow_attendance', true)
            ->orderBy('start_date')
            ->first();
    }
}
