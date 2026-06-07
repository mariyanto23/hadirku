<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AcademicCalendar;
use App\Models\AcademicHoliday;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademicCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_academic_calendar(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.academic-calendar'));

        $response->assertOk();
        $response->assertSee('Kelola Hari Libur');

        Livewire::actingAs($admin)
            ->test(AcademicCalendar::class)
            ->call('openCreateModal')
            ->set('title', 'Libur Semester Genap')
            ->set('type', AcademicHoliday::TYPE_SEMESTER)
            ->set('start_date', today()->format('Y-m-d'))
            ->set('end_date', today()->addDays(3)->format('Y-m-d'))
            ->set('allow_attendance', false)
            ->set('notes', 'Akhir semester')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('academic_holidays', [
            'title' => 'Libur Semester Genap',
            'type' => AcademicHoliday::TYPE_SEMESTER,
            'allow_attendance' => false,
        ]);
    }

    public function test_academic_calendar_rejects_overlapping_date_ranges(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        AcademicHoliday::create([
            'title' => 'Libur Sekolah',
            'type' => AcademicHoliday::TYPE_SCHOOL,
            'start_date' => today(),
            'end_date' => today()->addDays(2),
            'allow_attendance' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(AcademicCalendar::class)
            ->set('title', 'Kegiatan Sekolah')
            ->set('type', AcademicHoliday::TYPE_EVENT)
            ->set('start_date', today()->addDay()->format('Y-m-d'))
            ->set('end_date', today()->addDays(4)->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['start_date']);

        $this->assertDatabaseMissing('academic_holidays', [
            'title' => 'Kegiatan Sekolah',
        ]);
    }

    public function test_admin_can_import_academic_holidays(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $csv = implode("\n", [
            'nama_libur,jenis,tanggal_mulai,tanggal_selesai,presensi,keterangan',
            'Libur Nasional,Libur Nasional,'.today()->addDays(10)->format('Y-m-d').','.today()->addDays(10)->format('Y-m-d').',Tutup,Contoh libur',
            'Kegiatan Sekolah,Kegiatan Sekolah,'.today()->addDays(12)->format('Y-m-d').','.today()->addDays(12)->format('Y-m-d').',Buka,Tetap presensi',
        ]);

        $file = UploadedFile::fake()->createWithContent('libur.csv', $csv);

        Livewire::actingAs($admin)
            ->test(AcademicCalendar::class)
            ->call('openImportModal')
            ->set('importFile', $file)
            ->assertSet('importValidCount', 2)
            ->assertSet('importInvalidCount', 0)
            ->call('importHolidays')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('academic_holidays', [
            'title' => 'Libur Nasional',
            'type' => AcademicHoliday::TYPE_NATIONAL,
            'allow_attendance' => false,
        ]);

        $this->assertDatabaseHas('academic_holidays', [
            'title' => 'Kegiatan Sekolah',
            'type' => AcademicHoliday::TYPE_EVENT,
            'allow_attendance' => true,
        ]);
    }

    public function test_academic_holiday_import_rejects_overlapping_rows(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $csv = implode("\n", [
            'nama_libur,jenis,tanggal_mulai,tanggal_selesai,presensi,keterangan',
            'Libur Pertama,Libur Sekolah,'.today()->addDays(20)->format('Y-m-d').','.today()->addDays(22)->format('Y-m-d').',Tutup,-',
            'Libur Kedua,Libur Semester,'.today()->addDays(21)->format('Y-m-d').','.today()->addDays(23)->format('Y-m-d').',Tutup,-',
        ]);

        $file = UploadedFile::fake()->createWithContent('libur-overlap.csv', $csv);

        Livewire::actingAs($admin)
            ->test(AcademicCalendar::class)
            ->call('openImportModal')
            ->set('importFile', $file)
            ->assertSet('importValidCount', 1)
            ->assertSet('importInvalidCount', 1)
            ->call('importHolidays')
            ->assertHasErrors(['importFile']);

        $this->assertDatabaseMissing('academic_holidays', [
            'title' => 'Libur Pertama',
        ]);
    }
}
