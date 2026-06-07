<?php

namespace App\Livewire\Admin;

use App\Models\SchoolClass;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class ClassManagement extends Component
{
  use WithPagination;

  public $name;

  public $search = '';

  public function updatingSearch(): void
  {
    $this->resetPage();
  }

  public function save(): void
  {
    $this->name = trim((string) $this->name);

    $this->validate([
      'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('classes', 'name'),
      ],
    ]);

    SchoolClass::create([
      'name' => $this->name,
    ]);

    $this->resetForm();

    $this->dispatch(
      'hadirku-toast',
      type: 'success',
      message: 'Kelas berhasil ditambahkan.'
    );
  }

  public function resetForm(): void
  {
    $this->reset([
      'name',
    ]);

    $this->resetValidation();
  }

  public function delete($id): void
  {
    $class = SchoolClass::query()
      ->withCount('students')
      ->findOrFail($id);

    if ($class->students_count > 0) {
      $this->dispatch(
        'hadirku-toast',
        type: 'error',
        message: 'Kelas masih memiliki siswa. Pindahkan atau hapus siswa terlebih dahulu.'
      );

      return;
    }

    $class->delete();

    $this->dispatch(
      'hadirku-toast',
      type: 'success',
      message: 'Kelas berhasil dihapus.'
    );
  }

  public function render()
  {
    return view('livewire.admin.class-management', [
      'classes' => SchoolClass::query()
        ->withCount([
          'students',
          'students as face_ready_students_count' => function ($query) {
            $query->whereHas(
              'descriptors',
              null,
              '>=',
              3
            );
          },
        ])
        ->where('name', 'like', '%' . $this->search . '%')
        ->latest()
        ->paginate(10),

      'totalClasses' => SchoolClass::query()
        ->count(),
    ]);
  }
}
