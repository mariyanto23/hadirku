<?php

namespace Tests\Feature\Guru;

use App\Models\Attendance;
use App\Models\FaceDescriptor;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_dashboard_shows_default_class_summary_and_activity(): void
    {
        Role::create(['name' => 'guru']);

        $class = SchoolClass::create([
            'name' => 'Kelas 1',
        ]);

        $guru = User::factory()->create([
            'default_class_id' => $class->id,
        ]);
        $guru->assignRole('guru');

        $studentUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => '1001',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $requestStudentUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'username' => '1002',
        ]);

        $requestStudent = Student::create([
            'user_id' => $requestStudentUser->id,
            'class_id' => $class->id,
            'nis' => '1002',
            'gender' => 'Perempuan',
        ]);

        foreach (range(1, 3) as $index) {
            FaceDescriptor::create([
                'student_id' => $student->id,
                'descriptor' => [$index, $index + 0.1],
            ]);
        }

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'approval_status' => 'approved',
        ]);

        Attendance::create([
            'student_id' => $requestStudent->id,
            'attendance_date' => today(),
            'attendance_time' => '08:00:00',
            'status' => 'izin',
            'approval_status' => 'pending',
        ]);

        $response = $this
            ->actingAs($guru)
            ->get(route('guru.dashboard'));

        $response->assertOk();
        $response->assertSee('Kelas 1');
        $response->assertSee('Presensi Hari Ini');
        $response->assertSee('Siap Dipindai');
        $response->assertSee('1/2');
        $response->assertSee('Presensi Wajah');
        $response->assertSee('Registrasi Wajah');
        $response->assertSee('Izin/Sakit');
        $response->assertSee('Aktivitas Terakhir');
        $response->assertSee('Pengajuan Menunggu');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Aminah');
        $response->assertDontSee('Tips');
        $response->assertDontSee('Siapkan pencahayaan');
    }

    public function test_guru_dashboard_shows_empty_state_without_default_class(): void
    {
        Role::create(['name' => 'guru']);

        $guru = User::factory()->create([
            'default_class_id' => null,
        ]);
        $guru->assignRole('guru');

        $response = $this
            ->actingAs($guru)
            ->get(route('guru.dashboard'));

        $response->assertOk();
        $response->assertSee('Kelas bawaan belum diatur');
        $response->assertSee('0/0');
    }
}
