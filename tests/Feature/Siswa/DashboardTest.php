<?php

namespace Tests\Feature\Siswa;

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

    public function test_siswa_dashboard_shows_home_summary_actions_and_recent_data(): void
    {
        Role::create(['name' => 'siswa']);

        $class = SchoolClass::create([
            'name' => 'Kelas 1',
        ]);

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
            'student_id' => $student->id,
            'attendance_date' => today()->addDay(),
            'attendance_time' => '08:00:00',
            'status' => 'izin',
            'approval_status' => 'pending',
            'requested_by_user_id' => $user->id,
            'notes' => 'Keperluan keluarga',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('siswa.dashboard'));

        $response->assertOk();
        $response->assertSee('Beranda');
        $response->assertSee('Kelas 1');
        $response->assertSee('Status Wajah');
        $response->assertSee('Siap Digunakan');
        $response->assertSee('Presensi Hari Ini');
        $response->assertSee('Hadir');
        $response->assertSee('Pengajuan Aktif');
        $response->assertSee('Registrasi Wajah');
        $response->assertSee('Izin/Sakit');
        $response->assertSee('Riwayat Presensi');
        $response->assertSee('Menunggu');
        $response->assertSee('Navigasi utama siswa');
        $response->assertSee('Face ID');
        $response->assertSee('Profil');
        $response->assertDontSee('Dashboard Siswa');
        $response->assertDontSee('Minimal 3 descriptor');
    }

    public function test_siswa_dashboard_shows_empty_state_without_student_data(): void
    {
        Role::create(['name' => 'siswa']);

        $user = User::factory()->create();
        $user->assignRole('siswa');

        $response = $this
            ->actingAs($user)
            ->get(route('siswa.dashboard'));

        $response->assertOk();
        $response->assertSee('Data siswa belum ditemukan');
        $response->assertSee('Belum Ada');
    }
}
