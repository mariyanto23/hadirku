<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AppLogoController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Livewire\Admin\ClassManagement;
use App\Livewire\Admin\FaceAttendance;
use App\Livewire\Admin\GuruManagement;
use App\Livewire\Admin\StudentManagement;
use App\Livewire\Admin\AttendanceSettings;
use App\Livewire\Admin\AttendanceReport;
use App\Livewire\Admin\AcademicCalendar;
use App\Livewire\Attendance\ManualAttendance;
use App\Livewire\Siswa\FaceRegistration;
use App\Models\Student;

Route::middleware([
  'auth',
  'role:admin'
])->prefix('admin')->name('admin.')->group(function () {

  Route::view('/dashboard', 'admin.dashboard')
    ->name('dashboard');

  Route::get('/classes', ClassManagement::class)
    ->name('classes');

  Route::get(
    '/face-attendance',
    FaceAttendance::class
  )->name('face.attendance');

  Route::patch('/classes/{class}', [SchoolClassController::class, 'update'])
    ->name('classes.update');

  Route::get('/gurus', GuruManagement::class)
    ->name('gurus');

  Route::patch('/gurus/{guru}', [GuruController::class, 'update'])
    ->name('gurus.update');

  Route::patch('/gurus/{guru}/status', [GuruController::class, 'toggleStatus'])
    ->name('gurus.toggle-status');

  Route::get('/students', StudentManagement::class)
    ->name('students');

  Route::patch('/students/{student}', [StudentController::class, 'update'])
    ->name('students.update');

  Route::get('/face-registration', FaceRegistration::class)
    ->name('face-registration');

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

  Route::get(
    '/attendance-settings',
    AttendanceSettings::class
  )->name('attendance.settings');

  Route::post(
    '/app-logo',
    [AppLogoController::class, 'update']
  )->name('app-logo.update');

  Route::get(
    '/attendance-report',
    AttendanceReport::class
  )->name('attendance.report');

  Route::get(
    '/academic-calendar',
    AcademicCalendar::class
  )->name('academic-calendar');

  Route::get(
    '/manual-attendance',
    ManualAttendance::class
  )->name('manual.attendance');
});
