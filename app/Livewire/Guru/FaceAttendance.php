<?php

namespace App\Livewire\Guru;

use App\Models\AcademicHoliday;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FaceAttendance extends Component
{
    public $selectedClass;

    public function mount(): void
    {
        $this->selectedClass = Auth::user()?->default_class_id ?: '';
    }

    public function saveAttendance(
        $studentId,
        $confidence,
        $classId
    ): array {
        $attendanceNow = now();
        $attendanceDate = $attendanceNow->toDateString();
        $attendanceTime = $attendanceNow->format('H:i:s');

        $studentExists = Student::query()
            ->whereKey($studentId)
            ->where('class_id', $classId)
            ->exists();

        if (! $studentExists) {
            return [
                'saved' => false,
                'message' => 'Siswa tidak ditemukan pada kelas yang dipilih.',
            ];
        }

        $blockingHoliday = AcademicHoliday::blockingAttendanceOn($attendanceDate);

        if ($blockingHoliday) {
            return [
                'saved' => false,
                'message' => 'Presensi tidak dibuka karena hari ini libur: '.$blockingHoliday->title.'.',
            ];
        }

        $settings = AttendanceSetting::current();
        $allowingHoliday = AcademicHoliday::allowingAttendanceOn($attendanceDate);

        if (! $settings->isSchoolDay($attendanceDate) && ! $allowingHoliday) {
            return [
                'saved' => false,
                'message' => 'Presensi tidak dibuka karena hari ini bukan hari sekolah.',
            ];
        }

        $alreadyExists = Attendance::query()
            ->where('student_id', $studentId)
            ->whereDate('attendance_date', $attendanceDate)
            ->exists();

        if ($alreadyExists) {
            return [
                'saved' => false,
                'message' => 'Siswa ini sudah memiliki presensi hari ini.',
            ];
        }

        $status = $attendanceTime
          > $settings->late_after
          ? 'terlambat'
          : 'hadir';

        Attendance::create([
            'student_id' => $studentId,
            'attendance_date' => $attendanceDate,
            'attendance_time' => $attendanceTime,
            'status' => $status,
            'confidence_score' => $confidence,
            'match_threshold_used' => $settings->face_match_threshold,
            'approval_status' => 'approved',
        ]);

        $this->dispatch('attendance-success');

        return [
            'saved' => true,
            'message' => 'Presensi berhasil disimpan.',
            'status' => $status,
        ];
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
        ]);
    }

    protected function selectedClassStats(): ?array
    {
        if (! $this->selectedClass) {
            return null;
        }

        $class = SchoolClass::query()
            ->find($this->selectedClass);

        if (! $class) {
            return null;
        }

        $students = Student::query()
            ->where('class_id', $class->id)
            ->withCount('descriptors')
            ->get();

        return [
            'name' => $class->name,
            'students' => $students->count(),
            'descriptor_students' => $students
                ->where('descriptors_count', '>', 0)
                ->count(),
            'ready_students' => $students
                ->where('descriptors_count', '>=', 3)
                ->count(),
            'descriptors' => $students->sum('descriptors_count'),
        ];
    }

    protected function attendanceAvailability(): array
    {
        $attendanceDate = today()->toDateString();
        $blockingHoliday = AcademicHoliday::blockingAttendanceOn($attendanceDate);

        if ($blockingHoliday) {
            return [
                'can_scan' => false,
                'tone' => 'rose',
                'title' => 'Presensi ditutup hari ini',
                'message' => 'Hari ini libur: '.$blockingHoliday->title.'.',
                'detail' => 'Kamera tidak perlu dinyalakan karena presensi tidak dibuka.',
            ];
        }

        $settings = AttendanceSetting::current();
        $allowingHoliday = AcademicHoliday::allowingAttendanceOn($attendanceDate);

        if (! $settings->isSchoolDay($attendanceDate) && ! $allowingHoliday) {
            return [
                'can_scan' => false,
                'tone' => 'slate',
                'title' => 'Presensi tidak dibuka',
                'message' => 'Hari ini bukan hari sekolah berdasarkan pengaturan presensi.',
                'detail' => 'Kamera tidak perlu dinyalakan dan alpa otomatis tidak berjalan.',
            ];
        }

        if ($allowingHoliday) {
            return [
                'can_scan' => true,
                'tone' => 'emerald',
                'title' => 'Presensi dibuka',
                'message' => 'Presensi dibuka untuk kegiatan sekolah: '.$allowingHoliday->title.'.',
                'detail' => 'Tanggal ini menjadi pengecualian dari kalender akademik.',
            ];
        }

        return [
            'can_scan' => true,
            'tone' => 'blue',
            'title' => 'Presensi siap digunakan',
            'message' => 'Hari ini termasuk hari sekolah.',
            'detail' => null,
        ];
    }
}
