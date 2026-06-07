<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceSetting;
use Livewire\Component;

class AttendanceSettings extends Component
{
    public $face_match_threshold;

    public $scan_interval;

    public $attendance_start_time;

    public $late_after;

    public $max_descriptors;

    public $auto_alpha;

    public array $school_days = [];

    public $existingLogo;

    public $existingFavicon;

    public function mount(): void
    {
        $settings = AttendanceSetting::current();

        $this->face_match_threshold =
          $settings->face_match_threshold;

        $this->scan_interval =
          $settings->scan_interval;

        $this->attendance_start_time =
          substr((string) $settings->attendance_start_time, 0, 5);

        $this->late_after =
          substr((string) $settings->late_after, 0, 5);

        $this->max_descriptors =
          $settings->max_descriptors;

        $this->auto_alpha =
          $settings->auto_alpha;

        $this->school_days =
          $settings->normalizedSchoolDays();

        $this->existingLogo =
          $settings->logo_path;

        $this->existingFavicon =
          $settings->favicon_path;
    }

    public function save(): void
    {
        $this->validate([
            'face_match_threshold' => 'required|numeric|min:0.3|max:0.8',

            'scan_interval' => 'required|integer|min:500|max:5000',

            'attendance_start_time' => 'required|date_format:H:i',

            'late_after' => 'required|date_format:H:i|after_or_equal:attendance_start_time',

            'max_descriptors' => 'required|integer|min:3|max:10',

            'auto_alpha' => 'boolean',

            'school_days' => ['required', 'array', 'min:1'],

            'school_days.*' => ['integer', 'between:1,7'],
        ]);

        $schoolDays = collect($this->school_days)
            ->map(fn ($day) => (int) $day)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $settings = AttendanceSetting::current();

        $settings->update([
            'face_match_threshold' => $this->face_match_threshold,

            'scan_interval' => $this->scan_interval,

            'attendance_start_time' => $this->attendance_start_time,

            'late_after' => $this->late_after,

            'max_descriptors' => $this->max_descriptors,

            'auto_alpha' => $this->auto_alpha,

            'school_days' => $schoolDays,
        ]);

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Pengaturan berhasil disimpan.'
        );
    }

    public function render()
    {
        return view(
            'livewire.admin.attendance-settings',
            [
                'schoolDayOptions' => AttendanceSetting::schoolDayOptions(),
            ]
        );
    }
}
