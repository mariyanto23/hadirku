<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    public const DEFAULT_SCHOOL_DAYS = [1, 2, 3, 4, 5, 6];

    protected $fillable = [
        'face_match_threshold',
        'scan_interval',
        'attendance_start_time',
        'late_after',
        'max_descriptors',
        'auto_alpha',
        'school_days',
        'logo_path',
        'favicon_path',
    ];

    protected $casts = [
        'face_match_threshold' => 'float',
        'scan_interval' => 'integer',
        'max_descriptors' => 'integer',
        'auto_alpha' => 'boolean',
        'school_days' => 'array',
    ];

    public static function schoolDayOptions(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }

    public function normalizedSchoolDays(): array
    {
        $days = $this->school_days ?: self::DEFAULT_SCHOOL_DAYS;

        return collect($days)
            ->map(fn ($day) => (int) $day)
            ->filter(fn ($day) => $day >= 1 && $day <= 7)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function isSchoolDay(CarbonInterface|string|null $date = null): bool
    {
        $date = $date instanceof CarbonInterface
            ? $date
            : Carbon::parse($date ?: today());

        return in_array($date->dayOfWeekIso, $this->normalizedSchoolDays(), true);
    }

    public function schoolDayLabels(): array
    {
        $options = self::schoolDayOptions();

        return collect($this->normalizedSchoolDays())
            ->map(fn ($day) => $options[$day])
            ->all();
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            [],
            [
                'face_match_threshold' => 0.5,
                'scan_interval' => 1000,
                'attendance_start_time' => '06:30:00',
                'late_after' => '07:00:00',
                'max_descriptors' => 10,
                'auto_alpha' => true,
                'school_days' => self::DEFAULT_SCHOOL_DAYS,
            ]
        );
    }
}
