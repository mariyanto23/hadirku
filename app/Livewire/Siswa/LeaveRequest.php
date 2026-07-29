<?php

namespace App\Livewire\Siswa;

use App\Models\Attendance;
use App\Models\AcademicHoliday;
use App\Models\AttendanceSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LeaveRequest extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $attendance_date;

    public $status = 'izin';

    public $notes = '';

    public $attachment;

    protected array $validationAttributes = [
        'attachment' => 'lampiran',
    ];

    protected function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['izin', 'sakit'])],
            'notes' => ['required', 'string', 'min:5', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
        ];
    }

    public function mount(): void
    {
        $this->attendance_date = today()->format('Y-m-d');
    }

    public function updatedAttachment(): void
    {
        $this->resetValidation('attachment');

        if ($this->attachment) {
            $this->validateOnly('attachment');
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'status',
            'notes',
            'attachment',
        ]);

        $this->status = 'izin';
        $this->attendance_date = today()->format('Y-m-d');
        $this->resetValidation();
    }

    public function submit(): void
    {
        $this->validate();

        $student = Auth::user()->student;

        if (! $student) {
            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Data siswa belum ditemukan.'
            );

            return;
        }

        $blockingHoliday = AcademicHoliday::blockingAttendanceOn($this->attendance_date);

        if ($blockingHoliday) {
            $this->addError(
                'attendance_date',
                'Tanggal ini merupakan hari libur: '.$blockingHoliday->title.'.'
            );

            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Pengajuan tidak diperlukan pada hari libur.'
            );

            return;
        }

        $settings = AttendanceSetting::current();
        $allowingHoliday = AcademicHoliday::allowingAttendanceOn($this->attendance_date);

        if (! $settings->isSchoolDay($this->attendance_date) && ! $allowingHoliday) {
            $this->addError(
                'attendance_date',
                'Tanggal ini bukan hari sekolah.'
            );

            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Pengajuan tidak diperlukan pada hari yang bukan hari sekolah.'
            );

            return;
        }

        $existing = Attendance::query()
            ->where('student_id', $student->id)
            ->whereDate('attendance_date', $this->attendance_date)
            ->first();

        if ($existing && $existing->approval_status !== 'rejected') {
            $this->addError(
                'attendance_date',
                'Tanggal ini sudah memiliki presensi atau pengajuan.'
            );

            $this->dispatch(
                'hadirku-toast',
                type: 'error',
                message: 'Pengajuan untuk tanggal ini sudah ada.'
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

        DB::transaction(function () use ($student, $existing, $attachmentPath, $attachmentName, &$oldAttachmentToDelete) {
            if ($existing && $existing->approval_status === 'rejected') {
                if ($attachmentPath) {
                    $oldAttachmentToDelete = $existing->attachment_path;
                }

                $existing->update([
                    'attendance_date' => $this->attendance_date,
                    'attendance_time' => now()->format('H:i:s'),
                    'status' => $this->status,
                    'confidence_score' => null,
                    'match_threshold_used' => null,
                    'notes' => $this->notes,
                    'approval_status' => 'pending',
                    'requested_by_user_id' => Auth::id(),
                    'reviewed_by_user_id' => null,
                    'reviewed_at' => null,
                    'review_notes' => null,
                    ...($attachmentPath ? [
                        'attachment_path' => $attachmentPath,
                        'attachment_name' => $attachmentName,
                    ] : []),
                ]);
            } else {
                Attendance::query()->create([
                    'student_id' => $student->id,
                    'attendance_date' => $this->attendance_date,
                    'attendance_time' => now()->format('H:i:s'),
                    'status' => $this->status,
                    'confidence_score' => null,
                    'match_threshold_used' => null,
                    'notes' => $this->notes,
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $attachmentName,
                    'approval_status' => 'pending',
                    'requested_by_user_id' => Auth::id(),
                ]);
            }
        });

        if ($oldAttachmentToDelete) {
            Storage::disk('public')->delete($oldAttachmentToDelete);
        }

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Pengajuan berhasil dikirim dan menunggu persetujuan.'
        );

        $this->resetForm();
    }

    public function render()
    {
        $student = Auth::user()->student;

        return view('livewire.siswa.leave-request', [
            'student' => $student,
            'requests' => Attendance::query()
                ->when($student, function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                }, function ($query) {
                    $query->whereRaw('1 = 0');
                })
                ->whereIn('status', [
                    'izin',
                    'sakit',
                    'alpha',
                ])
                ->whereNotNull('requested_by_user_id')
                ->with([
                    'reviewedBy',
                ])
                ->latest()
                ->paginate(8),
        ]);
    }
}
