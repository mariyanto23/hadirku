<div
    x-data="{
        createModalOpen: @entangle('showCreateModal'),
        editModalOpen: @js($errors->has('edit_name') || $errors->has('edit_username') || $errors->has('edit_default_class_id') || $errors->has('edit_photo')),
        editAction: @js(old('edit_action', '')),
        editName: @js(old('edit_name', '')),
        editUsername: @js(old('edit_username', '')),
        editDefaultClassId: @js(old('edit_default_class_id', '')),
        editInitial: @js(old('edit_initial', 'G')),
        editPhotoUrl: @js(old('edit_photo_url', '')),
        editPhotoPreview: '',
        editIsActive: @js(old('edit_is_active', '1') === '1'),
        editRemovePhoto: @js(old('remove_photo', '0') === '1'),
        openEditModal(action, name, username, defaultClassId, active, photoUrl, initial) {
            this.editAction = action;
            this.editName = name;
            this.editUsername = username;
            this.editDefaultClassId = defaultClassId || '';
            this.editIsActive = active;
            this.editPhotoUrl = photoUrl || '';
            this.editPhotoPreview = '';
            this.editInitial = initial || 'G';
            this.editRemovePhoto = false;
            this.editModalOpen = true;
            this.$nextTick(() => this.$refs.editGuruName?.focus());
        },
        closeEditModal() {
            this.editModalOpen = false;
            this.editAction = '';
            this.editName = '';
            this.editUsername = '';
            this.editDefaultClassId = '';
            this.editPhotoUrl = '';
            this.editPhotoPreview = '';
            this.editInitial = 'G';
            this.editIsActive = true;
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

    <div class="hk-page">

        <section class="grid grid-cols-3 gap-2 sm:gap-4">
            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                            Total
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $totalGuru }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0M19 4v6M22 7h-6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300 sm:text-sm">
                            Aktif
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $activeGuru }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 sm:text-sm">
                            Nonaktif
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $inactiveGuru }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <section class="hk-card p-5 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Daftar Guru
                        </div>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Kelola akun pengajar
                        </h2>
                    </div>

                    <div class="flex flex-col gap-3 lg:items-end">
                        <button
                            type="button"
                            wire:click="openCreateModal"
                            class="hk-btn-primary w-full sm:w-auto"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Guru
                        </button>

                        <div class="grid gap-3 sm:grid-cols-2 lg:w-[28rem]">
                            <input
                                type="text"
                                wire:model.live="search"
                                placeholder="Cari nama, pengguna, kelas..."
                                class="hk-input"
                            >

                            <select
                                wire:model.live="statusFilter"
                                class="hk-input"
                            >
                                <option value="">
                                    Semua Status
                                </option>
                                <option value="1">
                                    Aktif
                                </option>
                                <option value="0">
                                    Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-3 md:hidden">
                    @forelse($gurus as $guru)
                        @php
                            $initial = strtoupper(substr($guru->name, 0, 1));
                            $photoUrl = $guru->photo ? asset('storage/' . $guru->photo) : '';
                        @endphp

                        <div class="rounded-2xl border border-slate-100 bg-white/75 p-3 dark:border-slate-800 dark:bg-slate-950/30" wire:key="guru-card-{{ $guru->id }}">
                            <div class="flex items-center gap-3">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                        @if($guru->photo)
                                            <img src="{{ $photoUrl }}"
                                                 alt="Foto {{ $guru->name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="truncate text-base font-extrabold text-slate-900 dark:text-white">
                                            {{ $guru->name }}
                                        </div>
                                        <div class="hidden truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            {{ $guru->username }}
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @if($guru->is_active)
                                                <span class="hk-badge flex w-fit bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="hk-badge flex w-fit bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid w-20 shrink-0 grid-cols-2 place-items-center gap-1">
                                    <button
                                        type="button"
                                        x-on:click="openEditModal(
                                            @js(route('admin.gurus.update', $guru)),
                                            @js($guru->name),
                                            @js($guru->username),
                                            @js((string) ($guru->default_class_id ?? '')),
                                            @js((bool) $guru->is_active),
                                            @js($photoUrl),
                                            @js($initial)
                                        )"
                                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                        title="Edit guru"
                                        aria-label="Edit guru"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                        </svg>
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.gurus.toggle-status', $guru) }}"
                                        class="flex"
                                        x-on:submit.prevent="
                                            confirmAction({
                                                title: '{{ $guru->is_active ? 'Nonaktifkan guru?' : 'Aktifkan guru?' }}',
                                                text: '{{ $guru->is_active ? 'Guru ini tidak bisa masuk sampai diaktifkan lagi.' : 'Guru ini akan bisa masuk kembali.' }}',
                                                confirmText: '{{ $guru->is_active ? 'Nonaktifkan' : 'Aktifkan' }}',
                                                icon: '{{ $guru->is_active ? 'warning' : 'question' }}',
                                                tone: '{{ $guru->is_active ? 'warning' : 'success' }}'
                                            }).then(confirmed => {
                                                if (confirmed) $el.submit();
                                            });
                                        "
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition {{ $guru->is_active ? 'hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200' : 'hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-slate-800 dark:hover:text-emerald-300' }} dark:text-slate-300"
                                            title="{{ $guru->is_active ? 'Nonaktifkan guru' : 'Aktifkan guru' }}"
                                            aria-label="{{ $guru->is_active ? 'Nonaktifkan guru' : 'Aktifkan guru' }}"
                                        >
                                            @if($guru->is_active)
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmAction({
                                                title: 'Atur ulang kata sandi?',
                                                text: 'Kata sandi guru akan diatur ulang menjadi nama pengguna.',
                                                confirmText: 'Atur Ulang',
                                                icon: 'question',
                                                tone: 'warning'
                                            }).then(confirmed => {
                                                if (confirmed) $wire.resetPassword({{ $guru->id }});
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
                                                title: 'Hapus guru?',
                                                text: 'Lebih aman menonaktifkan akun jika guru masih berkaitan dengan data presensi.',
                                                confirmText: 'Hapus',
                                                icon: 'warning',
                                                tone: 'danger'
                                            }).then(confirmed => {
                                                if (confirmed) $wire.delete({{ $guru->id }});
                                            });
                                        "
                                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                        title="Hapus guru"
                                        aria-label="Hapus guru"
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
                                Belum ada data guru.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="hk-table-wrap mt-6 hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="hk-table min-w-[760px]">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Kelas Bawaan</th>
                                    <th class="w-28 text-center">Status</th>
                                    <th class="w-32 text-center">Dibuat</th>
                                    <th class="w-52 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($gurus as $guru)
                                    @php
                                        $initial = strtoupper(substr($guru->name, 0, 1));
                                        $photoUrl = $guru->photo ? asset('storage/' . $guru->photo) : '';
                                    @endphp

                                    <tr wire:key="guru-row-{{ $guru->id }}">
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                                    @if($guru->photo)
                                                        <img src="{{ $photoUrl }}"
                                                             alt="Foto {{ $guru->name }}"
                                                             class="h-full w-full object-cover">
                                                    @else
                                                        {{ $initial }}
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate font-extrabold text-slate-900 dark:text-white">
                                                        {{ $guru->name }}
                                                    </div>
                                                    <div class="truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                        {{ $guru->username }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="hk-badge bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                                {{ $guru->defaultClass?->name ?: 'Belum dikaitkan' }}
                                            </span>
                                        </td>

                                        <td class="w-28 text-center">
                                            @if($guru->is_active)
                                                <span class="hk-badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="hk-badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="w-32 text-center font-semibold">
                                            {{ $guru->created_at?->translatedFormat('d F Y') }}
                                        </td>

                                        <td class="w-52">
                                            <div class="mx-auto flex w-48 flex-wrap justify-center gap-2">
                                                <button
                                                    type="button"
                                                    x-on:click="openEditModal(
                                                        @js(route('admin.gurus.update', $guru)),
                                                        @js($guru->name),
                                                        @js($guru->username),
                                                        @js((string) ($guru->default_class_id ?? '')),
                                                        @js((bool) $guru->is_active),
                                                        @js($photoUrl),
                                                        @js($initial)
                                                    )"
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                                    title="Edit guru"
                                                    aria-label="Edit guru"
                                                >
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                    </svg>
                                                </button>

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.gurus.toggle-status', $guru) }}"
                                                    x-on:submit.prevent="
                                                        confirmAction({
                                                            title: '{{ $guru->is_active ? 'Nonaktifkan guru?' : 'Aktifkan guru?' }}',
                                                            text: '{{ $guru->is_active ? 'Guru ini tidak bisa masuk sampai diaktifkan lagi.' : 'Guru ini akan bisa masuk kembali.' }}',
                                                            confirmText: '{{ $guru->is_active ? 'Nonaktifkan' : 'Aktifkan' }}',
                                                            icon: '{{ $guru->is_active ? 'warning' : 'question' }}',
                                                            tone: '{{ $guru->is_active ? 'warning' : 'success' }}'
                                                        }).then(confirmed => {
                                                            if (confirmed) $el.submit();
                                                        });
                                                    "
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition {{ $guru->is_active ? 'hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200' : 'hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-slate-800 dark:hover:text-emerald-300' }} dark:text-slate-300"
                                                        title="{{ $guru->is_active ? 'Nonaktifkan guru' : 'Aktifkan guru' }}"
                                                        aria-label="{{ $guru->is_active ? 'Nonaktifkan guru' : 'Aktifkan guru' }}"
                                                    >
                                                        @if($guru->is_active)
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>

                                                <button
                                                    type="button"
                                                    x-on:click="
                                                        confirmAction({
                                                            title: 'Atur ulang kata sandi?',
                                                            text: 'Kata sandi guru akan diatur ulang menjadi nama pengguna.',
                                                            confirmText: 'Atur Ulang',
                                                            icon: 'question',
                                                            tone: 'warning'
                                                        }).then(confirmed => {
                                                            if (confirmed) $wire.resetPassword({{ $guru->id }});
                                                        });
                                                    "
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                                    title="Atur ulang kata sandi"
                                                    aria-label="Atur ulang kata sandi"
                                                >
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 15.5-6.2M21 5v6h-6M21 12a9 9 0 0 1-15.5 6.2M3 19v-6h6" />
                                                    </svg>
                                                </button>

                                                <button
                                                    type="button"
                                                    x-on:click="
                                                        confirmAction({
                                                            title: 'Hapus guru?',
                                                            text: 'Lebih aman menonaktifkan akun jika guru masih berkaitan dengan data presensi.',
                                                            confirmText: 'Hapus',
                                                            icon: 'warning',
                                                            tone: 'danger'
                                                        }).then(confirmed => {
                                                            if (confirmed) $wire.delete({{ $guru->id }});
                                                        });
                                                    "
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                                    title="Hapus guru"
                                                    aria-label="Hapus guru"
                                                >
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="py-10 text-center">
                                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0" />
                                                    </svg>
                                                </div>
                                                <div class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-300">
                                                    Belum ada data guru.
                                                </div>
                                                <div class="mt-1 text-xs font-semibold text-slate-400">
                                                    Tambahkan guru baru untuk memberi akses dashboard guru.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $gurus->links() }}
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
                        Data Guru
                    </div>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        Tambah guru baru
                    </h2>
                </div>

                <button
                    type="button"
                    wire:click="closeCreateModal"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Tutup formulir tambah guru"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-5 p-5">
                <div>
                    <label class="hk-label">Foto guru</label>

                    <div class="flex flex-col items-center gap-4 rounded-2xl border border-slate-200 bg-white/60 p-4 text-center dark:border-slate-700 dark:bg-slate-950/30 sm:flex-row sm:items-center sm:text-left">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 text-2xl font-extrabold text-white">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}"
                                     alt="Pratinjau foto guru"
                                     class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($name ?: 'G', 0, 1)) }}
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                                class="hk-input file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-blue-300"
                            >

                            <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Format JPG, PNG, atau WEBP. Maksimal 1 MB.
                            </div>

                            <div wire:loading wire:target="photo"
                                 class="mt-2 text-xs font-bold text-blue-600 dark:text-blue-300">
                                Mengunggah foto...
                            </div>
                        </div>
                    </div>

                    @error('photo')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="hk-label">Nama guru</label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Nama lengkap guru"
                            class="hk-input"
                        >
                        @error('name')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Nama pengguna</label>
                        <input
                            type="text"
                            wire:model="username"
                            placeholder="Contoh: guru_ani"
                            class="hk-input"
                        >
                        @error('username')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror

                        <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Guru masuk memakai nama pengguna. Kata sandi awal otomatis sama dengan nama pengguna.
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="hk-label">Kelas bawaan</label>
                        <select
                            wire:model="default_class_id"
                            class="hk-input"
                        >
                            <option value="">Belum dikaitkan</option>

                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Jika diisi, kelas ini otomatis terpilih saat guru membuka presensi.
                        </div>
                        @error('default_class_id')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <label class="flex min-h-14 cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 dark:border-slate-700 dark:bg-slate-950/40">
                    <span>
                        <span class="block text-sm font-bold text-slate-800 dark:text-white">
                            Status akun aktif
                        </span>
                        <span class="mt-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Guru nonaktif tidak dapat masuk ke dashboard.
                        </span>
                    </span>

                    <input
                        type="checkbox"
                        wire:model="is_active"
                        class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900"
                    >
                </label>

                @error('is_active')
                    <div class="hk-error">{{ $message }}</div>
                @enderror

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
                            Tambah Guru
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
                            Edit Guru
                        </div>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Perbarui data guru
                        </h2>
                    </div>

                    <button
                        type="button"
                        x-on:click="closeEditModal()"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                        aria-label="Tutup edit guru"
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
                    <input type="hidden" name="edit_is_active" value="0">

                    <div>
                        <label class="hk-label">Foto guru</label>
                        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white/60 p-4 dark:border-slate-700 dark:bg-slate-950/30 sm:flex-row sm:items-center">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 text-2xl font-extrabold text-white">
                                <template x-if="!editRemovePhoto && (editPhotoPreview || editPhotoUrl)">
                                    <img x-bind:src="editPhotoPreview || editPhotoUrl"
                                         alt="Pratinjau foto guru"
                                         class="h-full w-full object-cover">
                                </template>
                                <template x-if="editRemovePhoto || (!editPhotoPreview && !editPhotoUrl)">
                                    <span x-text="editInitial"></span>
                                </template>
                            </div>

                            <div class="min-w-0 flex-1">
                                <input
                                    type="file"
                                    name="edit_photo"
                                    accept="image/*"
                                    x-on:change="previewEditPhoto($event)"
                                    class="hk-input file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-blue-300"
                                >

                                <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    Format JPG, PNG, atau WEBP. Maksimal 1 MB.
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
                        </div>

                        @error('edit_photo')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="hk-label" for="edit-guru-name">Nama guru</label>
                            <input
                                id="edit-guru-name"
                                x-ref="editGuruName"
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
                            <label class="hk-label" for="edit-guru-username">Nama pengguna</label>
                            <input
                                id="edit-guru-username"
                                type="text"
                                name="edit_username"
                                x-model="editUsername"
                                class="hk-input"
                            >
                            @error('edit_username')
                                <div class="hk-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="hk-label" for="edit-guru-default-class">Kelas bawaan</label>
                            <select
                                id="edit-guru-default-class"
                                name="edit_default_class_id"
                                x-model="editDefaultClassId"
                                class="hk-input"
                            >
                                <option value="">Belum dikaitkan</option>

                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Kelas ini otomatis terpilih saat guru membuka presensi.
                            </div>
                            @error('edit_default_class_id')
                                <div class="hk-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <label class="flex min-h-14 cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 dark:border-slate-700 dark:bg-slate-950/40">
                        <span>
                            <span class="block text-sm font-bold text-slate-800 dark:text-white">
                                Status akun aktif
                            </span>
                            <span class="mt-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Guru nonaktif tidak dapat masuk ke dashboard.
                            </span>
                        </span>

                        <input
                            type="checkbox"
                            name="edit_is_active"
                            value="1"
                            x-model="editIsActive"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900"
                        >
                    </label>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
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

</div>
