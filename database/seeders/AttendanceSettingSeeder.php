<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use Illuminate\Database\Seeder;

class AttendanceSettingSeeder extends Seeder
{
    public function run(): void
    {
        AttendanceSetting::firstOrCreate([], [
            'face_match_threshold' => 0.5,
            'scan_interval' => 1000,
            'attendance_start_time' => '06:30:00',
            'late_after' => '07:00:00',
            'max_descriptors' => 10,
            'auto_alpha' => true,
            'school_days' => AttendanceSetting::DEFAULT_SCHOOL_DAYS,
        ]);
    }
}
