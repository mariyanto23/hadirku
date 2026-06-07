<?php

namespace Tests\Feature\Attendance;

use App\Livewire\Attendance\ManualAttendance;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManualAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_correction_to_alpha_marks_attendance_as_rejected(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create([
            'name' => 'Admin',
        ]);
        $admin->assignRole('admin');

        $class = SchoolClass::create([
            'name' => 'Kelas 1',
        ]);

        $studentUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budi001',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => today(),
            'attendance_time' => '07:00:00',
            'status' => 'hadir',
            'notes' => 'Presensi awal.',
            'approval_status' => 'approved',
        ]);

        $this->actingAs($admin);

        Livewire::test(ManualAttendance::class)
            ->call('edit', $attendance->id)
            ->set('status', 'alpha')
            ->set('notes', 'Siswa tidak hadir tanpa keterangan.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'alpha',
            'approval_status' => 'rejected',
            'notes' => 'Siswa tidak hadir tanpa keterangan.',
            'reviewed_by_user_id' => $admin->id,
        ]);
    }
}
