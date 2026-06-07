<?php

namespace Tests\Feature\Guru;

use App\Livewire\Guru\AttendanceReport as GuruAttendanceReport;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_view_attendance_report_for_default_class_only(): void
    {
        Role::create(['name' => 'guru']);

        $defaultClass = SchoolClass::create(['name' => 'Kelas 1']);
        $otherClass = SchoolClass::create(['name' => 'Kelas 2']);

        $guru = User::factory()->create([
            'default_class_id' => $defaultClass->id,
        ]);
        $guru->assignRole('guru');

        $studentUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => '1001',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $defaultClass->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $otherStudentUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'username' => '1002',
        ]);
        $otherStudent = Student::create([
            'user_id' => $otherStudentUser->id,
            'class_id' => $otherClass->id,
            'nis' => '1002',
            'gender' => 'Perempuan',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'approval_status' => 'approved',
        ]);

        Attendance::create([
            'student_id' => $otherStudent->id,
            'attendance_date' => today(),
            'attendance_time' => '07:10:00',
            'status' => 'terlambat',
            'approval_status' => 'approved',
        ]);

        $response = $this
            ->actingAs($guru)
            ->get(route('guru.attendance.report'));

        $response->assertOk();
        $response->assertSee('Rekap Presensi');
        $response->assertSee('Kelas 1');
        $response->assertSee('Semua Kelas');
        $response->assertSee('Kelas 2');
        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Siti Aminah');
    }

    public function test_guru_can_filter_attendance_report_to_another_class(): void
    {
        Role::create(['name' => 'guru']);

        $defaultClass = SchoolClass::create(['name' => 'Kelas 1']);
        $otherClass = SchoolClass::create(['name' => 'Kelas 2']);

        $guru = User::factory()->create([
            'default_class_id' => $defaultClass->id,
        ]);
        $guru->assignRole('guru');

        $defaultStudentUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => '1001',
        ]);
        $defaultStudent = Student::create([
            'user_id' => $defaultStudentUser->id,
            'class_id' => $defaultClass->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $otherStudentUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'username' => '1002',
        ]);
        $otherStudent = Student::create([
            'user_id' => $otherStudentUser->id,
            'class_id' => $otherClass->id,
            'nis' => '1002',
            'gender' => 'Perempuan',
        ]);

        Attendance::create([
            'student_id' => $defaultStudent->id,
            'attendance_date' => today(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'approval_status' => 'approved',
        ]);

        Attendance::create([
            'student_id' => $otherStudent->id,
            'attendance_date' => today(),
            'attendance_time' => '07:10:00',
            'status' => 'terlambat',
            'approval_status' => 'approved',
        ]);

        Livewire::actingAs($guru)
            ->test(GuruAttendanceReport::class)
            ->assertSet('classFilter', (string) $defaultClass->id)
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah')
            ->set('classFilter', (string) $otherClass->id)
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Santoso');
    }

    public function test_guru_attendance_report_shows_empty_state_without_default_class(): void
    {
        Role::create(['name' => 'guru']);

        $guru = User::factory()->create([
            'default_class_id' => null,
        ]);
        $guru->assignRole('guru');

        $response = $this
            ->actingAs($guru)
            ->get(route('guru.attendance.report'));

        $response->assertOk();
        $response->assertSee('Semua Kelas');
        $response->assertSee('0 data ditemukan');
    }
}
