<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        public $search,
        public $classFilter,
        public $statusFilter,
        public $dateStartFilter,
        public $dateEndFilter,
        public $approvalFilter = ''
    ) {}

    public function collection()
    {
        return Attendance::query()
            ->with([
                'student.user',
                'student.class',
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('student.user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%');
                    })->orWhereHas('student', function ($studentQuery) {
                        $studentQuery->where('nis', 'like', '%'.$this->search.'%');
                    });
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('class_id', $this->classFilter);
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateStartFilter, function ($query) {
                $query->whereDate('attendance_date', '>=', $this->dateStartFilter);
            })
            ->when($this->dateEndFilter, function ($query) {
                $query->whereDate('attendance_date', '<=', $this->dateEndFilter);
            })
            ->when($this->approvalFilter, function ($query) {
                $query->where('approval_status', $this->approvalFilter);
            })
            ->latest()
            ->get()
            ->map(function ($attendance) {

                return [
                    'Nama' => $attendance->student->user->name,

                    'NIS' => $attendance->student->nis,

                    'Kelas' => $attendance->student->class->name,

                    'Tanggal' => $attendance->attendance_date?->translatedFormat('d F Y'),

                    'Jam' => substr((string) $attendance->attendance_time, 0, 5),

                    'Status' => match ($attendance->status) {
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpa',
                        default => '-',
                    },

                    'Persetujuan' => match ($attendance->approval_status) {
                        'pending' => 'Menunggu',
                        'rejected' => 'Ditolak',
                        default => 'Disetujui',
                    },

                    'Keterangan' => $attendance->notes ?: '-',

                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIS',
            'Kelas',
            'Tanggal',
            'Jam',
            'Status',
            'Persetujuan',
            'Keterangan',
        ];
    }
}
