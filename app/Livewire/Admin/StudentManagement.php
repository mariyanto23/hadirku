<?php

namespace App\Livewire\Admin;

use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class StudentManagement extends Component
{
  use WithFileUploads;
  use WithPagination;

  public $studentId;

  public $name;
  public $nis;
  public $gender;
  public $photo;
  public $existingPhoto;
  public $class_id;

  public $importFile;

  public $showImportModal = false;

  public $showCreateModal = false;

  public array $importPreviewRows = [];

  public int $importValidCount = 0;

  public int $importInvalidCount = 0;

  public $search = '';

  public $filterClass = '';

  public $isEdit = false;

  public function mount(): void
  {
    $this->search = trim((string) request()->query('search', ''));
    $requestedClass = (string) request()->query('class', '');

    $this->filterClass = SchoolClass::query()
      ->whereKey($requestedClass)
      ->exists()
        ? $requestedClass
        : '';
  }

  public function updatingSearch(): void
  {
    $this->resetPage();
  }

  public function updatingFilterClass(): void
  {
    $this->resetPage();
  }

  public function applySearch(string $value): void
  {
    $this->search = trim($value);
    $this->resetPage();
  }

  public function applyClassFilter(string $value): void
  {
    $this->filterClass = $value;
    $this->resetPage();
  }

  protected function rules(): array
  {
    $userId = $this->studentId
      ? Student::query()
        ->whereKey($this->studentId)
        ->value('user_id')
      : null;

    return [
      'name' => 'required|min:3',
      'nis' => [
        'required',
        Rule::unique('students', 'nis')
          ->ignore($this->studentId),
        Rule::unique('users', 'username')
          ->ignore($userId),
      ],
      'gender' => 'required',
      'class_id' => 'required|exists:classes,id',
      'photo' => 'nullable|image|max:1024',
    ];
  }

  public function resetForm(): void
  {
    $this->reset([
      'studentId',
      'name',
      'nis',
      'gender',
      'photo',
      'existingPhoto',
      'class_id',
      'isEdit',
    ]);

    $this->resetValidation();
  }

  public function openCreateModal(): void
  {
    $this->resetForm();
    $this->showCreateModal = true;
  }

  public function closeCreateModal(): void
  {
    $this->showCreateModal = false;
    $this->resetForm();
  }

  public function save(): void
  {
    $this->validate();

    $photoPath = $this->photo
      ? $this->photo->store('student-photos', 'public')
      : null;

    $oldPhotoToDelete = null;

    DB::transaction(function () use ($photoPath, &$oldPhotoToDelete) {

      if ($this->isEdit) {

        $student = Student::query()
          ->with('user')
          ->findOrFail($this->studentId);

        if ($photoPath) {
          $oldPhotoToDelete = $student->photo;
        }

        $student->user->update([
          'name' => $this->name,
          'username' => $this->nis,
        ]);

        $student->update([
          'class_id' => $this->class_id,
          'nis' => $this->nis,
          'gender' => $this->gender,
          'photo' => $photoPath ?: $student->photo,
        ]);

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Siswa berhasil diperbarui.'
        );
      } else {

        $user = User::create([
          'name' => $this->name,
          'username' => $this->nis,
          'email' => null,
          'password' => Hash::make($this->nis),
        ]);

        $user->assignRole('siswa');

        Student::create([
          'user_id' => $user->id,
          'class_id' => $this->class_id,
          'nis' => $this->nis,
          'gender' => $this->gender,
          'photo' => $photoPath,
        ]);

        $this->dispatch(
          'hadirku-toast',
          type: 'success',
          message: 'Siswa berhasil ditambahkan.'
        );
      }
    });

    if ($oldPhotoToDelete) {
      Storage::disk('public')->delete($oldPhotoToDelete);
    }

    $this->resetForm();
    $this->showCreateModal = false;
  }

  public function edit($id): void
  {
    $student = Student::query()
      ->with('user')
      ->findOrFail($id);

    $this->studentId = $student->id;

    $this->name = $student->user->name;
    $this->nis = $student->nis;
    $this->gender = $student->gender;
    $this->photo = null;
    $this->existingPhoto = $student->photo;
    $this->class_id = $student->class_id;

    $this->isEdit = true;
  }

  public function delete($id): void
  {
    $student = Student::query()
      ->with('user')
      ->findOrFail($id);

    DB::transaction(function () use ($student) {

      if ($student->user) {
        $student->user->delete();
      } else {
        $student->delete();
      }

    });

    if ($student->photo) {
      Storage::disk('public')->delete($student->photo);
    }

    $this->dispatch(
        'hadirku-toast',
        type: 'success',
        message: 'Siswa berhasil dihapus.'
    );
  }

  public function resetPassword($id): void
  {
    $student = Student::query()
      ->with('user')
      ->findOrFail($id);

    if (! $student->user) {
      $this->dispatch(
        'hadirku-toast',
        type: 'error',
        message: 'Akun siswa tidak ditemukan.'
      );

      return;
    }

    $student->user->update([
      'password' => Hash::make($student->nis),
    ]);

        $this->dispatch(
      'hadirku-toast',
      type: 'success',
      message: 'Kata sandi siswa berhasil diatur ulang ke NIS.'
    );
  }

  public function removePhoto(): void
  {
    if (! $this->isEdit || ! $this->studentId) {
      $this->photo = null;

      return;
    }

    $student = Student::query()
      ->findOrFail($this->studentId);

    $oldPhoto = $student->photo;

    $student->update([
      'photo' => null,
    ]);

    if ($oldPhoto) {
      Storage::disk('public')->delete($oldPhoto);
    }

    $this->photo = null;
    $this->existingPhoto = null;

    $this->dispatch(
      'hadirku-toast',
      type: 'success',
      message: 'Foto siswa berhasil dihapus.'
    );
  }

  public function exportCsv()
  {
    $students = $this->studentQuery()
      ->get();

    $fileName = 'data-siswa-' . now()->format('Ymd-His') . '.csv';

    return response()->streamDownload(function () use ($students) {
      $handle = fopen('php://output', 'w');

      fputcsv($handle, [
        'NIS',
        'Nama',
        'Kelas',
        'Jenis Kelamin',
        'Face Descriptor',
      ]);

      foreach ($students as $student) {
        fputcsv($handle, [
          $student->nis,
          $student->user?->name,
          $student->class?->name,
          $student->gender,
          $student->descriptors_count,
        ]);
      }

      fclose($handle);
    }, $fileName, [
      'Content-Type' => 'text/csv',
    ]);
  }

  public function downloadImportTemplate()
  {
    $classes = SchoolClass::query()
      ->orderBy('name')
      ->pluck('name')
      ->values();

    return response()->streamDownload(function () use ($classes) {
      $handle = fopen('php://output', 'w');

      fputcsv($handle, [
        'nis',
        'nama',
        'kelas',
        'gender',
      ]);

      fputcsv($handle, [
        '1001',
        'Nama Siswa',
        $classes->first() ?: 'Kelas 1',
        'Laki-laki',
      ]);

      fputcsv($handle, [
        '1002',
        'Nama Siswi',
        $classes->skip(1)->first() ?: ($classes->first() ?: 'Kelas 1'),
        'Perempuan',
      ]);

      fclose($handle);
    }, 'template-import-siswa.csv', [
      'Content-Type' => 'text/csv',
    ]);
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
      $import = new StudentsImport(
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
    } catch (Throwable $exception) {
      $this->addError(
        'importFile',
        'Berkas impor tidak bisa dibaca. Pastikan format kolom benar.'
      );

      $this->importPreviewRows = [];
      $this->importValidCount = 0;
      $this->importInvalidCount = 0;
    }
  }

  public function importStudents(): void
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
        'Tidak ada data siswa valid untuk diimpor.'
      );

      return;
    }

    $import = new StudentsImport();

    Excel::import(
      $import,
      $this->importFile
    );

    $this->resetImportState();
    $this->resetPage();

    $this->dispatch(
      'hadirku-toast',
      type: 'success',
      message: "Impor selesai. {$import->created} siswa baru, {$import->updated} siswa diperbarui."
    );
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

  private function studentQuery()
  {
    return Student::query()
      ->with(['user', 'class'])
      ->withCount('descriptors')
      ->when($this->search, function ($query) {
        $query->where(function ($q) {
          $q->where('nis', 'like', '%' . $this->search . '%')
            ->orWhereHas('user', function ($userQuery) {
              $userQuery->where('name', 'like', '%' . $this->search . '%');
            });
        });
      })
      ->when($this->filterClass, function ($query) {
        $query->where('class_id', $this->filterClass);
      })
      ->latest();
  }

  public function render()
  {
    return view('livewire.admin.student-management', [
      'students' => $this->studentQuery()
        ->paginate(10),

      'classes' => SchoolClass::query()
        ->orderBy('name')
        ->get(),

      'totalStudents' => Student::query()
        ->count(),
    ]);
  }
}
