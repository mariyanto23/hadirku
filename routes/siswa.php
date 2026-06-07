<?php

use App\Livewire\Siswa\FaceRegistration;
use App\Livewire\Siswa\AttendanceReport;
use App\Livewire\Siswa\LeaveRequest;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Illuminate\Support\Facades\Route;

Route::middleware([
  'auth',
  'role:siswa'
])->prefix('siswa')->name('siswa.')->group(function () {

  Route::get('/dashboard', function () {
    $user = auth()->user();
    $student = $user
      ->student()
      ->with('class')
      ->withCount('descriptors')
      ->first();

    $attendanceQuery = Attendance::query()
      ->when($student, function ($query) use ($student) {
        $query->where('student_id', $student->id);
      }, function ($query) {
        $query->whereRaw('1 = 0');
      });

    $todayAttendance = (clone $attendanceQuery)
      ->whereDate('attendance_date', today())
      ->latest()
      ->first();

    $activeRequests = (clone $attendanceQuery)
      ->whereIn('status', [
        'izin',
        'sakit',
        'alpha',
      ])
      ->where('approval_status', 'pending')
      ->whereNotNull('requested_by_user_id')
      ->latest()
      ->limit(3)
      ->get();

    $recentAttendances = (clone $attendanceQuery)
      ->latest()
      ->limit(3)
      ->get();

    return view('siswa.dashboard', [
      'student' => $student,
      'todayAttendance' => $todayAttendance,
      'activeRequests' => $activeRequests,
      'recentAttendances' => $recentAttendances,
      'minimumDescriptors' => 3,
      'maxDescriptors' => AttendanceSetting::current()->max_descriptors,
    ]);
  })->name('dashboard');

  Route::get('/face-registration', FaceRegistration::class)
    ->name('face-registration');

  Route::get('/leave-request', LeaveRequest::class)
    ->name('leave-request');

  Route::get('/attendance-report', AttendanceReport::class)
    ->name('attendance.report');
});
