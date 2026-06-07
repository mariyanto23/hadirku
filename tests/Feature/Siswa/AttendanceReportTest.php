<?php

namespace Tests\Feature\Siswa;

use App\Livewire\Siswa\AttendanceReport as SiswaAttendanceReport;
use App\Models\Attendance;
use App\Models\AcademicHoliday;
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

    public function test_siswa_can_view_current_month_attendance_report_only_for_themselves(): void
    {
        Role::create(['name' => 'siswa']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => '1001',
        ]);
        $user->assignRole('siswa');

        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $otherUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'username' => '1002',
        ]);
        $otherStudent = Student::create([
            'user_id' => $otherUser->id,
            'class_id' => $class->id,
            'nis' => '1002',
            'gender' => 'Perempuan',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today()->startOfMonth(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'approval_status' => 'approved',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today()->subMonth(),
            'attendance_time' => '07:05:00',
            'status' => 'alpha',
            'approval_status' => 'approved',
            'notes' => 'Data bulan lalu',
        ]);

        Attendance::create([
            'student_id' => $otherStudent->id,
            'attendance_date' => today()->startOfMonth(),
            'attendance_time' => '07:10:00',
            'status' => 'sakit',
            'approval_status' => 'pending',
            'notes' => 'Milik siswa lain',
        ]);

        AcademicHoliday::create([
            'title' => 'Libur Nasional',
            'type' => AcademicHoliday::TYPE_NATIONAL,
            'start_date' => today()->startOfMonth()->addDays(2),
            'end_date' => today()->startOfMonth()->addDays(2),
            'allow_attendance' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('siswa.attendance.report'));

        $response->assertOk();
        $response->assertSee('Rekap Bulan Ini');
        $response->assertSee('Kelas 1');
        $response->assertSee('Hadir');
        $response->assertSee('Kalender Bulan Ini');
        $response->assertSee('Libur');
        $response->assertSee('Libur Nasional');
        $response->assertDontSee('Grafik');
        $response->assertDontSee('Total Data');
        $response->assertDontSee('Milik siswa lain');
        $response->assertDontSee('Data bulan lalu');
    }

    public function test_siswa_attendance_report_can_filter_status(): void
    {
        Role::create(['name' => 'siswa']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today()->startOfMonth(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'approval_status' => 'approved',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today()->startOfMonth()->addDay(),
            'attendance_time' => '08:00:00',
            'status' => 'izin',
            'approval_status' => 'pending',
            'notes' => 'Keperluan keluarga',
        ]);

        Livewire::actingAs($user)
            ->test(SiswaAttendanceReport::class)
            ->assertSee('Hadir')
            ->assertSee('Keperluan keluarga')
            ->call('setStatusFilter', 'izin')
            ->assertSet('statusFilter', 'izin')
            ->assertSee('Keperluan keluarga')
            ->assertDontSee('07:00');
    }
}
