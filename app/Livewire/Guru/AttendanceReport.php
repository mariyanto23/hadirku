<?php

namespace App\Livewire\Guru;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\SchoolClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReport extends Component
{
    use WithPagination;

    public string $search = '';

    public string $classFilter = '';

    public string $statusFilter = '';

    public string $approvalFilter = '';

    public string $dateStartFilter = '';

    public string $dateEndFilter = '';

    public string $datePreset = 'this_month';

    public string $mobileClassFilter = '';

    public string $mobileStatusFilter = '';

    public string $mobileApprovalFilter = '';

    public string $mobileDateStartFilter = '';

    public string $mobileDateEndFilter = '';

    public string $mobileDatePreset = 'this_month';

    public bool $showMobileFilterModal = false;

    public ?int $selectedAttendanceId = null;

    public bool $showAttendanceDetailModal = false;

    public function mount(): void
    {
        $this->setDefaultClassFilter();
        $this->applyDatePreset('this_month');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function updatingApprovalFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateStartFilter(): void
    {
        $this->datePreset = 'custom';
        $this->guardDesktopDateRange();
        $this->resetPage();
    }

    public function updatedDateEndFilter(): void
    {
        $this->datePreset = 'custom';
        $this->guardDesktopDateRange();
        $this->resetPage();
    }

    public function updatedMobileDatePreset(): void
    {
        $this->applyMobileDatePreset($this->mobileDatePreset);
    }

    public function applyDatePreset(string $preset): void
    {
        $preset = $preset ?: 'this_month';
        $this->datePreset = $preset;
        $this->mobileDatePreset = $preset;

        match ($preset) {
            'today' => $this->setDateRange(today()->format('Y-m-d'), today()->format('Y-m-d')),
            'seven_days' => $this->setDateRange(today()->subDays(6)->format('Y-m-d'), today()->format('Y-m-d')),
            'this_month' => $this->setDateRange(today()->startOfMonth()->format('Y-m-d'), today()->endOfMonth()->format('Y-m-d')),
            'custom' => null,
            default => null,
        };

        $this->resetPage();
    }

    public function openMobileFilters(): void
    {
        $this->closeAttendanceDetail();
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
        if (! $this->dateRangeIsValid($this->mobileDateStartFilter, $this->mobileDateEndFilter)) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.'
            );

            return;
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
        $this->closeMobileFilters();
        $this->closeAttendanceDetail();
    }

    public function resetMobileFilters(): void
    {
        $this->resetFilters();
    }

    public function applyMobileDatePreset(string $preset): void
    {
        $preset = $preset ?: 'this_month';
        $this->mobileDatePreset = $preset;

        match ($preset) {
            'today' => $this->setMobileDateRange(today()->format('Y-m-d'), today()->format('Y-m-d')),
            'seven_days' => $this->setMobileDateRange(today()->subDays(6)->format('Y-m-d'), today()->format('Y-m-d')),
            'this_month' => $this->setMobileDateRange(today()->startOfMonth()->format('Y-m-d'), today()->endOfMonth()->format('Y-m-d')),
            'custom' => null,
            default => null,
        };
    }

    public function openAttendanceDetail(int $attendanceId): void
    {
        $this->closeMobileFilters();
        $this->selectedAttendanceId = $attendanceId;
        $this->showAttendanceDetailModal = true;
    }

    public function closeAttendanceDetail(): void
    {
        $this->showAttendanceDetailModal = false;
        $this->selectedAttendanceId = null;
    }

