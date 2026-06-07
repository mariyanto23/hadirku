<?php

namespace App\Livewire\Admin;

use App\Imports\AcademicHolidaysImport;
use App\Models\AcademicHoliday;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AcademicCalendar extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $holidayId = null;

    public string $title = '';

    public string $type = AcademicHoliday::TYPE_NATIONAL;

    public string $start_date = '';

    public string $end_date = '';

    public bool $allow_attendance = false;

    public string $notes = '';

    public string $search = '';

    public string $typeFilter = '';

    public int|string $yearFilter = '';

    public bool $showFormModal = false;

    public bool $showImportModal = false;

    public bool $isEdit = false;

    public $importFile;

    public array $importPreviewRows = [];

    public int $importValidCount = 0;

    public int $importInvalidCount = 0;

    public function mount(): void
    {
        $this->yearFilter = (int) today()->format('Y');
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', Rule::in(array_keys(AcademicHoliday::types()))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'allow_attendance' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingYearFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openImportModal(): void
    {
        $this->resetImportState();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->resetImportState();
    }

    public function resetForm(): void
    {
        $this->holidayId = null;
        $this->title = '';
        $this->type = AcademicHoliday::TYPE_NATIONAL;
        $this->start_date = today()->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->allow_attendance = false;
        $this->notes = '';
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        $holiday = AcademicHoliday::query()->findOrFail($id);

        $this->holidayId = $holiday->id;
        $this->title = $holiday->title;
        $this->type = $holiday->type;
        $this->start_date = $holiday->start_date->format('Y-m-d');
        $this->end_date = $holiday->end_date->format('Y-m-d');
        $this->allow_attendance = (bool) $holiday->allow_attendance;
        $this->notes = (string) $holiday->notes;
        $this->isEdit = true;
        $this->showFormModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();

        $overlap = AcademicHoliday::query()
            ->overlapping($this->start_date, $this->end_date)
            ->when($this->holidayId, function ($query) {
                $query->whereKeyNot($this->holidayId);
            })
            ->first();

        if ($overlap) {
            $this->addError(
                'start_date',
                'Rentang tanggal bertumpang tindih dengan '.$overlap->title.'.'
            );

            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Rentang tanggal libur bertumpang tindih.'
            );

            return;
        }

        if ($this->holidayId) {
            AcademicHoliday::query()
                ->findOrFail($this->holidayId)
                ->update($validated);
        } else {
            AcademicHoliday::query()->create($validated);
        }

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: $this->isEdit
                ? 'Kalender akademik berhasil diperbarui.'
                : 'Kalender akademik berhasil ditambahkan.'
        );

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        AcademicHoliday::query()->findOrFail($id)->delete();

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Kalender akademik berhasil dihapus.'
        );

        $this->resetPage();
    }

    public function downloadImportTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'nama_libur',
                'jenis',
                'tanggal_mulai',
                'tanggal_selesai',
                'presensi',
                'keterangan',
            ]);

            fputcsv($handle, [
                'Libur Semester Genap',
                'Libur Semester',
                today()->startOfMonth()->format('Y-m-d'),
                today()->startOfMonth()->addDays(6)->format('Y-m-d'),
                'Tutup',
                'Contoh rentang libur',
            ]);

            fputcsv($handle, [
                'Kegiatan Sekolah',
                'Kegiatan Sekolah',
                today()->startOfMonth()->addDays(10)->format('Y-m-d'),
                today()->startOfMonth()->addDays(10)->format('Y-m-d'),
                'Buka',
                'Contoh tanggal yang tetap membuka presensi',
            ]);

            fclose($handle);
        }, 'template-import-libur.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function updatedImportFile(): void
    {
        $this->previewImportFile();
    }

    public function previewImportFile(): void
    {
        $this->resetErrorBag('importFile');

        $this->validate([
            'importFile' => [
                'required',
                'file',
                'mimes:csv,txt,xlsx,xls',
                'max:2048',
            ],
        ]);

        try {
            $import = new AcademicHolidaysImport(
                persist: false
            );

            Excel::import(
                $import,
                $this->importFile
            );

            $this->importPreviewRows = $import->previewRows;
            $this->importValidCount = $import->valid;
            $this->importInvalidCount = $import->invalid;
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable) {
            $this->addError(
                'importFile',
                'Berkas impor tidak bisa dibaca. Pastikan format kolom benar.'
            );

            $this->importPreviewRows = [];
            $this->importValidCount = 0;
            $this->importInvalidCount = 0;
        }
    }

    public function importHolidays(): void
    {
        $this->validate([
            'importFile' => [
                'required',
                'file',
                'mimes:csv,txt,xlsx,xls',
                'max:2048',
            ],
        ]);

        if (! $this->importPreviewRows) {
            $this->previewImportFile();
        }

        if ($this->importInvalidCount > 0) {
            $this->addError(
                'importFile',
                'Perbaiki baris yang tidak valid sebelum impor.'
            );

            return;
        }

        if ($this->importValidCount === 0) {
            $this->addError(
                'importFile',
                'Tidak ada data libur valid untuk diimpor.'
            );

            return;
        }

        $import = new AcademicHolidaysImport();

        Excel::import(
            $import,
            $this->importFile
        );

        $this->resetImportState();
        $this->resetPage();

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: "Impor selesai. {$import->created} data libur ditambahkan."
        );
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->yearFilter = (int) today()->format('Y');
        $this->resetPage();
    }

    private function resetImportState(): void
    {
        $this->reset([
            'importFile',
            'importPreviewRows',
            'importValidCount',
            'importInvalidCount',
        ]);

        $this->showImportModal = false;
        $this->resetErrorBag('importFile');
    }

    public function render()
    {
        $yearStart = $this->yearFilter ? now()->setDate((int) $this->yearFilter, 1, 1)->startOfDay() : null;
        $yearEnd = $this->yearFilter ? now()->setDate((int) $this->yearFilter, 12, 31)->endOfDay() : null;

        $baseQuery = AcademicHoliday::query()
            ->when($this->yearFilter, function ($query) use ($yearStart, $yearEnd) {
                $query->overlapping($yearStart, $yearEnd);
            });

        return view('livewire.admin.academic-calendar', [
            'holidays' => AcademicHoliday::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($innerQuery) {
                        $innerQuery
                            ->where('title', 'like', '%'.$this->search.'%')
                            ->orWhere('notes', 'like', '%'.$this->search.'%');
                    });
                })
                ->when($this->typeFilter, function ($query) {
                    $query->where('type', $this->typeFilter);
                })
                ->when($this->yearFilter, function ($query) use ($yearStart, $yearEnd) {
                    $query->overlapping($yearStart, $yearEnd);
                })
                ->orderBy('start_date')
                ->paginate(10),
            'types' => AcademicHoliday::types(),
            'years' => range((int) today()->format('Y') - 1, (int) today()->format('Y') + 2),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'active' => (clone $baseQuery)->overlapping(today(), today())->count(),
                'blocked' => (clone $baseQuery)->where('allow_attendance', false)->count(),
                'allowed' => (clone $baseQuery)->where('allow_attendance', true)->count(),
            ],
        ]);
    }
}
