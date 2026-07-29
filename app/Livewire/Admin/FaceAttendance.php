<?php

namespace App\Livewire\Admin;

use App\Livewire\Guru\FaceAttendance as GuruFaceAttendance;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\SchoolClass;

class FaceAttendance extends GuruFaceAttendance
{
    public function mount(): void
    {
        $this->selectedClass = '';
    }

    public function render()
    {
        $attendanceDate = today()->toDateString();

        return view('livewire.guru.face-attendance', [
            'classes' => SchoolClass::query()
                ->orderBy('name')
                ->get(),
            'settings' => AttendanceSetting::current(),
            'attendanceAvailability' => $this->attendanceAvailability(),
            'selectedClassStats' => $this->selectedClassStats(),
            'recentAttendances' => Attendance::query()
                ->with([
                    'student.user',
                    'student.class',
                ])
                ->whereDate('attendance_date', $attendanceDate)
                ->where('approval_status', '!=', 'pending')
                ->latest()
                ->limit(8)
                ->get(),
            'descriptorBaseUrl' => url('/admin/class-descriptors'),
            'faceAttendanceTitle' => 'Presensi Wajah',
        ]);
    }
}
