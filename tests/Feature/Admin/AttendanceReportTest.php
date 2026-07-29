<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AttendanceReport as AdminAttendanceReport;
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

    public function test_admin_attendance_report_defaults_to_all_classes(): void
    {
        Role::create(['name' => 'admin']);

        SchoolClass::create(['name' => 'Kelas 1']);
        SchoolClass::create(['name' => 'Kelas 2']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(AdminAttendanceReport::class)
            ->assertSet('classFilter', '')
            ->assertSee('Semua Kelas');
    }

    public function test_admin_can_filter_unattended_students_without_creating_attendance_status(): void
    {
        Role::create(['name' => 'admin']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $attendedUser = User::factory()->create([
            'name' => 'Siswa Sudah',
            'username' => '1001',
        ]);
        $attendedStudent = Student::create([
            'user_id' => $attendedUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $unattendedUser = User::factory()->create([
            'name' => 'Siswa Belum',
            'username' => '1002',
        ]);
        Student::create([
            'user_id' => $unattendedUser->id,
            'class_id' => $class->id,
            'nis' => '1002',
            'gender' => 'Perempuan',
        ]);

        Attendance::create([
            'student_id' => $attendedStudent->id,
            'attendance_date' => today(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'approval_status' => 'approved',
        ]);

        Livewire::actingAs($admin)
            ->test(AdminAttendanceReport::class)
            ->set('statusFilter', 'belum_presensi')
            ->assertSee('Siswa Belum')
            ->assertSee('siswa belum presensi');

        $this->assertDatabaseMissing('attendances', [
            'status' => 'belum_presensi',
        ]);
    }
}
