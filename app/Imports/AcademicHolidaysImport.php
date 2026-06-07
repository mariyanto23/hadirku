<?php

namespace App\Imports;

use App\Models\AcademicHoliday;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AcademicHolidaysImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    public int $skipped = 0;

    public int $valid = 0;

    public int $invalid = 0;

    public array $previewRows = [];

    public array $validRows = [];

    public array $errors = [];

    public function __construct(
        private bool $persist = true
    ) {}

    public function collection(Collection $rows): void
    {
        $seenRanges = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = [];

            $title = $this->cleanCell($row['nama_libur'] ?? $row['nama'] ?? $row['judul'] ?? null);
            $type = $this->normalizeType($this->cleanCell($row['jenis'] ?? null));
            $startDate = $this->normalizeDate($row['tanggal_mulai'] ?? $row['mulai'] ?? $row['tanggal'] ?? null);
            $endDate = $this->normalizeDate($row['tanggal_selesai'] ?? $row['selesai'] ?? $row['tanggal'] ?? null);
            $allowAttendance = $this->normalizeAttendance($this->cleanCell($row['presensi'] ?? null));
            $notes = $this->cleanCell($row['keterangan'] ?? $row['catatan'] ?? null);

            if ($title === '' && $type === '' && $startDate === '' && $endDate === '' && $notes === '') {
                $this->skipped++;
                continue;
            }

            if ($title === '') {
                $rowErrors[] = "Baris {$rowNumber}: nama libur wajib diisi.";
            }

            if ($type === '') {
                $rowErrors[] = "Baris {$rowNumber}: jenis libur tidak valid.";
            }

            if ($startDate === '') {
                $rowErrors[] = "Baris {$rowNumber}: tanggal mulai wajib diisi dengan format YYYY-MM-DD.";
            }

            if ($endDate === '') {
                $rowErrors[] = "Baris {$rowNumber}: tanggal selesai wajib diisi dengan format YYYY-MM-DD.";
            }

            if ($startDate !== '' && $endDate !== '' && $startDate > $endDate) {
                $rowErrors[] = "Baris {$rowNumber}: tanggal selesai harus sama dengan atau setelah tanggal mulai.";
            }

            if ($startDate !== '' && $endDate !== '') {
                foreach ($seenRanges as $seenRange) {
                    if ($startDate <= $seenRange['end_date'] && $endDate >= $seenRange['start_date']) {
                        $rowErrors[] = "Baris {$rowNumber}: rentang tanggal bertumpang tindih dengan baris {$seenRange['row']}.";
                        break;
                    }
                }

                $overlap = AcademicHoliday::query()
                    ->overlapping($startDate, $endDate)
                    ->first();

                if ($overlap) {
                    $rowErrors[] = "Baris {$rowNumber}: rentang tanggal bertumpang tindih dengan {$overlap->title}.";
                }
            }

            if ($rowErrors) {
                array_push($this->errors, ...$rowErrors);

                $this->invalid++;

                $this->previewRows[] = [
                    'row' => $rowNumber,
                    'title' => $title,
                    'type' => $type ? AcademicHoliday::types()[$type] : '-',
                    'start_date' => $startDate ?: '-',
                    'end_date' => $endDate ?: '-',
                    'allow_attendance' => $allowAttendance ? 'Dibuka' : 'Ditutup',
                    'valid' => false,
                    'status' => implode(' ', array_map(
                        fn (string $error) => Str::after($error, ': '),
                        $rowErrors
                    )),
                ];

                continue;
            }

            $validRow = [
                'title' => $title,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'allow_attendance' => $allowAttendance,
                'notes' => $notes,
            ];

            $this->validRows[] = $validRow;
            $this->valid++;

            $seenRanges[] = [
                'row' => $rowNumber,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];

            $this->previewRows[] = [
                'row' => $rowNumber,
                'title' => $title,
                'type' => AcademicHoliday::types()[$type],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'allow_attendance' => $allowAttendance ? 'Dibuka' : 'Ditutup',
                'valid' => true,
                'status' => 'OK',
            ];
        }

        if (! $this->persist) {
            return;
        }

        if ($this->errors) {
            throw ValidationException::withMessages([
                'importFile' => array_slice($this->errors, 0, 10),
            ]);
        }

        DB::transaction(function () {
            foreach ($this->validRows as $row) {
                AcademicHoliday::query()->create($row);
                $this->created++;
            }
        });
    }

    private function cleanCell($value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function normalizeDate($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                )->format('Y-m-d');
            }

            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return '';
        }
    }

    private function normalizeType(string $value): string
    {
        $normalized = Str::lower(trim($value));
        $normalized = str_replace(['_', '-'], ' ', $normalized);

        return match ($normalized) {
            'national', 'nasional', 'libur nasional' => AcademicHoliday::TYPE_NATIONAL,
            'semester', 'libur semester' => AcademicHoliday::TYPE_SEMESTER,
            'school', 'sekolah', 'libur sekolah' => AcademicHoliday::TYPE_SCHOOL,
            'event', 'kegiatan', 'kegiatan sekolah' => AcademicHoliday::TYPE_EVENT,
            'other', 'lainnya', 'lain lain', 'lain-lain' => AcademicHoliday::TYPE_OTHER,
            default => '',
        };
    }

    private function normalizeAttendance(string $value): bool
    {
        $normalized = Str::lower(trim($value));

        return in_array($normalized, [
            'buka',
            'dibuka',
            'ya',
            'y',
            'yes',
            '1',
            'true',
            'presensi buka',
        ], true);
    }
}
