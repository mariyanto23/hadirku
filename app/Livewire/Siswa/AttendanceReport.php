<?php

namespace App\Livewire\Siswa;

use App\Models\Attendance;
use App\Models\AcademicHoliday;
use App\Models\AttendanceSetting;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceReport extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->statusFilter = '';
        $this->resetPage();
    }

    private function attendanceQuery(bool $withRelations = true)
    {
        $student = auth()->user()?->student;

        $query = Attendance::query();

        if ($withRelations) {
            $query->with('student.class');
        }

        return $query
            ->when($student, function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->whereDate('attendance_date', '>=', today()->startOfMonth())
            ->whereDate('attendance_date', '<=', today()->endOfMonth())
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            });
    }

    private function summaryQuery()
    {
        $student = auth()->user()?->student;

        return Attendance::query()
            ->when($student, function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->whereDate('attendance_date', '>=', today()->startOfMonth())
            ->whereDate('attendance_date', '<=', today()->endOfMonth());
    }

    public function render()
    {
        $summaryQuery = $this->summaryQuery();
        $attendances = $this->attendanceQuery()
            ->latest()
            ->paginate(10);

        return view('livewire.siswa.attendance-report', [
            'student' => auth()->user()?->student?->load('class'),
            'attendances' => $attendances,
            'resultText' => $attendances->total().' data ditemukan',
            'monthLabel' => today()->translatedFormat('F Y'),
            'summary' => [
                'present' => (clone $summaryQuery)->where('status', 'hadir')->count(),
                'late' => (clone $summaryQuery)->where('status', 'terlambat')->count(),
                'leave' => (clone $summaryQuery)->whereIn('status', ['izin', 'sakit'])->count(),
                'absent' => (clone $summaryQuery)->where('status', 'alpha')->count(),
            ],
            'calendar' => $this->attendanceCalendar(),
        ]);
    }

    private function attendanceCalendar(): array
    {
        $start = today()->startOfMonth();
        $end = today()->endOfMonth();
        $settings = AttendanceSetting::current();

        $attendances = $this->summaryQuery()
            ->latest()
            ->get()
            ->keyBy(fn ($attendance) => $attendance->attendance_date?->format('Y-m-d'));

        $holidaysByDate = [];

        AcademicHoliday::query()
            ->overlapping($start, $end)
            ->orderBy('start_date')
            ->get()
            ->each(function (AcademicHoliday $holiday) use (&$holidaysByDate, $start, $end) {
                $cursor = $holiday->start_date->copy()->max($start);
                $holidayEnd = $holiday->end_date->copy()->min($end);

                while ($cursor->lessThanOrEqualTo($holidayEnd)) {
                    $holidaysByDate[$cursor->format('Y-m-d')] = [
                        'title' => $holiday->title,
                        'allow_attendance' => (bool) $holiday->allow_attendance,
                    ];

                    $cursor->addDay();
                }
            });

        $cells = [];

        if ($start->dayOfWeekIso > 1) {
            foreach (range(1, $start->dayOfWeekIso - 1) as $unused) {
                $cells[] = ['blank' => true];
            }
        }

        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            $date = $cursor->format('Y-m-d');
            $attendance = $attendances->get($date);
            $holiday = $holidaysByDate[$date] ?? null;

            $cells[] = [
                'blank' => false,
                'date' => $date,
                'day' => $cursor->format('j'),
                'is_today' => $cursor->isToday(),
                'status' => $attendance?->status,
                'approval_status' => $attendance?->approval_status,
                'holiday' => $holiday,
                'is_school_day' => $settings->isSchoolDay($cursor),
            ];

            $cursor->addDay();
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = ['blank' => true];
        }

        return [
            'weeks' => array_chunk($cells, 7),
            'total' => $attendances->count(),
        ];
    }
}
