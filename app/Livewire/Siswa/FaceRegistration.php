<?php

namespace App\Livewire\Siswa;

use App\Models\AttendanceSetting;
use App\Models\FaceDescriptor;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FaceRegistration extends Component
{
    use WithPagination;

    public $selectedClass = '';

    public $selectedStudentId = '';

    public $descriptorCount = 0;

    public $canSelectStudent = false;

    public $search = '';

    public $classFilter = '';

    public $faceFilter = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->canSelectStudent = $this->userCanSelectStudent();

        if (! $this->canSelectStudent) {
            $student = $user->student;

            if ($student) {
                $this->selectedClass = (string) $student->class_id;
                $this->selectedStudentId = (string) $student->id;
            }
        } else {
            $studentId = request()->query('student');

            if ($studentId) {
                $student = Student::query()
                    ->whereKey($studentId)
                    ->first();

                if ($student) {
                    $this->selectedClass = (string) $student->class_id;
                    $this->selectedStudentId = (string) $student->id;
                }
            }
        }

        $this->refreshDescriptorCount();
    }

    public function updatedSelectedClass(): void
    {
        if ($this->userCanSelectStudent()) {
            $this->selectedStudentId = '';
        }

        $this->refreshDescriptorCount();
    }

    public function updatedSelectedStudentId(): void
    {
        $this->refreshDescriptorCount();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFaceFilter(): void
    {
        $this->resetPage();
    }

    public function saveDescriptor($descriptor): void
    {
        $student = $this->targetStudent();

        if (! $student) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Pilih siswa terlebih dahulu.'
            );

            return;
        }

        if (! is_array($descriptor) || count($descriptor) === 0) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Descriptor wajah tidak valid.'
            );

            return;
        }

        $maxDescriptors = AttendanceSetting::current()
            ->max_descriptors;

        if ($student->descriptors()->count() >= $maxDescriptors) {

            $student->descriptors()
                ->oldest()
                ->first()
                ?->delete();
        }

        FaceDescriptor::create([
            'student_id' => $student->id,
            'descriptor' => $descriptor,
        ]);

        $this->refreshDescriptorCount($student);

        $this->dispatch('descriptor-saved', count: $this->descriptorCount);
    }

    public function saveDescriptorForStudent($studentId, $descriptor): void
    {
        if (! $this->userCanSelectStudent()) {
            $this->saveDescriptor($descriptor);

            return;
        }

        $student = Student::query()
            ->with([
                'user',
                'class',
            ])
            ->find($studentId);

        if (! $student) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Siswa tidak ditemukan.'
            );

            return;
        }

        $this->selectedClass = (string) $student->class_id;
        $this->selectedStudentId = (string) $student->id;

        $this->saveDescriptor($descriptor);
    }

    public function resetDescriptors(): void
    {
        $student = $this->targetStudent();

        if (! $student) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Pilih siswa terlebih dahulu.'
            );

            return;
        }

        $student->descriptors()->delete();

        $this->descriptorCount = 0;

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Descriptor wajah berhasil direset.'
        );

        $this->dispatch('descriptor-reset', count: 0);
    }

    public function resetStudentDescriptors($studentId): void
    {
        if (! $this->userCanSelectStudent()) {
            $this->resetDescriptors();

            return;
        }

        $student = Student::query()
            ->find($studentId);

        if (! $student) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Siswa tidak ditemukan.'
            );

            return;
        }

        $student->descriptors()->delete();

        if ((string) $this->selectedStudentId === (string) $student->id) {
            $this->descriptorCount = 0;
        }

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Descriptor wajah siswa berhasil direset.'
        );

        $this->dispatch('descriptor-reset', count: 0);
    }

    private function refreshDescriptorCount(?Student $student = null): void
    {
        $student ??= $this->targetStudent();

        $this->descriptorCount = $student
          ? $student->descriptors()->count()
          : 0;
    }

    private function targetStudent(): ?Student
    {
        if ($this->userCanSelectStudent()) {
            if (! $this->selectedStudentId) {
                return null;
            }

            return Student::query()
                ->with([
                    'user',
                    'class',
                ])
                ->when($this->selectedClass, function ($query) {
                    $query->where(
                        'class_id',
                        $this->selectedClass
                    );
                })
                ->find($this->selectedStudentId);
        }

        return Auth::user()
            ->student()
            ->with('class')
            ->first();
    }

    private function userCanSelectStudent(): bool
    {
        return Auth::user()->hasAnyRole([
            'admin',
            'guru',
        ]);
    }

    public function render()
    {
        $this->canSelectStudent = $this->userCanSelectStudent();

        $student = $this->targetStudent();
        $maxDescriptors = AttendanceSetting::current()
            ->max_descriptors;

        $studentListQuery = Student::query()
            ->with([
                'user',
                'class',
            ])
            ->withCount('descriptors')
            ->whereHas('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nis', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->where('class_id', $this->classFilter);
            })
            ->when($this->faceFilter === 'ready', function ($query) {
                $query->has('descriptors', '>=', 3);
            })
            ->when($this->faceFilter === 'partial', function ($query) {
                $query
                    ->has('descriptors', '>', 0)
                    ->has('descriptors', '<', 3);
            })
            ->when($this->faceFilter === 'empty', function ($query) {
                $query->has('descriptors', '=', 0);
            })
            ->latest();

        $totalStudents = Student::query()
            ->count();

        $faceReadyStudents = Student::query()
            ->has('descriptors', '>=', 3)
            ->count();

        $faceEmptyStudents = Student::query()
            ->has('descriptors', '=', 0)
            ->count();

        $facePartialStudents = max(
            $totalStudents - $faceReadyStudents - $faceEmptyStudents,
            0
        );

        return view('livewire.siswa.face-registration', [
            'student' => $student,
            'classes' => $this->canSelectStudent
              ? SchoolClass::query()
                  ->orderBy('name')
                  ->get()
              : collect(),
            'students' => $this->canSelectStudent
              ? $studentListQuery->paginate(10)
              : collect(),
            'maxDescriptors' => $maxDescriptors,
            'totalStudents' => $totalStudents,
            'faceReadyStudents' => $faceReadyStudents,
            'facePartialStudents' => $facePartialStudents,
            'faceEmptyStudents' => $faceEmptyStudents,
        ]);
    }
}
