<?php

namespace Tests\Feature;

use App\Livewire\Guru\FaceAttendance;
use App\Livewire\Siswa\LeaveRequest;
use App\Models\AcademicHoliday;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademicHolidayIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_face_attendance_is_blocked_on_holiday(): void
    {
        Role::create(['name' => 'guru']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $guru = User::factory()->create(['default_class_id' => $class->id]);
        $guru->assignRole('guru');

        $studentUser = User::factory()->create(['username' => '1001']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        AcademicHoliday::create([
            'title' => 'Libur Nasional',
            'type' => AcademicHoliday::TYPE_NATIONAL,
            'start_date' => today(),
            'end_date' => today(),
            'allow_attendance' => false,
        ]);

        Livewire::actingAs($guru)
            ->test(FaceAttendance::class)
            ->call('saveAttendance', $student->id, 0.84, $class->id)
            ->assertReturned([
                'saved' => false,
                'message' => 'Presensi tidak dibuka karena hari ini libur: Libur Nasional.',
            ]);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $student->id,
            'attendance_date' => today()->format('Y-m-d'),
        ]);
    }

    public function test_face_attendance_page_shows_closed_holiday_state(): void
    {
        Role::create(['name' => 'guru']);

        $guru = User::factory()->create();
        $guru->assignRole('guru');

        AcademicHoliday::create([
            'title' => 'Libur Nasional',
            'type' => AcademicHoliday::TYPE_NATIONAL,
            'start_date' => today(),
            'end_date' => today(),
            'allow_attendance' => false,
        ]);

        $response = $this
            ->actingAs($guru)
            ->get(route('guru.face.attendance'));

        $response->assertOk();
        $response->assertSee('Presensi ditutup hari ini');
        $response->assertSee('Hari ini libur: Libur Nasional.');
        $response->assertSee('Presensi Ditutup');
    }

    public function test_student_leave_request_is_blocked_on_holiday(): void
    {
        Role::create(['name' => 'siswa']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $user = User::factory()->create(['username' => '1001']);
        $user->assignRole('siswa');

        Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        AcademicHoliday::create([
            'title' => 'Libur Sekolah',
            'type' => AcademicHoliday::TYPE_SCHOOL,
            'start_date' => today(),
            'end_date' => today(),
            'allow_attendance' => false,
        ]);

        Livewire::actingAs($user)
            ->test(LeaveRequest::class)
            ->set('attendance_date', today()->format('Y-m-d'))
            ->set('status', 'izin')
            ->set('notes', 'Keperluan keluarga')
            ->call('submit')
            ->assertHasErrors(['attendance_date']);

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_auto_alpha_skips_blocked_holiday(): void
    {
        $class = SchoolClass::create(['name' => 'Kelas 1']);
        ClassSchedule::create([
            'class_id' => $class->id,
            'start_time' => '06:30:00',
            'end_time' => now()->subMinute()->format('H:i:s'),
        ]);

        $studentUser = User::factory()->create(['username' => '1001']);
        Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        AcademicHoliday::create([
            'title' => 'Libur Semester',
            'type' => AcademicHoliday::TYPE_SEMESTER,
            'start_date' => today(),
            'end_date' => today(),
            'allow_attendance' => false,
        ]);

        $this->artisan('attendance:auto-alpha')
            ->expectsOutput('Alpa otomatis dilewati karena hari ini libur: Libur Semester.')
            ->assertOk();

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_face_attendance_is_blocked_on_non_school_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-07 07:00:00'));

        Role::create(['name' => 'guru']);

        AttendanceSetting::current()->update([
            'school_days' => [1, 2, 3, 4, 5],
        ]);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $guru = User::factory()->create(['default_class_id' => $class->id]);
        $guru->assignRole('guru');

        $studentUser = User::factory()->create(['username' => '1001']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        Livewire::actingAs($guru)
            ->test(FaceAttendance::class)
            ->call('saveAttendance', $student->id, 0.84, $class->id)
            ->assertReturned([
                'saved' => false,
                'message' => 'Presensi tidak dibuka karena hari ini bukan hari sekolah.',
            ]);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $student->id,
        ]);
    }

    public function test_face_attendance_page_shows_non_school_day_state(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-07 07:00:00'));

        Role::create(['name' => 'guru']);

        AttendanceSetting::current()->update([
            'school_days' => [1, 2, 3, 4, 5],
        ]);

        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $response = $this
            ->actingAs($guru)
            ->get(route('guru.face.attendance'));

        $response->assertOk();
        $response->assertSee('Presensi tidak dibuka');
        $response->assertSee('Hari ini bukan hari sekolah berdasarkan pengaturan presensi.');
        $response->assertSee('Presensi Ditutup');
    }

    public function test_open_academic_calendar_can_override_non_school_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-07 07:00:00'));

        Role::create(['name' => 'guru']);

        AttendanceSetting::current()->update([
            'school_days' => [1, 2, 3, 4, 5],
        ]);

        AcademicHoliday::create([
            'title' => 'Kegiatan Sekolah',
            'type' => AcademicHoliday::TYPE_EVENT,
            'start_date' => today(),
            'end_date' => today(),
            'allow_attendance' => true,
        ]);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $guru = User::factory()->create(['default_class_id' => $class->id]);
        $guru->assignRole('guru');

        $studentUser = User::factory()->create(['username' => '1001']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        Livewire::actingAs($guru)
            ->test(FaceAttendance::class)
            ->call('saveAttendance', $student->id, 0.84, $class->id)
            ->assertReturned([
                'saved' => true,
                'message' => 'Presensi berhasil disimpan.',
                'status' => 'hadir',
            ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'hadir',
        ]);
    }

    public function test_auto_alpha_skips_non_school_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-07 12:00:00'));

        AttendanceSetting::current()->update([
            'school_days' => [1, 2, 3, 4, 5],
        ]);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        ClassSchedule::create([
            'class_id' => $class->id,
            'start_time' => '06:30:00',
            'end_time' => '11:00:00',
        ]);

        $studentUser = User::factory()->create(['username' => '1001']);
        Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $this->artisan('attendance:auto-alpha')
            ->expectsOutput('Alpa otomatis dilewati karena hari ini bukan hari sekolah.')
            ->assertOk();

        $this->assertDatabaseCount('attendances', 0);
    }
}
