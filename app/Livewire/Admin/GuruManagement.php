<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GuruManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $guruId;

    public $name;

    public $username;

    public $photo;

    public $existingPhoto;

    public $is_active = true;

    public $default_class_id = '';

    public $search = '';

    public $statusFilter = '';

    public $isEdit = false;

    public $showCreateModal = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->guruId),
            ],
            'photo' => ['nullable', 'image', 'max:1024'],
            'is_active' => ['boolean'],
            'default_class_id' => ['nullable', 'exists:classes,id'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'guruId',
            'name',
            'username',
            'photo',
            'existingPhoto',
            'isEdit',
            'default_class_id',
        ]);

        $this->is_active = true;
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
            ? $this->photo->store('guru-photos', 'public')
            : null;

        $oldPhotoToDelete = null;

        DB::transaction(function () use ($photoPath, &$oldPhotoToDelete) {
            if ($this->isEdit) {
                $guru = User::query()
                    ->role('guru')
                    ->findOrFail($this->guruId);

                if ($photoPath) {
                    $oldPhotoToDelete = $guru->photo;
                }

                $guru->update([
                    'name' => $this->name,
                    'username' => $this->username,
                    'photo' => $photoPath ?: $guru->photo,
                    'is_active' => (bool) $this->is_active,
                    'default_class_id' => $this->default_class_id ?: null,
                ]);

                $message = 'Data guru berhasil diperbarui.';
            } else {
                $guru = User::query()->create([
                    'name' => $this->name,
                    'username' => $this->username,
                    'email' => null,
                    'phone' => null,
                    'photo' => $photoPath,
                    'is_active' => (bool) $this->is_active,
                    'default_class_id' => $this->default_class_id ?: null,
                    'password' => Hash::make($this->username),
                ]);

                $guru->assignRole('guru');

                $message = 'Guru berhasil ditambahkan.';
            }

            $this->dispatch(
                'hadirku-toast',
                type: 'success',
                message: $message
            );
        });

        if ($oldPhotoToDelete) {
            Storage::disk('public')->delete($oldPhotoToDelete);
        }

        $this->resetForm();
        $this->showCreateModal = false;
    }

    public function edit($id): void
    {
        $guru = User::query()
            ->role('guru')
            ->findOrFail($id);

        $this->guruId = $guru->id;
        $this->name = $guru->name;
        $this->username = $guru->username;
        $this->photo = null;
        $this->existingPhoto = $guru->photo;
        $this->is_active = (bool) $guru->is_active;
        $this->default_class_id = $guru->default_class_id ?: '';
        $this->isEdit = true;

        $this->resetValidation();
    }

    public function delete($id): void
    {
        $guru = User::query()
            ->role('guru')
            ->findOrFail($id);

        DB::transaction(function () use ($guru) {
            $guru->delete();
        });

        if ($guru->photo) {
            Storage::disk('public')->delete($guru->photo);
        }

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Guru berhasil dihapus.'
        );
    }

    public function toggleActive($id): void
    {
        $guru = User::query()
            ->role('guru')
            ->findOrFail($id);

        $guru->update([
            'is_active' => ! $guru->is_active,
        ]);

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: $guru->is_active
                ? 'Guru berhasil diaktifkan.'
                : 'Guru berhasil dinonaktifkan.'
        );
    }

    public function removePhoto(): void
    {
        if (! $this->isEdit || ! $this->guruId) {
            $this->photo = null;

            return;
        }

        $guru = User::query()
            ->role('guru')
            ->findOrFail($this->guruId);

        $oldPhoto = $guru->photo;

        $guru->update([
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
            message: 'Foto guru berhasil dihapus.'
        );
    }

    public function resetPassword($id): void
    {
        $guru = User::query()
            ->role('guru')
            ->findOrFail($id);

        $guru->update([
            'password' => Hash::make($guru->username),
        ]);

        $this->dispatch(
            'hadirku-toast',
            type: 'success',
            message: 'Kata sandi guru berhasil diatur ulang ke nama pengguna.'
        );
    }

    public function render()
    {
        return view('livewire.admin.guru-management', [
            'gurus' => User::query()
                ->role('guru')
                ->with('defaultClass')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('username', 'like', '%' . $this->search . '%')
                            ->orWhereHas('defaultClass', function ($classQuery) {
                                $classQuery->where('name', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->statusFilter !== '', function ($query) {
                    $query->where('is_active', (bool) $this->statusFilter);
                })
                ->latest()
                ->paginate(10),

            'totalGuru' => User::query()
                ->role('guru')
                ->count(),

            'activeGuru' => User::query()
                ->role('guru')
                ->where('is_active', true)
                ->count(),

            'inactiveGuru' => User::query()
                ->role('guru')
                ->where('is_active', false)
                ->count(),

            'classes' => SchoolClass::query()
                ->orderBy('name')
                ->get(),
        ]);
    }
}
