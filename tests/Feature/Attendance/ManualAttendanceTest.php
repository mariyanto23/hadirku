<?php

namespace Tests\Feature\Attendance;

use App\Livewire\Attendance\ManualAttendance;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_manual_leave_attendance_can_store_optional_attachment(): void
    {
        Storage::fake('public');

        Role::create(['name' => 'admin']);

        $admin = User::factory()->create([
            'name' => 'Admin',
        ]);
        $admin->assignRole('admin');

        $class = SchoolClass::create([
            'name' => 'Kelas 1',
        ]);

        $studentUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'username' => 'siti001',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1002',
            'gender' => 'Perempuan',
        ]);

        $file = UploadedFile::fake()->image('bukti-izin.jpg', 600, 400)->size(256);

        $this->actingAs($admin);

        Livewire::test(ManualAttendance::class)
            ->set('selectedClass', (string) $class->id)
            ->set('student_id', (string) $student->id)
            ->set('attendance_date', today()->toDateString())
            ->set('attendance_time', '08:00')
            ->set('status', 'izin')
            ->set('notes', 'Izin mengikuti kegiatan keluarga.')
            ->set('attachment', $file)
            ->call('save')
            ->assertHasNoErrors();

        $attendance = Attendance::query()->firstOrFail();

        $this->assertSame('bukti-izin.jpg', $attendance->attachment_name);
        $this->assertNotNull($attendance->attachment_path);
        Storage::disk('public')->assertExists($attendance->attachment_path);
    }
}
