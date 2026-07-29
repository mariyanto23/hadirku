<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManualAttendance extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $attendanceId;

    public $selectedClass = '';

    public $student_id = '';

    public $attendance_date;

    public $attendance_time;

    public $status = 'izin';

    public $notes = '';

    public $attachment;

    public bool $removeAttachment = false;

    public ?string $existingAttachmentPath = null;

    public ?string $existingAttachmentName = null;

    public $search = '';

    public $classFilter = '';

    public $statusFilter = '';

    public $approvalFilter = 'pending';

    public $dateFilter = '';

    public $mobileClassFilter = '';

    public $mobileStatusFilter = '';

    public $mobileApprovalFilter = 'pending';

    public $mobileDateFilter = '';

    public $isEdit = false;

    public $showFormModal = false;

    public bool $showMobileFilterModal = false;

    public function mount(): void
    {
        $this->attendance_date = today()->format('Y-m-d');
        $this->attendance_time = now()->format('H:i');
        $this->dateFilter = '';

        if (Auth::user()?->hasRole('guru') && Auth::user()?->default_class_id) {
            $this->selectedClass = (string) Auth::user()->default_class_id;
        }
    }

    protected function rules(): array
    {
        return [
            'selectedClass' => ['nullable', 'exists:classes,id'],
            'student_id' => ['required', 'exists:students,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance_time' => ['required', 'date_format:H:i'],
            'status' => [
                'required',
                Rule::in(array_keys($this->availableStatusOptions())),
            ],
            'notes' => $this->isEdit
                ? ['required', 'string', 'min:3', 'max:1000']
                : ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'removeAttachment' => ['boolean'],
        ];
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

    public function updatingApprovalFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedClass(): void
    {
        $this->student_id = '';
    }

    public function viewAllApprovals(): void
    {
        $this->approvalFilter = '';
        $this->mobileApprovalFilter = '';
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'classFilter',
            'statusFilter',
            'dateFilter',
            'mobileClassFilter',
            'mobileStatusFilter',
            'mobileDateFilter',
        ]);

        $this->approvalFilter = 'pending';
        $this->mobileApprovalFilter = 'pending';
        $this->showMobileFilterModal = false;
        $this->resetPage();
    }

    public function openMobileFilters(): void
    {
        $this->mobileClassFilter = $this->classFilter;
        $this->mobileStatusFilter = $this->statusFilter;
        $this->mobileApprovalFilter = $this->approvalFilter;
        $this->mobileDateFilter = $this->dateFilter;
        $this->showMobileFilterModal = true;
    }

    public function closeMobileFilters(): void
    {
        $this->showMobileFilterModal = false;
    }

    public function applyMobileFilters(): void
    {
        $this->classFilter = $this->mobileClassFilter;
        $this->statusFilter = $this->mobileStatusFilter;
        $this->approvalFilter = $this->mobileApprovalFilter;
        $this->dateFilter = $this->mobileDateFilter;
        $this->showMobileFilterModal = false;

        $this->resetPage();
    }

    public function resetMobileFilters(): void
    {
        $this->reset([
            'classFilter',
            'statusFilter',
            'dateFilter',
            'mobileClassFilter',
            'mobileStatusFilter',
            'mobileDateFilter',
        ]);

        $this->approvalFilter = 'pending';
        $this->mobileApprovalFilter = 'pending';
        $this->showMobileFilterModal = false;

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

    public function resetForm(): void
    {
        $this->reset([
            'attendanceId',
            'selectedClass',
            'student_id',
            'status',
            'notes',
            'attachment',
            'removeAttachment',
            'existingAttachmentPath',
            'existingAttachmentName',
            'isEdit',
        ]);

        $this->attendance_date = today()->format('Y-m-d');
        $this->attendance_time = now()->format('H:i');
        $this->status = 'izin';

        if (Auth::user()?->hasRole('guru') && Auth::user()?->default_class_id) {
            $this->selectedClass = (string) Auth::user()->default_class_id;
        }

        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $approvalStatus = $this->isEdit && $this->status === 'alpha'
            ? 'rejected'
            : 'approved';

        if ($this->selectedClass && ! Student::query()
            ->whereKey($this->student_id)
            ->where('class_id', $this->selectedClass)
            ->exists()) {
            $this->addError(
                'student_id',
                'Siswa tidak sesuai dengan kelas yang dipilih.'
            );

            return;
        }

        $duplicate = Attendance::query()
            ->where('student_id', $this->student_id)
            ->whereDate('attendance_date', $this->attendance_date)
            ->when($this->isEdit, function ($query) {
                $query->whereKeyNot($this->attendanceId);
            })
            ->exists();

        if ($duplicate) {
            $this->addError(
                'student_id',
                'Siswa ini sudah memiliki presensi pada tanggal tersebut.'
            );

            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Presensi siswa pada tanggal ini sudah ada.'
            );

            return;
        }

        $attachmentPath = $this->attachment
            ? $this->attachment->store('leave-attachments', 'public')
            : null;

        $attachmentName = $this->attachment
            ? $this->attachment->getClientOriginalName()
            : null;

        $oldAttachmentToDelete = null;

        DB::transaction(function () use ($approvalStatus, $attachmentPath, $attachmentName, &$oldAttachmentToDelete) {
            if ($this->isEdit) {
                $attendance = Attendance::query()
                    ->findOrFail($this->attendanceId);

                $updates = [
                    'student_id' => $this->student_id,
                    'attendance_date' => $this->attendance_date,
                    'attendance_time' => $this->attendance_time.':00',
                    'status' => $this->status,
                    'notes' => $this->notes ?: null,
                    'approval_status' => $approvalStatus,
                    'reviewed_by_user_id' => Auth::id(),
                    'reviewed_at' => now(),
                    'review_notes' => $approvalStatus === 'rejected'
                        ? 'Status dikoreksi menjadi alpa oleh '.Auth::user()->name.'.'
                        : null,
                ];

                if ($attachmentPath) {
                    $oldAttachmentToDelete = $attendance->attachment_path;
                    $updates['attachment_path'] = $attachmentPath;
                    $updates['attachment_name'] = $attachmentName;
                } elseif ($this->removeAttachment) {
                    $oldAttachmentToDelete = $attendance->attachment_path;
                    $updates['attachment_path'] = null;
                    $updates['attachment_name'] = null;
                }

                $attendance->update($updates);

                $message = 'Presensi berhasil dikoreksi.';
            } else {
                Attendance::query()->create([
                    'student_id' => $this->student_id,
                    'attendance_date' => $this->attendance_date,
                    'attendance_time' => $this->attendance_time.':00',
                    'status' => $this->status,
                    'confidence_score' => null,
                    'match_threshold_used' => null,
                    'notes' => $this->notes ?: null,
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $attachmentName,
                    'approval_status' => 'approved',
                    'reviewed_by_user_id' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                $message = 'Presensi manual berhasil disimpan.';
            }

            $this->dispatch(
                'hadirku-toast',
                type: 'success',
                message: $message
            );
        });

        if ($oldAttachmentToDelete) {
            Storage::disk('public')->delete($oldAttachmentToDelete);
        }

        $this->approvalFilter = $approvalStatus;
        $this->resetForm();
        $this->showFormModal = false;
    }

    public function edit($id): void
    {
        $attendance = Attendance::query()
            ->with('student')
            ->findOrFail($id);

        $this->attendanceId = $attendance->id;
        $this->selectedClass = (string) $attendance->student->class_id;
        $this->student_id = (string) $attendance->student_id;
        $this->attendance_date = $attendance->attendance_date->format('Y-m-d');
        $this->attendance_time = substr((string) $attendance->attendance_time, 0, 5);
        $this->status = $attendance->status;
        $this->notes = $attendance->notes;
        $this->attachment = null;
        $this->removeAttachment = false;
        $this->existingAttachmentPath = $attendance->attachment_path;
        $this->existingAttachmentName = $attendance->attachment_name;
        $this->isEdit = true;
        $this->showFormModal = true;

        $this->resetValidation();
    }

    public function approve($id): void
    {
        $attendance = Attendance::query()
            ->where('approval_status', 'pending')
            ->findOrFail($id);

        $attendance->update([
            'approval_status' => 'approved',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => null,
        ]);

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Pengajuan berhasil disetujui.'
        );
    }

    public function reject($id, ?string $reason = null): void
    {
        $attendance = Attendance::query()
            ->where('approval_status', 'pending')
            ->findOrFail($id);

        $reason = trim((string) $reason);

        if (mb_strlen($reason) > 500) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Alasan penolakan maksimal 500 karakter.'
            );

            return;
        }

        $reviewNotes = $reason !== ''
            ? $reason
            : 'Pengajuan ditolak oleh '.Auth::user()->name.'.';

        $attendance->update([
            'status' => 'alpha',
            'approval_status' => 'rejected',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $reviewNotes,
        ]);

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Pengajuan ditolak dan presensi ditandai alpa.'
        );
    }

    public function availableStatusOptions(): array
    {
        if (! $this->isEdit) {
            return [
                'izin' => 'Izin',
                'sakit' => 'Sakit',
            ];
        }

        return [
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpa',
        ];
    }

    private function attendanceQuery(bool $applyApprovalFilter = true)
    {
        return Attendance::query()
            ->with([
                'student.user',
                'student.class',
                'requestedBy',
                'reviewedBy',
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('student.user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('student', function ($studentQuery) {
                            $studentQuery->where('nis', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->whereHas('student', function ($studentQuery) {
                    $studentQuery->where('class_id', $this->classFilter);
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($applyApprovalFilter && $this->approvalFilter, function ($query) {
                $query->where('approval_status', $this->approvalFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('attendance_date', $this->dateFilter);
            })
            ->latest();
    }

    public function render()
    {
        $summaryQuery = $this->attendanceQuery(false);

        return view('livewire.attendance.manual-attendance', [
            'classes' => SchoolClass::query()
                ->orderBy('name')
                ->get(),

            'students' => Student::query()
                ->with(['user', 'class'])
                ->when($this->selectedClass, function ($query) {
                    $query->where('class_id', $this->selectedClass);
                })
                ->whereHas('user')
                ->get()
                ->sortBy('user.name')
                ->values(),

            'attendances' => $this->attendanceQuery()
                ->paginate(10),

            'statusOptions' => $this->availableStatusOptions(),

            'summary' => [
                'total' => (clone $summaryQuery)->count(),
                'pending' => (clone $summaryQuery)->where('approval_status', 'pending')->count(),
                'approved' => (clone $summaryQuery)->where('approval_status', 'approved')->count(),
                'rejected' => (clone $summaryQuery)->where('approval_status', 'rejected')->count(),
            ],

            'activeFilterCount' => collect([
                $this->classFilter,
                $this->statusFilter,
                $this->dateFilter,
                $this->approvalFilter,
            ])->filter()->count(),
        ]);
    }
}
