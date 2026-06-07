<div
    x-data="{
        createModalOpen: @entangle('showCreateModal'),
        editModalOpen: @js($errors->has('edit_name') || $errors->has('edit_nis') || $errors->has('edit_class_id') || $errors->has('edit_gender') || $errors->has('edit_photo')),
        editAction: @js(old('edit_action', '')),
        editName: @js(old('edit_name', '')),
        editNis: @js(old('edit_nis', '')),
        editClassId: @js(old('edit_class_id', '')),
        editGender: @js(old('edit_gender', '')),
        editInitial: @js(old('edit_initial', 'S')),
        editPhotoUrl: @js(old('edit_photo_url', '')),
        editPhotoPreview: '',
        editRemovePhoto: @js(old('remove_photo', '0') === '1'),
        openEditModal(action, name, nis, classId, gender, photoUrl, initial) {
            this.editAction = action;
            this.editName = name || '';
            this.editNis = nis || '';
            this.editClassId = classId || '';
            this.editGender = gender || '';
            this.editPhotoUrl = photoUrl || '';
            this.editPhotoPreview = '';
            this.editInitial = initial || 'S';
            this.editRemovePhoto = false;
            this.editModalOpen = true;
            this.$nextTick(() => this.$refs.editStudentName?.focus());
        },
        closeEditModal() {
            this.editModalOpen = false;
            this.editAction = '';
            this.editName = '';
            this.editNis = '';
            this.editClassId = '';
            this.editGender = '';
            this.editPhotoUrl = '';
            this.editPhotoPreview = '';
            this.editInitial = 'S';
            this.editRemovePhoto = false;
        },
        previewEditPhoto(event) {
            const file = event.target.files?.[0];

            if (!file) {
                this.editPhotoPreview = '';
                return;
            }

            this.editRemovePhoto = false;
            this.editPhotoPreview = URL.createObjectURL(file);
        }
    }"
    x-on:keydown.escape.window="
        if (createModalOpen) $wire.closeCreateModal();
        if (editModalOpen) closeEditModal();
    "
