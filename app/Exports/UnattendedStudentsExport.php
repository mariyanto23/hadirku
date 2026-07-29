<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnattendedStudentsExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        private Collection $students,
        private string $attendanceDate
    ) {}

    public function collection(): Collection
    {
        return $this->students->map(function ($student) {
            return [
                'Nama' => $student->user->name,
                'NIS' => $student->nis,
                'Kelas' => $student->class?->name ?? '-',
                'Tanggal Acuan' => Carbon::parse($this->attendanceDate)->translatedFormat('d F Y'),
                'Status' => 'Belum Presensi',
                'Data Wajah' => $student->descriptors_count.' descriptor',
                'Keterangan' => $student->descriptors_count >= 3
                    ? 'Siap dipindai'
                    : 'Data wajah belum lengkap',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIS',
            'Kelas',
            'Tanggal Acuan',
            'Status',
            'Data Wajah',
            'Keterangan',
        ];
    }
}
