<div
    x-data="{
        editModalOpen: @js($errors->has('class_name')),
        editAction: @js(old('edit_action', '')),
        editName: @js(old('class_name', '')),
        openEditModal(action, name) {
            this.editAction = action;
            this.editName = name;
            this.editModalOpen = true;
            this.$nextTick(() => this.$refs.editClassInput?.focus());
        },
        closeEditModal() {
            this.editModalOpen = false;
            this.editAction = '';
            this.editName = '';
        }
    }"
    x-on:keydown.escape.window="if (editModalOpen) closeEditModal()"
>

    <div class="hk-page">

        <section class="grid gap-6 2xl:grid-cols-[24rem_1fr]">

            <div id="class-form-card" class="hk-card p-5 sm:p-6">
                <div class="mb-6">
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Manajemen Kelas
                    </div>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        Tambah kelas
                    </h1>
                </div>

                <form wire:submit="save" class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end 2xl:block 2xl:space-y-4">
                    <div class="2xl:mb-4">
                        <label class="hk-label">Nama kelas</label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: Kelas 1"
                            class="hk-input"
                        >

                        @error('name')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="hk-btn-primary w-full md:w-auto 2xl:w-full"
                    >
                        <span wire:loading.remove wire:target="save">
                            Tambah Kelas
                        </span>

                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </form>
            </div>

            <div class="hk-card p-5 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Daftar Kelas
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                                Kelas aktif
                            </h2>
                            <span class="hk-badge bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                {{ $classes->total() }} dari {{ $totalClasses }} kelas
                            </span>
                        </div>
                    </div>

                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Cari kelas..."
                        class="hk-input sm:w-72"
                    >
                </div>

                <div class="mt-6 space-y-3 md:hidden">
                    @forelse($classes as $class)
                        @php
                            $studentsCount = $class->students_count ?? 0;
                            $faceReadyCount = $class->face_ready_students_count ?? 0;
                            $faceIncompleteCount = max($studentsCount - $faceReadyCount, 0);
                        @endphp

                        <div class="rounded-2xl border border-slate-100 bg-white/75 p-4 dark:border-slate-800 dark:bg-slate-950/30" wire:key="class-card-{{ $class->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-base font-extrabold text-slate-900 dark:text-white">
                                        {{ $class->name }}
                                    </div>

                                    <div class="mt-2 space-y-2">
                                        <span class="hk-badge flex w-fit bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $studentsCount }} siswa
                                        </span>

                                        @if($studentsCount === 0)
                                            <span class="hk-badge flex w-fit bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                Face ID -
                                            </span>
                                        @elseif($faceIncompleteCount === 0)
                                            <span class="hk-badge flex w-fit bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                Face ID siap {{ $faceReadyCount }}/{{ $studentsCount }}
                                            </span>
                                        @else
                                            <span class="hk-badge flex w-fit bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                                Face ID kurang {{ $faceIncompleteCount }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex shrink-0 self-stretch items-center gap-1">
                                    <button
                                        type="button"
                                        x-on:click="openEditModal(@js(route('admin.classes.update', $class)), @js($class->name))"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                        title="Edit kelas"
                                        aria-label="Edit kelas"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmAction({
                                                title: 'Hapus kelas?',
                                                text: 'Kelas hanya bisa dihapus jika belum memiliki siswa.',
                                                confirmText: 'Hapus',
                                                icon: 'warning',
                                                tone: 'danger'
                                            }).then(confirmed => {
                                                if (confirmed) $wire.delete({{ $class->id }});
                                            });
                                        "
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                        title="Hapus kelas"
                                        aria-label="Hapus kelas"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700">
                            <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                Belum ada data kelas.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="hk-table-wrap mt-6 hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="hk-table min-w-[680px]">
                            <thead>
                                <tr>
                                    <th>Nama Kelas</th>
                                    <th class="w-28 text-center">Siswa</th>
                                    <th class="w-40 text-center">Face ID</th>
                                    <th class="w-32 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($classes as $class)
                                    @php
                                        $studentsCount = $class->students_count ?? 0;
                                        $faceReadyCount = $class->face_ready_students_count ?? 0;
                                        $faceIncompleteCount = max($studentsCount - $faceReadyCount, 0);
                                    @endphp

                                    <tr wire:key="class-row-{{ $class->id }}">
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12M7 9h10M7 13h6M5 19h14" />
                                                    </svg>
                                                </div>
                                                <div class="font-extrabold text-slate-900 dark:text-white">
                                                    {{ $class->name }}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="w-28 text-center">
                                            <span class="font-extrabold text-slate-900 dark:text-white">
                                                {{ $studentsCount }}
                                            </span>
                                        </td>

                                        <td class="w-40 text-center">
                                            @if($studentsCount === 0)
                                                <span class="text-sm font-bold text-slate-400">
                                                    -
                                                </span>
                                            @elseif($faceIncompleteCount === 0)
                                                <span class="inline-flex items-center justify-center gap-1.5 text-sm font-bold text-emerald-600 dark:text-emerald-300">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                    </svg>
                                                    Siap {{ $faceReadyCount }}/{{ $studentsCount }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center gap-1.5 text-sm font-bold text-amber-600 dark:text-amber-300">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                                                    </svg>
                                                    Kurang {{ $faceIncompleteCount }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="w-32">
                                            <div class="mx-auto flex w-24 justify-center gap-2">
                                                <button
                                                    type="button"
                                                    x-on:click="openEditModal(@js(route('admin.classes.update', $class)), @js($class->name))"
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                                    title="Edit kelas"
                                                    aria-label="Edit kelas"
                                                >
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                    </svg>
                                                </button>

                                                <button
                                                    type="button"
                                                    x-on:click="
                                                        confirmAction({
                                                            title: 'Hapus kelas?',
                                                            text: 'Kelas hanya bisa dihapus jika belum memiliki siswa.',
                                                            confirmText: 'Hapus',
                                                            icon: 'warning',
                                                            tone: 'danger'
                                                        }).then(confirmed => {
                                                            if (confirmed) $wire.delete({{ $class->id }});
                                                        });
                                                    "
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                                    title="Hapus kelas"
                                                    aria-label="Hapus kelas"
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
                                        <td colspan="4">
                                            <div class="py-8 text-center">
                                                <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                                    Belum ada data kelas.
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
                    {{ $classes->links() }}
                </div>
            </div>

        </section>

    </div>

    <template x-teleport="body">
        <div
            x-show="editModalOpen"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
        >
            <section class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Edit Kelas
                        </div>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Perbarui nama kelas
                        </h2>
                    </div>

                    <button
                        type="button"
                        x-on:click="closeEditModal()"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                        aria-label="Tutup edit kelas"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <form method="POST" x-bind:action="editAction" class="space-y-5 p-5">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="edit_action" x-bind:value="editAction">

                    <div>
                        <label class="hk-label" for="edit-class-name">Nama kelas</label>
                        <input
                            id="edit-class-name"
                            x-ref="editClassInput"
                            type="text"
                            name="class_name"
                            x-model="editName"
                            class="hk-input"
                            placeholder="Contoh: Kelas 1"
                        >

                        @error('class_name')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

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
