<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\AcademicHoliday;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoAlphaAttendance extends Command
{
    protected $signature = 'attendance:auto-alpha';

    protected $description =
    'Membuat presensi alpa otomatis';

    public function handle(): void
    {
        if (! AttendanceSetting::current()->auto_alpha) {
            $this->info('Alpa otomatis tidak aktif.');

            return;
        }

        $blockingHoliday = AcademicHoliday::blockingAttendanceOn(today());

        if ($blockingHoliday) {
            $this->info('Alpa otomatis dilewati karena hari ini libur: '.$blockingHoliday->title.'.');

            return;
        }

        if (! AttendanceSetting::current()->isSchoolDay(today()) && ! AcademicHoliday::allowingAttendanceOn(today())) {
            $this->info('Alpa otomatis dilewati karena hari ini bukan hari sekolah.');

            return;
        }

        $classes = SchoolClass::query()
            ->with(['students', 'schedule'])
            ->get();

        foreach ($classes as $class) {

            if (!$class->schedule) {
                continue;
            }

            $endTime = Carbon::parse(
                $class->schedule->end_time
            );

            if (now()->lt($endTime)) {
                continue;
            }

            foreach ($class->students as $student) {

                $alreadyExists = Attendance::query()
                    ->where('student_id', $student->id)
                    ->whereDate(
                        'attendance_date',
                        today()
                    )
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'attendance_date' => today(),
                    'attendance_time' => now()->format('H:i:s'),
                    'status' => 'alpha',
                    'notes' => 'Alpa otomatis setelah jam kelas selesai.',
                    'approval_status' => 'approved',
                ]);
            }
        }

        $this->info('Alpa otomatis selesai.');
    }
}
