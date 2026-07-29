<?php

namespace App\Livewire\Admin;

use App\Exports\AttendanceExport;
use App\Exports\UnattendedStudentsExport;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReport extends Component
{
    use WithPagination;

    private const STATUS_UNATTENDED = 'belum_presensi';

    public $search = '';

    public $classFilter = '';

    public $statusFilter = '';

    public $approvalFilter = '';

    public $dateStartFilter = '';

    public $dateEndFilter = '';

    public $datePreset = 'this_month';

    public $mobileClassFilter = '';

    public $mobileStatusFilter = '';

    public $mobileApprovalFilter = '';

    public $mobileDateStartFilter = '';

    public $mobileDateEndFilter = '';

    public $mobileDatePreset = 'this_month';

    public $trendDateStartFilter = '';

    public $trendDateEndFilter = '';

    public $trendDatePreset = 'this_month';

    public bool $showMobileFilterModal = false;

    public ?int $selectedAttendanceId = null;

    public bool $showAttendanceDetailModal = false;

    public function mount(): void
    {
        $this->setDefaultClassFilter();

        $this->setDateRange(
            today()->startOfMonth()->format('Y-m-d'),
            today()->endOfMonth()->format('Y-m-d')
        );

        $this->applyTrendDatePreset('this_month');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        if ($this->isUnattendedMode()) {
            $this->approvalFilter = '';
            $this->mobileApprovalFilter = '';
        }
    }

    public function updatingApprovalFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateStartFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateEndFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDatePreset(): void
    {
        $this->resetPage();
    }

    public function updatedDatePreset(): void
    {
        $this->applyDatePreset($this->datePreset);
    }

    public function updatedMobileDatePreset(): void
    {
        $this->applyMobileDatePreset($this->mobileDatePreset);
    }

    public function updatedTrendDatePreset(): void
    {
        $this->applyTrendDatePreset($this->trendDatePreset);
    }

    public function updatedDateStartFilter(): void
    {
        $this->guardDesktopDateRange();
    }

    public function updatedDateEndFilter(): void
    {
        $this->guardDesktopDateRange();
    }

    public function updatedTrendDateStartFilter(): void
    {
        $this->trendDatePreset = 'custom';
        $this->guardTrendDateRange();
    }

    public function updatedTrendDateEndFilter(): void
    {
        $this->trendDatePreset = 'custom';
        $this->guardTrendDateRange();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'classFilter',
            'statusFilter',
            'approvalFilter',
            'dateStartFilter',
            'dateEndFilter',
            'datePreset',
            'mobileClassFilter',
            'mobileStatusFilter',
            'mobileApprovalFilter',
            'mobileDateStartFilter',
            'mobileDateEndFilter',
            'mobileDatePreset',
        ]);

        $this->setDefaultClassFilter();
        $this->applyDatePreset('this_month');

        $this->showMobileFilterModal = false;
        $this->showAttendanceDetailModal = false;
        $this->selectedAttendanceId = null;

        $this->resetPage();
    }

    public function openMobileFilters(): void
    {
        $this->showAttendanceDetailModal = false;
        $this->selectedAttendanceId = null;
        $this->mobileClassFilter = $this->classFilter;
        $this->mobileStatusFilter = $this->statusFilter;
        $this->mobileApprovalFilter = $this->approvalFilter;
        $this->mobileDateStartFilter = $this->dateStartFilter;
        $this->mobileDateEndFilter = $this->dateEndFilter;
        $this->mobileDatePreset = $this->datePreset;
        $this->showMobileFilterModal = true;
    }

    public function closeMobileFilters(): void
    {
        $this->showMobileFilterModal = false;
    }

    public function applyMobileFilters(): void
    {
        if (! $this->dateRangeIsValid(
            $this->mobileDateStartFilter,
            $this->mobileDateEndFilter
        )) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.'
            );

            return;
        }

        if ($this->mobileStatusFilter === self::STATUS_UNATTENDED) {
            $this->mobileApprovalFilter = '';
        }

        $this->classFilter = $this->mobileClassFilter;
        $this->statusFilter = $this->mobileStatusFilter;
        $this->approvalFilter = $this->mobileApprovalFilter;
        $this->dateStartFilter = $this->mobileDateStartFilter;
        $this->dateEndFilter = $this->mobileDateEndFilter;
        $this->datePreset = $this->mobileDatePreset;
        $this->showMobileFilterModal = false;

        $this->resetPage();
    }

    public function resetMobileFilters(): void
    {
        $this->reset([
            'classFilter',
            'statusFilter',
            'approvalFilter',
            'dateStartFilter',
            'dateEndFilter',
            'datePreset',
            'mobileClassFilter',
            'mobileStatusFilter',
            'mobileApprovalFilter',
            'mobileDateStartFilter',
            'mobileDateEndFilter',
            'mobileDatePreset',
        ]);

        $this->setDefaultClassFilter();
        $this->applyDatePreset('this_month');

        $this->showMobileFilterModal = false;

        $this->resetPage();
    }

    public function openAttendanceDetail(int $attendanceId): void
    {
        $this->showMobileFilterModal = false;
        $this->selectedAttendanceId = $attendanceId;
        $this->showAttendanceDetailModal = true;
    }

    public function closeAttendanceDetail(): void
    {
        $this->showAttendanceDetailModal = false;
        $this->selectedAttendanceId = null;
    }

    public function applyDatePreset(string $preset): void
    {
        $preset = $preset ?: 'this_month';

        $this->datePreset = $preset;
        $this->mobileDatePreset = $preset;

        match ($preset) {
            'today' => $this->setDateRange(
                today()->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'seven_days' => $this->setDateRange(
                today()->subDays(6)->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'this_month' => $this->setDateRange(
                today()->startOfMonth()->format('Y-m-d'),
                today()->endOfMonth()->format('Y-m-d')
            ),
            'custom' => null,
            default => null,
        };

        $this->resetPage();
    }

    public function applyMobileDatePreset(string $preset): void
    {
        $preset = $preset ?: 'this_month';

        $this->mobileDatePreset = $preset;

        match ($preset) {
            'today' => $this->setMobileDateRange(
                today()->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'seven_days' => $this->setMobileDateRange(
                today()->subDays(6)->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'this_month' => $this->setMobileDateRange(
                today()->startOfMonth()->format('Y-m-d'),
                today()->endOfMonth()->format('Y-m-d')
            ),
            'custom' => null,
            default => null,
        };
    }

    public function applyTrendDatePreset(string $preset): void
    {
        $preset = $preset ?: 'this_month';

        $this->trendDatePreset = $preset;

        match ($preset) {
            'seven_days' => $this->setTrendDateRange(
                today()->subDays(6)->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'fourteen_days' => $this->setTrendDateRange(
                today()->subDays(13)->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'thirty_days' => $this->setTrendDateRange(
                today()->subDays(29)->format('Y-m-d'),
                today()->format('Y-m-d')
            ),
            'this_month' => $this->setTrendDateRange(
                today()->startOfMonth()->format('Y-m-d'),
                today()->endOfMonth()->format('Y-m-d')
            ),
            'custom' => null,
            default => null,
        };
    }

    public function exportExcel()
    {
        if (! $this->ensureExportableDateRange()) {
            return null;
        }

        if ($this->isUnattendedMode()) {
            return Excel::download(
                new UnattendedStudentsExport(
                    $this->unattendedStudentQuery()->get(),
                    $this->unattendedDate()
                ),
                $this->exportFileName('xlsx')
            );
        }

        return Excel::download(
            new AttendanceExport(
                $this->search,
                $this->classFilter,
                $this->statusFilter,
                $this->dateStartFilter,
                $this->dateEndFilter,
                $this->approvalFilter
            ),
            $this->exportFileName('xlsx')
        );
    }

    public function exportPdf()
    {
        if (! $this->ensureExportableDateRange()) {
            return null;
        }

        $classes = $this->classes();

        if ($this->isUnattendedMode()) {
            $students = $this->unattendedStudentQuery()->get();

            $pdf = Pdf::loadView('exports.attendance-report-pdf', [
                'attendances' => collect(),
                'unattendedStudents' => $students,
                'unattendedMode' => true,
                'unattendedDate' => Carbon::parse($this->unattendedDate())->translatedFormat('d F Y'),
                'filters' => $this->activeFilters($classes),
                'generatedAt' => now()->translatedFormat('d F Y H:i'),
            ])->setPaper('a4', 'landscape');

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $this->exportFileName('pdf'));
        }

        $attendances = $this->attendanceQuery()
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.attendance-report-pdf', [
            'attendances' => $attendances,
            'filters' => $this->activeFilters($classes),
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $this->exportFileName('pdf'));
    }

    private function attendanceQuery(bool $withRelations = true)
    {
        $query = Attendance::query();

        if ($withRelations) {
            $query->with([
                'student.user',
                'student.class',
            ]);
        }

        return $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->whereHas(
                    'student.user',
                    function ($userQuery) {
                        $userQuery->where(
                            'name',
                            'like',
                            '%'.$this->search.'%'
                        );
                    }
                )->orWhereHas(
                    'student',
                    function ($studentQuery) {
                        $studentQuery->where(
                            'nis',
                            'like',
                            '%'.$this->search.'%'
                        );
                    }
                );
            });
        })
            ->when($this->classFilter, function ($query) {
                $query->whereHas(
                    'student',
                    function ($q) {
                        $q->where(
                            'class_id',
                            $this->classFilter
                        );
                    }
                );
            })
            ->when($this->attendanceStatusFilter(), function ($query) {
                $query->where(
                    'status',
                    $this->attendanceStatusFilter()
                );
            })
            ->when($this->approvalFilter, function ($query) {
                $query->where(
                    'approval_status',
                    $this->approvalFilter
                );
            })
            ->when($this->dateStartFilter, function ($query) {
                $query->whereDate(
                    'attendance_date',
                    '>=',
                    $this->dateStartFilter
                );
            })
            ->when($this->dateEndFilter, function ($query) {
                $query->whereDate(
                    'attendance_date',
                    '<=',
                    $this->dateEndFilter
                );
            });
    }

    private function classes()
    {
        return SchoolClass::query()
            ->orderBy('name')
            ->get();
    }

    private function setDefaultClassFilter(): void
    {
        $this->classFilter = '';
        $this->mobileClassFilter = $this->classFilter;
    }

    private function isUnattendedMode(): bool
    {
        return $this->statusFilter === self::STATUS_UNATTENDED;
    }

    private function attendanceStatusFilter(): string
    {
        return $this->isUnattendedMode() ? '' : $this->statusFilter;
    }

    private function unattendedDate(): string
    {
        $today = today()->toDateString();
        $start = $this->dateStartFilter;
        $end = $this->dateEndFilter;

        if ($start && $end && $start === $end) {
            return $start;
        }

        if ($start && $today < $start) {
            return $start;
        }

        if ($end && $today > $end) {
            return $end;
        }

        return $today;
    }

    private function unattendedStudentQuery()
    {
        $attendanceDate = $this->unattendedDate();

        return Student::query()
            ->with(['user', 'class'])
            ->withCount('descriptors')
            ->when($this->classFilter, function ($query) {
                $query->where('class_id', $this->classFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%');
                    })->orWhere('nis', 'like', '%'.$this->search.'%');
                });
            })
            ->whereDoesntHave('attendances', function ($query) use ($attendanceDate) {
                $query->whereDate('attendance_date', $attendanceDate);
            })
            ->orderBy('class_id')
            ->orderBy('nis');
    }

    private function dateRangeIsValid(?string $start, ?string $end): bool
    {
        return ! ($start && $end && $start > $end);
    }

    private function setDateRange(string $start, string $end): void
    {
        $this->dateStartFilter = $start;
        $this->dateEndFilter = $end;
        $this->mobileDateStartFilter = $start;
        $this->mobileDateEndFilter = $end;
        $this->mobileDatePreset = $this->datePreset;
    }

    private function setMobileDateRange(string $start, string $end): void
    {
        $this->mobileDateStartFilter = $start;
        $this->mobileDateEndFilter = $end;
    }

    private function setTrendDateRange(string $start, string $end): void
    {
        $this->trendDateStartFilter = $start;
        $this->trendDateEndFilter = $end;
    }

    private function guardDesktopDateRange(): void
    {
        if ($this->dateRangeIsValid(
            $this->dateStartFilter,
            $this->dateEndFilter
        )) {
            return;
        }

        $this->dateEndFilter = '';

        $this->dispatch(
            'hadirku-toast',
            type: 'error',
            message: 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.'
        );
    }

    private function guardTrendDateRange(): void
    {
        if ($this->dateRangeIsValid(
            $this->trendDateStartFilter,
            $this->trendDateEndFilter
        )) {
            return;
        }

        $this->trendDateEndFilter = '';

        $this->dispatch(
            'hadirku-toast',
            type: 'error',
            message: 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.'
        );
    }

    private function ensureExportableDateRange(): bool
    {
        if ($this->dateRangeIsValid(
            $this->dateStartFilter,
            $this->dateEndFilter
        )) {
            return true;
        }

        $this->dispatch(
            'hadirku-toast',
            type: 'error',
            message: 'Perbaiki rentang tanggal sebelum mengekspor data.'
        );

        return false;
    }

    private function activeFilters($classes): array
    {
        $filters = [];

        if ($this->classFilter) {
            $filters[] = 'Kelas: '.($classes->firstWhere('id', (int) $this->classFilter)?->name ?? '-');
        }

        if ($this->statusFilter) {
            $filters[] = 'Status: '.match ($this->statusFilter) {
                'hadir' => 'Hadir',
                'terlambat' => 'Terlambat',
                'izin' => 'Izin',
                'sakit' => 'Sakit',
                'alpha' => 'Alpa',
                self::STATUS_UNATTENDED => 'Belum Presensi',
                default => '-',
            };
        }

        if ($this->isUnattendedMode()) {
            $filters[] = 'Tanggal acuan: '.Carbon::parse($this->unattendedDate())->translatedFormat('d F Y');
        }

        if ($this->approvalFilter) {
            $filters[] = 'Persetujuan: '.match ($this->approvalFilter) {
                'pending' => 'Menunggu',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => '-',
            };
        }

        if ($this->dateStartFilter || $this->dateEndFilter) {
            $filters[] = 'Tanggal: '
                .($this->dateStartFilter ?: 'Awal')
                .' - '
                .($this->dateEndFilter ?: 'Akhir');
        }

        if ($this->search) {
            $filters[] = 'Pencarian: '.$this->search;
        }

        return $filters;
    }

    private function attendanceTrend(): array
    {
        $start = $this->trendDateStartFilter ?: today()->subDays(6)->format('Y-m-d');
        $end = $this->trendDateEndFilter ?: today()->format('Y-m-d');

        $rows = Attendance::query()
            ->selectRaw('DATE(attendance_date) as attendance_day, status, COUNT(*) as total')
            ->whereDate('attendance_date', '>=', $start)
            ->whereDate('attendance_date', '<=', $end)
            ->groupBy('attendance_day', 'status')
            ->orderBy('attendance_day')
            ->get();

        $statuses = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        $daily = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $daily[$date->format('Y-m-d')] = $statuses;
        }

        foreach ($rows as $row) {
            $day = Carbon::parse($row->attendance_day)->format('Y-m-d');
            $daily[$day] ??= $statuses;
            $daily[$day][$row->status] = (int) $row->total;
        }

        ksort($daily);

        $points = collect($daily)
            ->map(function (array $counts, string $day) {
                $total = array_sum($counts);

                return [
                    'date' => $day,
                    'label' => Carbon::parse($day)->translatedFormat('d M'),
                    'hadir' => $counts['hadir'],
                    'terlambat' => $counts['terlambat'],
                    'izin' => $counts['izin'],
                    'sakit' => $counts['sakit'],
                    'tidak_hadir' => $counts['alpha'],
                    'total' => $total,
                ];
            })
            ->values();

        $seriesKeys = ['hadir', 'terlambat', 'izin', 'sakit', 'tidak_hadir'];
        $activeDays = $points->filter(fn ($point) => $point['total'] > 0)->count();
        $maxValue = $points
            ->flatMap(fn ($point) => collect($seriesKeys)->map(fn ($key) => $point[$key]))
            ->max();

        return [
            'points' => $points->all(),
            'max' => max(1, $maxValue),
            'startLabel' => Carbon::parse($start)->translatedFormat('j F Y'),
            'endLabel' => Carbon::parse($end)->translatedFormat('j F Y'),
            'activeDays' => $activeDays,
            'seriesTotals' => [
                'hadir' => $points->sum('hadir'),
                'terlambat' => $points->sum('terlambat'),
                'izin' => $points->sum('izin'),
                'sakit' => $points->sum('sakit'),
                'tidak_hadir' => $points->sum('tidak_hadir'),
            ],
            'rankings' => $this->attendanceTrendRankings($start, $end),
        ];
    }

    private function attendanceTrendRankings(string $start, string $end): array
    {
        $diligent = Student::query()
            ->with(['user', 'class'])
            ->withCount([
                'attendances as attendance_days_count' => function ($query) use ($start, $end) {
                    $query
                        ->whereDate('attendance_date', '>=', $start)
                        ->whereDate('attendance_date', '<=', $end)
                        ->where('status', 'hadir');
                },
            ])
            ->orderByDesc('attendance_days_count')
            ->orderBy('nis')
            ->limit(3)
            ->get()
            ->filter(fn ($student) => $student->attendance_days_count > 0)
            ->values();

        $oftenAbsent = Student::query()
            ->with(['user', 'class'])
            ->withCount([
                'attendances as absence_days_count' => function ($query) use ($start, $end) {
                    $query
                        ->whereDate('attendance_date', '>=', $start)
                        ->whereDate('attendance_date', '<=', $end)
                        ->where('status', 'alpha');
                },
            ])
            ->orderByDesc('absence_days_count')
            ->orderBy('nis')
            ->limit(5)
            ->get()
            ->filter(fn ($student) => $student->absence_days_count > 0)
            ->values();

        return [
            'diligent' => $diligent,
            'oftenAbsent' => $oftenAbsent,
        ];
    }

    private function exportFileName(string $extension): string
    {
        $parts = ['rekap-presensi'];

        if ($this->dateStartFilter || $this->dateEndFilter) {
            $parts[] = ($this->dateStartFilter ?: 'awal')
                .'-sampai-'
                .($this->dateEndFilter ?: 'akhir');
        } else {
            $parts[] = now()->format('Y-m-d');
        }

        return implode('-', $parts).'.'.$extension;
    }

    public function render()
    {
        $classes = $this->classes();
        $isUnattendedMode = $this->isUnattendedMode();
        $summaryQuery = $this->attendanceQuery();
        $attendances = $isUnattendedMode
            ? $this->unattendedStudentQuery()->paginate(10)
            : $this->attendanceQuery()
                ->latest()
                ->paginate(10);
        $selectedAttendance = (! $isUnattendedMode && $this->selectedAttendanceId)
            ? Attendance::query()
                ->with([
                    'student.user',
                    'student.class',
                ])
                ->find($this->selectedAttendanceId)
            : null;

        return view('livewire.admin.attendance-report', [

            'classes' => $classes,

            'attendances' => $attendances,

            'resultText' => $isUnattendedMode
                ? $attendances->total().' siswa belum presensi'
                : $attendances->total().' data ditemukan',

            'summary' => [
                'total' => $isUnattendedMode ? $attendances->total() : (clone $summaryQuery)->count(),
                'present' => $isUnattendedMode ? 0 : (clone $summaryQuery)->where('status', 'hadir')->count(),
                'late' => $isUnattendedMode ? 0 : (clone $summaryQuery)->where('status', 'terlambat')->count(),
                'absent' => $isUnattendedMode ? 0 : (clone $summaryQuery)->where('status', 'alpha')->count(),
            ],

            'activeFilterCount' => collect([
                $this->classFilter,
                $this->statusFilter,
                $this->approvalFilter,
                ($this->dateStartFilter || $this->dateEndFilter) ? 'date' : '',
            ])->filter()->count(),

            'selectedAttendance' => $selectedAttendance,

            'activeFilters' => $this->activeFilters($classes),

            'attendanceTrend' => $this->attendanceTrend(),

            'isUnattendedMode' => $isUnattendedMode,

            'unattendedDateLabel' => Carbon::parse($this->unattendedDate())->translatedFormat('d F Y'),

        ]);
    }
}