>

    <div class="hk-page max-w-none">

        <section class="hk-card overflow-hidden">
                <div class="border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:px-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                                    Daftar Siswa
                                </h2>
                                <span class="hk-badge bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ $students->total() }} dari {{ $totalStudents }} siswa
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="hk-btn-primary"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                </svg>
                                Tambah Siswa
                            </button>

                            <button
                                type="button"
                                wire:click="openImportModal"
                                class="hk-btn-secondary"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                </svg>
                                Impor
                            </button>

                            <button
                                type="button"
                                wire:click="exportCsv"
                                wire:loading.attr="disabled"
                                wire:target="exportCsv"
                                class="hk-btn-secondary"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                                </svg>
                                <span wire:loading.remove wire:target="exportCsv">Ekspor</span>
                                <span wire:loading wire:target="exportCsv">Menyiapkan...</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 lg:grid-cols-[1fr_12rem]">
                        <label class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                wire:model.live.debounce.350ms="search"
                                autocomplete="off"
                                placeholder="Cari nama atau NIS..."
                                class="hk-input pl-12"
                            >
                        </label>

                        <select
                            wire:model.live="filterClass"
                            class="hk-input"
                        >
                            <option value="">Semua Kelas</option>

                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-3 px-5 py-5 md:hidden">
                    @forelse($students as $student)
                        @php
                            $descriptorCount = $student->descriptors_count ?? 0;
                            $isFaceReady = $descriptorCount >= 3;
                            $hasPartialFace = $descriptorCount > 0 && ! $isFaceReady;
                            $initial = strtoupper(substr($student->user?->name ?? 'S', 0, 1));
                            $photoUrl = $student->photo ? asset('storage/' . $student->photo) : '';
                        @endphp

                        <div class="rounded-2xl border border-slate-100 bg-white/75 p-3 dark:border-slate-800 dark:bg-slate-950/30" wire:key="student-card-{{ $student->id }}">
                            <div class="flex items-center gap-3">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}"
                                                 alt="Foto {{ $student->user?->name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                            {{ $student->user?->name }}
                                        </div>
                                        <div class="mt-1 flex flex-wrap gap-1.5">
                                            <span class="hk-badge flex w-fit bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                                {{ $student->class?->name ?? '-' }}
                                            </span>

                                            @if($isFaceReady)
                                                <span class="hk-badge flex w-fit bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                    Face ID {{ $descriptorCount }}
                                                </span>
                                            @elseif($hasPartialFace)
                                                <span class="hk-badge flex w-fit bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                                    Kurang {{ $descriptorCount }}/3
                                                </span>
                                            @else
                                                <span class="hk-badge flex w-fit bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                                    Face ID 0
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-0.5">
                                    <a
                                        href="{{ route('admin.face-registration', ['student' => $student->id]) }}"
                                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                        title="Registrasi wajah"
                                        aria-label="Registrasi wajah"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                        </svg>
                                    </a>

                                    <button
                                        type="button"
                                        x-on:click="openEditModal(
                                            @js(route('admin.students.update', $student)),
                                            @js($student->user?->name),
                                            @js($student->nis),
                                            @js((string) $student->class_id),
                                            @js($student->gender),
                                            @js($photoUrl),
                                            @js($initial)
                                        )"
                                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                        title="Edit siswa"
                                        aria-label="Edit siswa"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmAction({
                                                title: 'Atur ulang kata sandi siswa?',
                                                text: 'Kata sandi siswa akan diatur ulang menjadi NIS.',
                                                confirmText: 'Atur Ulang',
                                                icon: 'question',
                                                tone: 'warning'
                                            }).then(confirmed => {
                                                if (confirmed) $wire.resetPassword({{ $student->id }});
                                            });
                                        "
                                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                        title="Atur ulang kata sandi"
                                        aria-label="Atur ulang kata sandi"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 15.5-6.2M21 5v6h-6M21 12a9 9 0 0 1-15.5 6.2M3 19v-6h6" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmAction({
                                                title: 'Hapus siswa?',
                                                text: 'Data siswa dan akun masuknya akan dihapus.',
                                                confirmText: 'Hapus',
                                                icon: 'warning',
                                                tone: 'danger'
                                            }).then(confirmed => {
                                                if (confirmed) $wire.delete({{ $student->id }});
                                            });
                                        "
                                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                        title="Hapus siswa"
                                        aria-label="Hapus siswa"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700">
                            <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                Belum ada data siswa.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="hidden overflow-x-hidden md:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col class="w-[7%]">
                            <col class="w-[11%]">
                            <col class="w-[32%]">
                            <col class="w-[13%]">
                            <col class="w-[18%]">
                            <col class="w-[19%]">
                        </colgroup>

                        <thead class="bg-slate-50/90 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">
                            <tr>
                                <th class="px-2 py-4 text-left font-bold">Foto</th>
                                <th class="px-2 py-4 text-left font-bold">NIS</th>
                                <th class="px-2 py-4 text-left font-bold">Nama</th>
                                <th class="px-2 py-4 text-left font-bold">Kelas</th>
                                <th class="px-2 py-4 text-left font-bold">Face ID</th>
                                <th class="px-2 py-4 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($students as $student)
                                @php
                                    $descriptorCount = $student->descriptors_count ?? 0;
                                    $isFaceReady = $descriptorCount >= 3;
                                    $hasPartialFace = $descriptorCount > 0 && ! $isFaceReady;
                                    $initial = strtoupper(substr($student->user?->name ?? 'S', 0, 1));
                                    $photoUrl = $student->photo ? asset('storage/' . $student->photo) : '';
                                @endphp

                                <tr wire:key="student-{{ $student->id }}"
                                    class="transition hover:bg-blue-50/60 dark:hover:bg-slate-800/45">
                                    <td class="px-2 py-4">
                                        <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                            @if($student->photo)
                                                <img src="{{ asset('storage/' . $student->photo) }}"
                                                     alt="Foto {{ $student->user?->name }}"
                                                     class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0" />
                                                </svg>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-2 py-4 font-bold text-slate-700 dark:text-slate-200">
                                        <div class="truncate">
                                            {{ $student->nis }}
                                        </div>
                                    </td>

                                    <td class="min-w-0 px-2 py-4">
                                        <div class="truncate font-extrabold text-slate-900 dark:text-white">
                                            {{ $student->user?->name }}
                                        </div>
                                        <div class="mt-1 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            {{ $student->gender }}
                                        </div>
                                    </td>

                                    <td class="px-2 py-4">
                                        <span class="hk-badge flex max-w-full truncate bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                            {{ $student->class?->name ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-2 py-4">
                                        @if($isFaceReady)
                                            <span class="inline-flex max-w-full items-center gap-1 truncate text-sm font-bold text-emerald-600 dark:text-emerald-300">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                </svg>
                                                <span class="truncate">Terdaftar ({{ $descriptorCount }})</span>
                                            </span>
                                        @elseif($hasPartialFace)
                                            <span class="inline-flex max-w-full items-center gap-1 truncate text-sm font-bold text-amber-600 dark:text-amber-300">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                                                </svg>
                                                <span class="truncate">Kurang ({{ $descriptorCount }}/3)</span>
                                            </span>
                                        @else
                                            <span class="inline-flex max-w-full items-center gap-1 truncate text-sm font-bold text-amber-600 dark:text-amber-300">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                                                </svg>
                                                <span class="truncate">Face ID 0</span>
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-2 py-4">
                                        <div class="mx-auto flex max-w-36 items-center justify-center gap-0.5">
                                            <a
                                                href="{{ route('admin.face-registration', ['student' => $student->id]) }}"
                                                class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                                title="Registrasi wajah"
                                                aria-label="Registrasi wajah"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                                </svg>
                                            </a>

                                            <button
                                                type="button"
                                                x-on:click="openEditModal(
                                                    @js(route('admin.students.update', $student)),
                                                    @js($student->user?->name),
                                                    @js($student->nis),
                                                    @js((string) $student->class_id),
                                                    @js($student->gender),
                                                    @js($photoUrl),
                                                    @js($initial)
                                                )"
                                                class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                                title="Edit siswa"
                                                aria-label="Edit siswa"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="
                                                    confirmAction({
                                                        title: 'Atur ulang kata sandi siswa?',
                                                        text: 'Kata sandi siswa akan diatur ulang menjadi NIS.',
                                                        confirmText: 'Atur Ulang',
                                                        icon: 'question',
                                                        tone: 'warning'
                                                    }).then(confirmed => {
                                                        if (confirmed) $wire.resetPassword({{ $student->id }});
                                                    });
                                                "
                                                class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                                title="Atur ulang kata sandi"
                                                aria-label="Atur ulang kata sandi"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 15.5-6.2M21 5v6h-6M21 12a9 9 0 0 1-15.5 6.2M3 19v-6h6" />
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="
                                                    confirmAction({
                                                        title: 'Hapus siswa?',
                                                        text: 'Data siswa dan akun masuknya akan dihapus.',
                                                        confirmText: 'Hapus',
                                                        icon: 'warning',
                                                        tone: 'danger'
                                                    }).then(confirmed => {
                                                        if (confirmed) $wire.delete({{ $student->id }});
                                                    });
                                                "
                                                class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                                title="Hapus siswa"
                                                aria-label="Hapus siswa"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-14 text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0" />
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-300">
                                            Belum ada data siswa.
                                        </div>
                                        <div class="mt-1 text-xs font-semibold text-slate-400">
                                            Tambahkan siswa baru melalui tombol Tambah Siswa.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200/70 px-5 py-4 dark:border-slate-800 sm:px-6">
                    {{ $students->links() }}
                </div>
        </section>

    </div>

    <div
        x-show="createModalOpen"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
    >
        <section
            x-on:click.outside="$wire.closeCreateModal()"
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Data Siswa
                    </div>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        Tambah siswa baru
                    </h2>
                </div>

                <button
                    type="button"
                    wire:click="closeCreateModal"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Tutup formulir tambah siswa"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-5 p-5">
                <div>
                    <label class="hk-label text-center">Foto siswa</label>

                    <div class="flex flex-col items-center">
                        <label class="group flex h-32 w-32 cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 text-slate-500 transition hover:border-blue-300 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300 dark:hover:border-blue-500/70 dark:hover:bg-blue-500/10">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}"
                                     alt="Pratinjau foto siswa"
                                     class="h-full w-full object-cover">
                            @else
                                <span class="flex flex-col items-center gap-2">
                                    <svg class="h-8 w-8 transition group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                    </svg>
                                    <span class="text-sm font-bold">Unggah Foto</span>
                                </span>
                            @endif

                            <input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                                class="sr-only"
                            >
                        </label>

                        <div class="mt-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Format JPG, PNG, WEBP. Maksimal 1 MB.
                        </div>

                        <div wire:loading wire:target="photo"
                             class="mt-2 text-xs font-bold text-blue-600 dark:text-blue-300">
                            Mengunggah foto...
                        </div>

                        @if($photo)
                            <button
                                type="button"
                                x-on:click.stop="
                                    confirmAction({
                                        title: 'Hapus foto siswa?',
                                        text: 'Foto profil siswa akan dihapus dari formulir tambah siswa.',
                                        confirmText: 'Hapus Foto',
                                        icon: 'warning',
                                        tone: 'danger'
                                    }).then(confirmed => {
                                        if (confirmed) $wire.removePhoto();
                                    });
                                "
                                class="mt-3 text-sm font-bold text-rose-600 transition hover:text-rose-500 dark:text-rose-300"
                            >
                                Hapus foto
                            </button>
                        @endif
                    </div>

                    @error('photo')
                        <div class="hk-error text-center">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="hk-label">NIS (Nomor Induk Siswa)</label>
                        <input
                            type="text"
                            wire:model="nis"
                            placeholder="Masukkan NIS"
                            class="hk-input"
                        >
                        @error('nis')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror

                        <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Nama pengguna dan kata sandi awal otomatis memakai NIS.
                        </div>
                    </div>

                    <div>
                        <label class="hk-label">Nama lengkap</label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Masukkan nama lengkap"
                            class="hk-input"
                        >
                        @error('name')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Kelas</label>
                        <select
                            wire:model="class_id"
                            class="hk-input"
                        >
                            <option value="">Pilih kelas</option>

                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Jenis kelamin</label>
                        <select
                            wire:model="gender"
                            class="hk-input"
                        >
                            <option value="">Pilih jenis kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('gender')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeCreateModal"
                        class="hk-btn-secondary"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save,photo"
                        class="hk-btn-primary"
                    >
                        <span wire:loading.remove wire:target="save">
                            Simpan Siswa
                        </span>

                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </section>
    </div>

    <template x-teleport="body">
        <div
            x-show="editModalOpen"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
        >
            <section class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Edit Siswa
                        </div>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Perbarui data siswa
                        </h2>
                    </div>

                    <button
                        type="button"
                        x-on:click="closeEditModal()"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                        aria-label="Tutup edit siswa"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <form method="POST" x-bind:action="editAction" enctype="multipart/form-data" class="space-y-5 p-5">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="edit_action" x-bind:value="editAction">
                    <input type="hidden" name="edit_initial" x-bind:value="editInitial">
                    <input type="hidden" name="edit_photo_url" x-bind:value="editPhotoUrl">

                    <div>
                        <label class="hk-label text-center">Foto siswa</label>
                        <div class="flex flex-col items-center">
                            <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-3xl font-extrabold text-slate-500 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300">
                                <template x-if="!editRemovePhoto && (editPhotoPreview || editPhotoUrl)">
                                    <img x-bind:src="editPhotoPreview || editPhotoUrl"
                                         alt="Pratinjau foto siswa"
                                         class="h-full w-full object-cover">
                                </template>
                                <template x-if="editRemovePhoto || (!editPhotoPreview && !editPhotoUrl)">
                                    <span x-text="editInitial"></span>
                                </template>
                            </div>

                            <label class="hk-btn-secondary mt-4 cursor-pointer">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                </svg>
                                Pilih Foto
                                <input
                                    type="file"
                                    name="edit_photo"
                                    accept="image/*"
                                    x-on:change="previewEditPhoto($event)"
                                    class="sr-only"
                                >
                            </label>

                            <div class="mt-2 text-center text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Format JPG, PNG, WEBP. Maksimal 1 MB.
                            </div>

                            <label class="mt-3 flex items-center gap-2 text-sm font-bold text-rose-600 dark:text-rose-300">
                                <input
                                    type="checkbox"
                                    name="remove_photo"
                                    value="1"
                                    x-model="editRemovePhoto"
                                    class="h-5 w-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500 dark:border-slate-600 dark:bg-slate-900"
                                >
                                Hapus foto saat menyimpan
                            </label>
                        </div>

                        @error('edit_photo')
                            <div class="hk-error text-center">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="hk-label" for="edit-student-nis">NIS</label>
                            <input
                                id="edit-student-nis"
                                type="text"
                                name="edit_nis"
                                x-model="editNis"
                                class="hk-input"
                            >
                            @error('edit_nis')
                                <div class="hk-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="hk-label" for="edit-student-name">Nama lengkap</label>
                            <input
                                id="edit-student-name"
                                x-ref="editStudentName"
                                type="text"
                                name="edit_name"
                                x-model="editName"
                                class="hk-input"
                            >
                            @error('edit_name')
                                <div class="hk-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="hk-label" for="edit-student-class">Kelas</label>
                            <select
                                id="edit-student-class"
                                name="edit_class_id"
                                x-model="editClassId"
                                class="hk-input"
                            >
                                <option value="">Pilih kelas</option>

                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('edit_class_id')
                                <div class="hk-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="hk-label" for="edit-student-gender">Jenis kelamin</label>
                            <select
                                id="edit-student-gender"
                                name="edit_gender"
                                x-model="editGender"
                                class="hk-input"
                            >
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('edit_gender')
                                <div class="hk-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-slate-800 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            x-on:click="closeEditModal()"
                            class="hk-btn-secondary"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="hk-btn-primary"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </template>

    @if($showImportModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm">
            <section class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Impor Siswa
                        </div>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Impor Data Siswa
                        </h2>
                    </div>

                    <button
                        type="button"
                        wire:click="closeImportModal"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                        aria-label="Tutup impor siswa"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-5 p-5">
                    <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                        Kolom wajib: nis, nama, kelas, gender.
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            wire:click="downloadImportTemplate"
                            wire:loading.attr="disabled"
                            wire:target="downloadImportTemplate"
                            class="hk-btn-secondary"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                            </svg>
                            Unduh Berkas Template
                        </button>

                        <label class="hk-btn-secondary cursor-pointer">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                            </svg>
                            <span class="max-w-56 truncate">
                                {{ $importFile ? $importFile->getClientOriginalName() : 'Pilih Berkas' }}
                            </span>
                            <input
                                type="file"
                                wire:model="importFile"
                                accept=".csv,.txt,.xlsx,.xls"
                                class="sr-only"
                            >
                        </label>
                    </div>

                    <div wire:loading wire:target="importFile,previewImportFile" class="text-sm font-bold text-blue-600 dark:text-blue-300">
                        Membaca berkas impor...
                    </div>

                    @error('importFile')
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                            {{ $message }}
                        </div>
                    @enderror

                    @if($importPreviewRows)
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="hk-badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                {{ count($importPreviewRows) }} baris
                            </span>

                            <span class="hk-badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                {{ $importValidCount }} valid
                            </span>

                            @if($importInvalidCount > 0)
                                <span class="hk-badge bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">
                                    {{ $importInvalidCount }} perlu diperbaiki
                                </span>
                            @endif
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800">
                            <div class="max-h-80 overflow-auto">
                                <table class="w-full min-w-[640px] text-sm">
                                    <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-bold">#</th>
                                            <th class="px-4 py-3 text-left font-bold">NIS</th>
                                            <th class="px-4 py-3 text-left font-bold">Nama</th>
                                            <th class="px-4 py-3 text-left font-bold">Kelas</th>
                                            <th class="px-4 py-3 text-left font-bold">Jenis Kelamin</th>
                                            <th class="px-4 py-3 text-left font-bold">Status</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @foreach($importPreviewRows as $previewRow)
                                            <tr>
                                                <td class="px-4 py-3 font-semibold text-slate-500 dark:text-slate-400">
                                                    {{ $loop->iteration }}
                                                </td>

                                                <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-200">
                                                    {{ $previewRow['nis'] ?: '-' }}
                                                </td>

                                                <td class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                                                    {{ $previewRow['name'] ?: '-' }}
                                                </td>

                                                <td class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                                                    {{ $previewRow['class'] ?: '-' }}
                                                </td>

                                                <td class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                                                    {{ $previewRow['gender'] ?: '-' }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    @if($previewRow['valid'])
                                                        <span class="inline-flex items-center gap-2 text-sm font-bold text-emerald-600 dark:text-emerald-300">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                            </svg>
                                                            Valid
                                                        </span>
                                                    @else
                                                        <span class="inline-flex max-w-xs items-center gap-2 text-sm font-bold text-rose-600 dark:text-rose-300">
                                                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                                                            </svg>
                                                            <span class="truncate">
                                                                {{ $previewRow['status'] }}
                                                            </span>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-5 py-5 dark:border-slate-800 sm:flex-row sm:justify-end sm:px-6">
                    <button
                        type="button"
                        wire:click="closeImportModal"
                        class="hk-btn-secondary"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="importStudents"
                        wire:loading.attr="disabled"
                        wire:target="importStudents"
                        class="hk-btn-primary"
                        @disabled(! $importFile || $importValidCount === 0 || $importInvalidCount > 0)
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                        </svg>
                        <span wire:loading.remove wire:target="importStudents">
                            Impor {{ $importValidCount }} Siswa
                        </span>
                        <span wire:loading wire:target="importStudents">
                            Mengimpor...
                        </span>
                    </button>
                </div>
            </section>
        </div>
    @endif

</div>
