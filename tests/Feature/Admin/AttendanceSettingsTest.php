<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AttendanceSettings;
use App\Models\AttendanceSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_school_days(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.attendance.settings'));

        $response->assertOk();
        $response->assertSee('Hari Sekolah');

        Livewire::actingAs($admin)
            ->test(AttendanceSettings::class)
            ->set('face_match_threshold', 0.5)
            ->set('scan_interval', 1000)
            ->set('attendance_start_time', '06:30')
            ->set('late_after', '07:00')
            ->set('max_descriptors', 10)
            ->set('auto_alpha', true)
            ->set('school_days', [1, 2, 3, 4, 5])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            [1, 2, 3, 4, 5],
            AttendanceSetting::current()->fresh()->normalizedSchoolDays()
        );
    }
}
