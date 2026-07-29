<?php

namespace Tests\Feature\Siswa;

use App\Livewire\Siswa\LeaveRequest;
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

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_leave_request_with_optional_attachment(): void
    {
        Storage::fake('public');

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

        $file = UploadedFile::fake()->create(
            'surat-sakit.pdf',
            128,
            'application/pdf'
        );

        Livewire::actingAs($user)
            ->test(LeaveRequest::class)
            ->set('attendance_date', today()->toDateString())
            ->set('status', 'sakit')
            ->set('notes', 'Sakit demam dan perlu istirahat.')
            ->set('attachment', $file)
            ->call('submit')
            ->assertHasNoErrors();

        $attendance = Attendance::query()->firstOrFail();

        $this->assertSame('surat-sakit.pdf', $attendance->attachment_name);
        $this->assertNotNull($attendance->attachment_path);
        Storage::disk('public')->assertExists($attendance->attachment_path);
    }
}
