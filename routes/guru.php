<?php

use App\Livewire\Attendance\ManualAttendance;
use App\Livewire\Guru\AttendanceReport;
use App\Livewire\Guru\FaceAttendance;
use App\Livewire\Siswa\FaceRegistration;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'role:guru',
])->prefix('guru')->name('guru.')->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        $defaultClass = $user
            ->defaultClass()
            ->withCount('students')
            ->first();
        $classId = $defaultClass?->id;

        $students = $classId
            ? Student::query()
                ->where('class_id', $classId)
                ->withCount('descriptors')
                ->get()
            : collect();

        $attendanceScope = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->when($classId, function ($query) use ($classId) {
                $query->whereHas('student', function ($studentQuery) use ($classId) {
                    $studentQuery->where('class_id', $classId);
                });
            }, function ($query) {
                $query->whereRaw('1 = 0');
            });

        $todayScope = (clone $attendanceScope)
            ->whereDate('attendance_date', today());

        $pendingScope = (clone $attendanceScope)
            ->whereIn('status', ['izin', 'sakit'])
            ->where('approval_status', 'pending');

        return view('guru.dashboard', [
            'defaultClass' => $defaultClass,
            'studentsCount' => $students->count(),
            'readyStudentsCount' => $students
                ->where('descriptors_count', '>=', 3)
                ->count(),
            'attendanceTodayCount' => (clone $todayScope)->count(),
            'pendingRequestsCount' => (clone $pendingScope)->count(),
            'recentAttendances' => (clone $attendanceScope)
                ->latest()
                ->limit(5)
                ->get(),
            'pendingRequests' => (clone $pendingScope)
                ->oldest()
                ->limit(5)
                ->get(),
        ]);
    })->name('dashboard');

    Route::get(
        '/face-attendance',
        FaceAttendance::class
    )->name('face.attendance');

    Route::get('/face-registration', FaceRegistration::class)
        ->name('face-registration');

    Route::get(
        '/manual-attendance',
        ManualAttendance::class
    )->name('manual.attendance');

    Route::get(
        '/attendance-report',
        AttendanceReport::class
    )->name('attendance.report');

    Route::get(
        '/class-descriptors/{classId}',
        function ($classId) {

            return Student::query()
                ->with('descriptors')
                ->whereHas('descriptors')
                ->where('class_id', $classId)
                ->orderBy('nis')
                ->get()
                ->map(function ($student) {

                    return [
                        'label' => (string) $student->id,
                        'descriptors' => $student
                            ->descriptors
                            ->pluck('descriptor'),
                    ];
                });
        }
    );
});