    public function exportExcel()
    {
        if (! $this->ensureExportableDateRange()) {
            return null;
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

        $attendances = $this->attendanceQuery()
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.attendance-report-pdf', [
            'attendances' => $attendances,
            'filters' => $this->activeFilters($this->classes()),
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
            $query->with(['student.user', 'student.class']);
        }

        return $query
            ->when($this->classFilter, function ($query) {
                $query->whereHas('student', function ($studentQuery) {
                    $studentQuery->where('class_id', $this->classFilter);
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('student.user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%');
                    })->orWhereHas('student', function ($studentQuery) {
                        $studentQuery->where('nis', 'like', '%'.$this->search.'%');
                    });
                });
            })
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->approvalFilter, fn ($query) => $query->where('approval_status', $this->approvalFilter))
            ->when($this->dateStartFilter, fn ($query) => $query->whereDate('attendance_date', '>=', $this->dateStartFilter))
            ->when($this->dateEndFilter, fn ($query) => $query->whereDate('attendance_date', '<=', $this->dateEndFilter));
    }

    private function activeFilters($classes): array
    {
        $filters = [];

        if ($this->classFilter) {
            $filters[] = 'Kelas: '.($classes->firstWhere('id', (int) $this->classFilter)?->name ?? '-');
        }

        if ($this->statusFilter) {
            $filters[] = 'Status: '.$this->statusLabel($this->statusFilter);
        }

        if ($this->approvalFilter) {
            $filters[] = 'Persetujuan: '.$this->approvalLabel($this->approvalFilter);
        }

        if ($this->dateStartFilter || $this->dateEndFilter) {
            $filters[] = 'Tanggal: '.($this->dateStartFilter ?: 'Awal').' - '.($this->dateEndFilter ?: 'Akhir');
        }

        if ($this->search) {
            $filters[] = 'Pencarian: '.$this->search;
        }

        return $filters;
    }

    private function setDateRange(string $start, string $end): void
    {
        $this->dateStartFilter = $start;
        $this->dateEndFilter = $end;
        $this->mobileDateStartFilter = $start;
        $this->mobileDateEndFilter = $end;
    }

    private function setMobileDateRange(string $start, string $end): void
    {
        $this->mobileDateStartFilter = $start;
        $this->mobileDateEndFilter = $end;
    }

    private function guardDesktopDateRange(): void
    {
        if ($this->dateRangeIsValid($this->dateStartFilter, $this->dateEndFilter)) {
            return;
        }

        $this->dateEndFilter = '';
        $this->dispatch(
            'hadirku-toast',
            type: 'error',
            message: 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.'
        );
    }

    private function ensureExportableDateRange(): bool
    {
        if ($this->dateRangeIsValid($this->dateStartFilter, $this->dateEndFilter)) {
            return true;
        }

        $this->dispatch(
            'hadirku-toast',
            type: 'error',
            message: 'Perbaiki rentang tanggal sebelum mengekspor data.'
        );

        return false;
    }

    private function dateRangeIsValid(?string $start, ?string $end): bool
    {
        return ! ($start && $end && $start > $end);
    }

    private function classes()
    {
        return SchoolClass::query()
            ->orderBy('name')
            ->get();
    }

    private function setDefaultClassFilter(): void
    {
        $this->classFilter = (string) (auth()->user()?->default_class_id ?: '');
        $this->mobileClassFilter = $this->classFilter;
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpa',
            default => '-',
        };
    }

    private function approvalLabel(?string $approval): string
    {
        return match ($approval) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => '-',
        };
    }

    private function exportFileName(string $extension): string
    {
        $parts = ['rekap-presensi-guru'];

        if ($this->classFilter) {
            $class = $this->classes()->firstWhere('id', (int) $this->classFilter);
            $parts[] = str($class?->name ?: 'kelas')->slug()->toString();
        }

        $parts[] = $this->dateStartFilter || $this->dateEndFilter
            ? ($this->dateStartFilter ?: 'awal').'-sampai-'.($this->dateEndFilter ?: 'akhir')
            : now()->format('Y-m-d');

        return implode('-', $parts).'.'.$extension;
    }

    public function render()
    {
        $summaryQuery = $this->attendanceQuery();
        $classes = $this->classes();
        $attendances = $this->attendanceQuery()
            ->latest()
            ->paginate(10);
        $selectedAttendance = $this->selectedAttendanceId
            ? $this->attendanceQuery()->find($this->selectedAttendanceId)
            : null;

        return view('livewire.guru.attendance-report', [
            'classes' => $classes,
            'attendances' => $attendances,
            'resultText' => $attendances->total().' data ditemukan',
            'summary' => [
                'total' => (clone $summaryQuery)->count(),
                'present' => (clone $summaryQuery)->where('status', 'hadir')->count(),
                'late' => (clone $summaryQuery)->where('status', 'terlambat')->count(),
                'absent' => (clone $summaryQuery)->where('status', 'alpha')->count(),
            ],
            'defaultClass' => auth()->user()?->defaultClass,
            'selectedClass' => $this->classFilter
                ? $classes->firstWhere('id', (int) $this->classFilter)
                : null,
            'activeFilterCount' => collect([
                $this->classFilter,
                $this->statusFilter,
                $this->approvalFilter,
                ($this->dateStartFilter || $this->dateEndFilter) ? 'date' : '',
            ])->filter()->count(),
            'activeFilters' => $this->activeFilters($classes),
            'selectedAttendance' => $selectedAttendance,
        ]);
    }
}
