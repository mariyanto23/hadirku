<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardStats extends Component
{
  public function render()
  {
    $today = today();
    $totalStudents = Student::count();

    $todayPresent = Attendance::query()
      ->whereDate(
        'attendance_date',
        $today
      )
      ->whereIn('status', [
        'hadir',
        'terlambat',
      ])
      ->where('approval_status', 'approved')
      ->count();

    $todayLate = Attendance::query()
      ->whereDate(
        'attendance_date',
        $today
      )
      ->where('status', 'terlambat')
      ->where('approval_status', 'approved')
      ->count();

    $todayAlpha = Attendance::query()
      ->whereDate(
        'attendance_date',
        $today
      )
      ->where('status', 'alpha')
      ->whereIn('approval_status', [
        'approved',
        'rejected',
      ])
      ->count();

    $todayIzin = Attendance::query()
      ->whereDate(
        'attendance_date',
        $today
      )
      ->where('status', 'izin')
      ->where('approval_status', 'approved')
      ->count();

    $todaySakit = Attendance::query()
      ->whereDate(
        'attendance_date',
        $today
      )
      ->where('status', 'sakit')
      ->where('approval_status', 'approved')
      ->count();

    $pendingLeaveRequests = Attendance::query()
      ->whereIn('status', [
        'izin',
        'sakit',
      ])
      ->where('approval_status', 'pending')
      ->count();

    $faceIncompleteStudents = Student::query()
      ->withCount('descriptors')
      ->get()
      ->where('descriptors_count', '<', 3)
      ->count();

    $classAttendanceRows = Attendance::query()
      ->join('students', 'attendances.student_id', '=', 'students.id')
      ->whereDate('attendances.attendance_date', $today)
      ->where(function ($query) {
        $query
          ->where(function ($q) {
            $q->whereIn('attendances.status', [
              'hadir',
              'terlambat',
              'izin',
              'sakit',
            ])
              ->where('attendances.approval_status', 'approved');
          })
          ->orWhere(function ($q) {
            $q->where('attendances.status', 'alpha')
              ->whereIn('attendances.approval_status', [
                'approved',
                'rejected',
              ]);
          });
      })
      ->select([
        'students.class_id',
        'attendances.status',
        DB::raw('COUNT(*) as total'),
      ])
      ->groupBy([
        'students.class_id',
        'attendances.status',
      ])
      ->get()
      ->groupBy('class_id');

    $classSummaries = SchoolClass::query()
      ->withCount('students')
      ->orderBy('name')
      ->get()
      ->map(function ($class) use ($classAttendanceRows) {
        $rows = $classAttendanceRows->get($class->id, collect());

        $countStatus = fn (string $status) => (int) (
          $rows->firstWhere('status', $status)?->total ?? 0
        );

        return [
          'name' => $class->name,
          'students_count' => $class->students_count,
          'hadir' => $countStatus('hadir'),
          'terlambat' => $countStatus('terlambat'),
          'izin_sakit' => $countStatus('izin') + $countStatus('sakit'),
          'alpha' => $countStatus('alpha'),
        ];
      });

    return view('livewire.admin.dashboard-stats', [

      'totalStudents' =>
      $totalStudents,

      'todayPresent' =>
      $todayPresent,

      'todayLate' =>
      $todayLate,

      'todayAlpha' =>
      $todayAlpha,

      'todayIzin' =>
      $todayIzin,

      'todaySakit' =>
      $todaySakit,

      'pendingLeaveRequests' =>
      $pendingLeaveRequests,

      'faceIncompleteStudents' =>
      $faceIncompleteStudents,

      'classSummaries' =>
      $classSummaries,

      'totalClasses' =>
      SchoolClass::count(),

      'attendanceRate' =>
      $totalStudents > 0
        ? round(($todayPresent / $totalStudents) * 100)
        : 0,

      'recentAttendances' =>
      Attendance::query()
        ->with([
          'student.user',
          'student.class',
        ])
        ->where('approval_status', '!=', 'pending')
        ->latest()
        ->limit(6)
        ->get(),

    ]);
  }
}
