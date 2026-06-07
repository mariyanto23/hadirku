<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

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
        $classIdsByName = SchoolClass::query()
            ->get()
            ->mapWithKeys(function (SchoolClass $class) {
                return [
                    $this->normalize($class->name) => $class->id,
                ];
            });

        $seenNis = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = [];

            $nis = $this->cleanCell($row['nis'] ?? null);
            $name = $this->cleanCell($row['nama'] ?? null);
            $className = $this->cleanCell($row['kelas'] ?? null);
            $gender = $this->normalizeGender(
                $this->cleanCell($row['gender'] ?? null)
            );

            if ($nis === '' && $name === '' && $className === '' && $gender === '') {
                $this->skipped++;
                continue;
            }

            if ($nis === '') {
                $rowErrors[] = "Baris {$rowNumber}: NIS wajib diisi.";
            }

            if ($name === '') {
                $rowErrors[] = "Baris {$rowNumber}: nama wajib diisi.";
            }

            if ($className === '') {
                $rowErrors[] = "Baris {$rowNumber}: kelas wajib diisi.";
            }

            if ($gender === '') {
                $rowErrors[] = "Baris {$rowNumber}: gender harus Laki-laki atau Perempuan.";
            }

            $classId = $classIdsByName->get(
                $this->normalize($className)
            );

            if ($className !== '' && ! $classId) {
                $rowErrors[] = "Baris {$rowNumber}: kelas {$className} belum ada.";
            }

            if ($nis !== '') {
                if (isset($seenNis[$nis])) {
                    $rowErrors[] = "Baris {$rowNumber}: NIS {$nis} duplikat dengan baris {$seenNis[$nis]}.";
                }

                $seenNis[$nis] = $rowNumber;

                $studentUserId = Student::query()
                    ->where('nis', $nis)
                    ->value('user_id');

                $usernameOwner = User::query()
                    ->where('username', $nis)
                    ->value('id');

                if ($usernameOwner && $studentUserId && (int) $usernameOwner !== (int) $studentUserId) {
                    $rowErrors[] = "Baris {$rowNumber}: NIS {$nis} sudah dipakai akun lain.";
                }

                if ($usernameOwner && ! $studentUserId) {
                    $rowErrors[] = "Baris {$rowNumber}: username {$nis} sudah dipakai akun non-siswa.";
                }
            }

            if ($rowErrors) {
                array_push($this->errors, ...$rowErrors);

                $this->invalid++;

                $this->previewRows[] = [
                    'row' => $rowNumber,
                    'nis' => $nis,
                    'name' => $name,
                    'class' => $className,
                    'gender' => $gender ?: $this->cleanCell($row['gender'] ?? null),
                    'valid' => false,
                    'status' => implode(' ', array_map(
                        fn (string $error) => Str::after($error, ': '),
                        $rowErrors
                    )),
                ];

                continue;
            }

            $validRow = [
                'nis' => $nis,
                'name' => $name,
                'class_id' => $classId,
                'class' => $className,
                'gender' => $gender,
            ];

            $this->validRows[] = $validRow;

            $this->valid++;

            $this->previewRows[] = [
                'row' => $rowNumber,
                'nis' => $nis,
                'name' => $name,
                'class' => $className,
                'gender' => $gender,
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
                $student = Student::query()
                    ->with('user')
                    ->where('nis', $row['nis'])
                    ->first();

                if ($student) {
                    $student->user?->update([
                        'name' => $row['name'],
                        'username' => $row['nis'],
                    ]);

                    $student->update([
                        'class_id' => $row['class_id'],
                        'gender' => $row['gender'],
                    ]);

                    $this->updated++;

                    continue;
                }

                $user = User::query()->create([
                    'name' => $row['name'],
                    'username' => $row['nis'],
                    'email' => null,
                    'password' => Hash::make($row['nis']),
                ]);

                $user->assignRole('siswa');

                Student::query()->create([
                    'user_id' => $user->id,
                    'class_id' => $row['class_id'],
                    'nis' => $row['nis'],
                    'gender' => $row['gender'],
                    'photo' => null,
                ]);

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

    private function normalize(string $value): string
    {
        return Str::lower(trim($value));
    }

    private function normalizeGender(string $value): string
    {
        $normalized = $this->normalize($value);

        return match ($normalized) {
            'l', 'laki', 'laki laki', 'laki-laki' => 'Laki-laki',
            'p', 'perempuan' => 'Perempuan',
            default => '',
        };
    }
}
