<div
    x-data="{
        faceModalOpen: false,
        modalStudentId: '',
        modalStudentName: '',
        modalStudentMeta: '',
        modalDescriptorCount: 0,
        modalMaxDescriptors: @js($maxDescriptors),
        faceRegistrationMode: window.hadirkuFaceRegistrationMode || 'camera',
        openFaceRegistrationModal(id, name, meta, count, maxDescriptors) {
            this.modalStudentId = String(id);
            this.modalStudentName = name || 'Siswa';
            this.modalStudentMeta = meta || '';
            this.modalDescriptorCount = Number(count || 0);
            this.modalMaxDescriptors = Number(maxDescriptors || this.modalMaxDescriptors || 10);
            this.faceRegistrationMode = window.hadirkuFaceRegistrationMode || 'camera';
            window.hadirkuSelectedFaceStudentId = this.modalStudentId;
            this.faceModalOpen = true;
            this.$nextTick(() => {
                const selectedStudent = document.getElementById('selectedStudentId');
                if (selectedStudent) selectedStudent.value = this.modalStudentId;
                window.hadirkuUpdateFaceModalProgress?.(this.modalDescriptorCount, this.modalMaxDescriptors);
                window.hadirkuInitFaceRegistrationPage?.();
            });
        },
        closeFaceRegistrationModal() {
            window.hadirkuStopFaceRegistrationCamera?.({ silent: true });
            this.faceRegistrationMode = 'camera';
            window.hadirkuFaceRegistrationMode = 'camera';
            this.faceModalOpen = false;
        }
    }"
    x-init="
        window.hadirkuSetFaceModalCount = count => {
            modalDescriptorCount = Number(count || 0);
            window.hadirkuUpdateFaceModalProgress?.(modalDescriptorCount, modalMaxDescriptors);
        };
        @if($canSelectStudent && $student)
            $nextTick(() => openFaceRegistrationModal(
                @js($student->id),
                @js($student->user?->name),
                @js(($student->class?->name ?? '-') . ' - NIS ' . ($student->nis ?? '-')),
                @js($descriptorCount),
                @js($maxDescriptors)
            ));
        @endif
    "
    x-on:hadirku-close-face-modal.window="closeFaceRegistrationModal()"
    x-on:keydown.escape.window="if (faceModalOpen) closeFaceRegistrationModal()"
>

    <div class="hk-page max-w-5xl">

        @if($canSelectStudent)
            <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
                <div class="hk-card p-3 sm:p-5">
                    <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                                Total Siswa
                            </div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                                {{ $totalStudents }}
                            </div>
                        </div>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="hk-card p-3 sm:p-5">
                    <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300 sm:text-sm">
                                Face ID Siap
                            </div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                                {{ $faceReadyStudents }}
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
                            <div class="text-[10px] font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300 sm:text-sm">
                                Face ID Kurang
                            </div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                                {{ $facePartialStudents }}
                            </div>
                        </div>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="hk-card p-3 sm:p-5">
                    <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wide text-rose-600 dark:text-rose-300 sm:text-sm">
                                Face ID Kosong
                            </div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                                {{ $faceEmptyStudents }}
                            </div>
                        </div>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="hk-card overflow-hidden">

            <div class="border-b border-slate-200/70 p-5 dark:border-slate-800 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Registrasi Wajah
                        </div>

                        <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Registrasi Face ID Siswa
                        </h1>
                    </div>

                    @unless($canSelectStudent)
                        <div id="captureStatus" wire:ignore class="w-fit rounded-2xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            Kamera belum aktif
                        </div>
                    @endunless
                </div>
            </div>

            <div class="space-y-6 p-4 sm:p-6">
                @if($canSelectStudent)
                    <section class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-950/30 sm:p-5">
                        <div class="grid gap-3 lg:grid-cols-[1fr_12rem_12rem]">
                            <input
                                type="text"
                                wire:model.live.debounce.350ms="search"
                                placeholder="Cari nama atau NIS..."
                                class="hk-input"
                            >

                            <select
                                wire:model.live="classFilter"
                                class="hk-input"
                            >
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select
                                wire:model.live="faceFilter"
                                class="hk-input"
                            >
                                <option value="">Semua Face ID</option>
                                <option value="ready">Siap</option>
                                <option value="partial">Kurang</option>
                                <option value="empty">Kosong</option>
                            </select>
                        </div>
                    </section>

                    <section class="space-y-3 md:hidden">
                        @forelse($students as $studentOption)
                            @php
                                $count = $studentOption->descriptors_count ?? 0;
                                $isReady = $count >= 3;
                                $isPartial = $count > 0 && ! $isReady;
                                $initial = strtoupper(substr($studentOption->user?->name ?? 'S', 0, 1));
                                $meta = ($studentOption->class?->name ?? '-') . ' - NIS ' . ($studentOption->nis ?? '-');
                            @endphp

                            <div class="rounded-2xl border border-slate-100 bg-white/75 p-3 dark:border-slate-800 dark:bg-slate-950/30" wire:key="face-card-{{ $studentOption->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                        @if($studentOption->photo)
                                            <img src="{{ asset('storage/' . $studentOption->photo) }}"
                                                 alt="Foto {{ $studentOption->user?->name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                            {{ $studentOption->user?->name }}
                                        </div>
                                        <div class="mt-1 flex flex-wrap gap-1.5">
                                            <span class="hk-badge bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                                {{ $studentOption->class?->name ?? '-' }}
                                            </span>

                                            @if($isReady)
                                                <span class="hk-badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                    Face ID {{ $count }}/{{ $maxDescriptors }}
                                                </span>
                                            @elseif($isPartial)
                                                <span class="hk-badge bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                                    Kurang {{ $count }}/3
                                                </span>
                                            @else
                                                <span class="hk-badge bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">
                                                    Face ID 0
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-0.5">
                                        <button
                                            type="button"
                                            x-on:click="openFaceRegistrationModal(@js($studentOption->id), @js($studentOption->user?->name), @js($meta), @js($count), @js($maxDescriptors))"
                                            class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                            title="Registrasi wajah"
                                            aria-label="Registrasi wajah"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="
                                                confirmAction({
                                                    title: 'Reset descriptor siswa?',
                                                    text: 'Semua data Face ID siswa ini akan dihapus.',
                                                    confirmText: 'Reset',
                                                    icon: 'warning',
                                                    tone: 'danger'
                                                }).then(confirmed => {
                                                    if (confirmed) $wire.resetStudentDescriptors({{ $studentOption->id }});
                                                });
                                            "
                                            class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                            title="Reset descriptor"
                                            aria-label="Reset descriptor"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 15.5-6.2M21 5v6h-6M21 12a9 9 0 0 1-15.5 6.2M3 19v-6h6" />
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
                    </section>

                    <section class="hidden overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 md:block">
                        <table class="w-full table-fixed text-sm">
                            <colgroup>
                                <col class="w-[9%]">
                                <col class="w-[13%]">
                                <col class="w-[30%]">
                                <col class="w-[16%]">
                                <col class="w-[18%]">
                                <col class="w-[14%]">
                            </colgroup>
                            <thead class="bg-slate-50/90 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">
                                <tr>
                                    <th class="px-3 py-4 text-left font-bold">Foto</th>
                                    <th class="px-3 py-4 text-left font-bold">NIS</th>
                                    <th class="px-3 py-4 text-left font-bold">Nama</th>
                                    <th class="px-3 py-4 text-left font-bold">Kelas</th>
                                    <th class="px-3 py-4 text-left font-bold">Face ID</th>
                                    <th class="px-3 py-4 text-center font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($students as $studentOption)
                                    @php
                                        $count = $studentOption->descriptors_count ?? 0;
                                        $isReady = $count >= 3;
                                        $isPartial = $count > 0 && ! $isReady;
                                        $initial = strtoupper(substr($studentOption->user?->name ?? 'S', 0, 1));
                                        $meta = ($studentOption->class?->name ?? '-') . ' - NIS ' . ($studentOption->nis ?? '-');
                                    @endphp

                                    <tr wire:key="face-row-{{ $studentOption->id }}" class="transition hover:bg-blue-50/60 dark:hover:bg-slate-800/45">
                                        <td class="px-3 py-4">
                                            <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                                @if($studentOption->photo)
                                                    <img src="{{ asset('storage/' . $studentOption->photo) }}"
                                                         alt="Foto {{ $studentOption->user?->name }}"
                                                         class="h-full w-full object-cover">
                                                @else
                                                    {{ $initial }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 font-bold text-slate-700 dark:text-slate-200">
                                            {{ $studentOption->nis }}
                                        </td>
                                        <td class="px-3 py-4">
                                            <div class="truncate font-extrabold text-slate-900 dark:text-white">
                                                {{ $studentOption->user?->name }}
                                            </div>
                                            <div class="mt-1 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                {{ $studentOption->gender }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-4">
                                            <span class="hk-badge max-w-full truncate bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                                {{ $studentOption->class?->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4">
                                            @if($isReady)
                                                <span class="font-bold text-emerald-600 dark:text-emerald-300">
                                                    Siap {{ $count }}/{{ $maxDescriptors }}
                                                </span>
                                            @elseif($isPartial)
                                                <span class="font-bold text-amber-600 dark:text-amber-300">
                                                    Kurang {{ $count }}/3
                                                </span>
                                            @else
                                                <span class="font-bold text-rose-600 dark:text-rose-300">
                                                    Face ID 0
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4">
                                            <div class="mx-auto flex w-20 items-center justify-center gap-1">
                                                <button
                                                    type="button"
                                                    x-on:click="openFaceRegistrationModal(@js($studentOption->id), @js($studentOption->user?->name), @js($meta), @js($count), @js($maxDescriptors))"
                                                    class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                                    title="Registrasi wajah"
                                                    aria-label="Registrasi wajah"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                                    </svg>
                                                </button>

                                                <button
                                                    type="button"
                                                    x-on:click="
                                                        confirmAction({
                                                            title: 'Reset descriptor siswa?',
                                                            text: 'Semua data Face ID siswa ini akan dihapus.',
                                                            confirmText: 'Reset',
                                                            icon: 'warning',
                                                            tone: 'danger'
                                                        }).then(confirmed => {
                                                            if (confirmed) $wire.resetStudentDescriptors({{ $studentOption->id }});
                                                        });
                                                    "
                                                    class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                                    title="Reset descriptor"
                                                    aria-label="Reset descriptor"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 15.5-6.2M21 5v6h-6M21 12a9 9 0 0 1-15.5 6.2M3 19v-6h6" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center">
                                            <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                                Belum ada data siswa.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>

                    <div>
                        {{ $students->links() }}
                    </div>

                    <template x-teleport="body">
                        <div
                            x-show="faceModalOpen"
                            x-transition.opacity
                            x-cloak
                            class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                        >
                            <section class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                            Registrasi Wajah
                                        </div>
                                        <h2 class="mt-1 truncate text-2xl font-extrabold text-slate-900 dark:text-white" x-text="modalStudentName"></h2>
                                        <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400" x-text="modalStudentMeta"></p>
                                    </div>

                                    <button
                                        type="button"
                                        x-on:click="closeFaceRegistrationModal()"
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                                        aria-label="Tutup registrasi wajah"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="space-y-4 p-5">
                                    <input id="selectedStudentId" type="hidden" x-bind:value="modalStudentId">

                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-950/30">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="text-sm font-extrabold text-slate-800 dark:text-slate-100">
                                                Data Wajah Tersimpan
                                            </div>

                                            <div id="faceModalCount" class="text-sm font-extrabold text-blue-600 dark:text-blue-300" x-text="`${modalDescriptorCount} / ${modalMaxDescriptors}`"></div>
                                        </div>

                                        <div x-show="modalDescriptorCount >= modalMaxDescriptors" x-cloak class="mt-3 flex items-start gap-2 rounded-2xl bg-amber-50 px-3 py-2 text-sm font-bold text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.9 2.6 18.2A2 2 0 0 0 4.3 21h15.4a2 2 0 0 0 1.7-2.8L13.7 4.9a2 2 0 0 0-3.4 0Z" />
                                            </svg>
                                            <span>Maksimum tercapai. Data baru akan mengganti data lama.</span>
                                        </div>
                                    </div>

                                    <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                        <div
                                            id="faceModalProgress"
                                            class="h-full rounded-full bg-gradient-to-r from-blue-600 to-emerald-500"
                                            x-bind:style="`width: ${Math.min(100, (modalDescriptorCount / Math.max(modalMaxDescriptors, 1)) * 100)}%`"
                                        ></div>
                                    </div>

                                    <div wire:ignore>
                                        <div class="flex items-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 p-1 dark:border-slate-800 dark:bg-slate-950/50">
                                            <button
                                                type="button"
                                                x-on:click="faceRegistrationMode = 'camera'; window.hadirkuFaceRegistrationMode = 'camera'"
                                                x-bind:class="faceRegistrationMode === 'camera' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-800 dark:text-blue-300' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white'"
                                                class="w-1/2 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-extrabold transition"
                                            >
                                                Kamera
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="faceRegistrationMode = 'upload'; window.hadirkuFaceRegistrationMode = 'upload'; window.hadirkuStopFaceRegistrationCamera?.({ silent: true })"
                                                x-bind:class="faceRegistrationMode === 'upload' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-800 dark:text-blue-300' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white'"
                                                class="w-1/2 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-extrabold transition"
                                            >
                                                Pilih Foto
                                            </button>
                                        </div>

                                        <div x-show="faceRegistrationMode === 'camera'" class="mt-4">
                                            <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-800 bg-slate-950 shadow-glow">
                                                <video
                                                    id="video"
                                                    autoplay
                                                    muted
                                                    playsinline
                                                    class="aspect-[3/4] max-h-[68vh] w-full bg-slate-950 object-cover sm:aspect-video sm:max-h-[52vh] sm:object-contain"
                                                ></video>

                                                <canvas
                                                    id="faceOverlay"
                                                    class="pointer-events-none absolute inset-0 h-full w-full"
                                                ></canvas>

                                                <div
                                                    id="faceCaptureFlash"
                                                    class="pointer-events-none absolute inset-0 hidden bg-white/25"
                                                ></div>

                                                <div id="cameraInactiveState" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 px-6 text-center text-white">
                                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white">
                                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                                                        </svg>
                                                    </div>
                                                    <div class="mt-4 text-lg font-extrabold">
                                                        Kamera belum aktif
                                                    </div>
                                                    <div class="mt-1 text-sm font-semibold text-slate-300">
                                                        Klik tombol di bawah untuk memulai
                                                    </div>
                                                </div>

                                                <div class="pointer-events-none absolute inset-4 rounded-[1.35rem] border border-white/20"></div>

                                                <div class="absolute left-4 top-4 rounded-2xl bg-slate-950/70 px-4 py-2 text-sm font-bold text-white backdrop-blur">
                                                    <span id="modelStatus">Menunggu model</span>
                                                </div>

                                                <button
                                                    type="button"
                                                    data-face-registration-camera-toggle
                                                    aria-label="Balik kamera"
                                                    title="Balik kamera"
                                                    class="absolute bottom-4 right-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-950/60 text-white shadow-lg ring-1 ring-white/20 backdrop-blur transition hover:bg-slate-950/80 disabled:cursor-not-allowed disabled:opacity-50 md:hidden"
                                                >
                                                    <svg
                                                        data-face-registration-camera-toggle-icon
                                                        class="h-7 w-7"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2.25"
                                                    >
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0 1 13.66-5.66" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.66 6.34H14m3.66 0V2.68" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12A8 8 0 0 1 6.34 17.66" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.34 17.66H10m-3.66 0v3.66" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div id="captureStatus" class="sr-only" aria-live="polite">
                                                Kamera belum aktif
                                            </div>

                                            <div class="mt-4">
                                                <div class="grid gap-3 sm:grid-cols-3">
                                                    <button
                                                        id="startCamera"
                                                        type="button"
                                                        class="hk-btn-primary"
                                                    >
                                                        <svg id="startCameraIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                                        </svg>

                                                        <svg id="stopCameraIcon" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h12v12H6V6Z" />
                                                        </svg>

                                                        <span id="cameraButtonText">
                                                            Aktifkan Kamera
                                                        </span>
                                                    </button>

                                                    <button
                                                        id="captureFace"
                                                        type="button"
                                                        class="hk-btn-success"
                                                    >
                                                        <span id="captureFaceSpinner" class="hidden h-5 w-5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                                        <svg id="captureFaceIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                                        </svg>
                                                        <span id="captureFaceText">Ambil Wajah</span>
                                                    </button>

                                                    <button
                                                        id="resetDescriptors"
                                                        type="button"
                                                        class="hk-btn-secondary"
                                                    >
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="faceRegistrationMode === 'upload'" x-cloak class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/80 p-5 text-center dark:border-slate-700 dark:bg-slate-950/30">
                                            <div class="mx-auto h-48 w-full max-w-sm overflow-hidden rounded-2xl bg-white dark:bg-slate-900">
                                                <img id="facePhotoPreview" alt="Pratinjau foto wajah" class="hidden h-full w-full object-cover">
                                            </div>

                                            <div id="facePhotoUploadPlaceholder" class="mx-auto -mt-48 flex h-48 w-full max-w-sm flex-col items-center justify-center rounded-2xl bg-white text-slate-500 dark:bg-slate-900 dark:text-slate-300">
                                                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 20h16" />
                                                </svg>
                                                <div class="mt-3 text-sm font-extrabold">
                                                    Pilih foto wajah
                                                </div>
                                                <div class="mt-1 text-xs font-semibold text-slate-400">
                                                    JPG, PNG, atau WEBP
                                                </div>
                                            </div>

                                            <label id="facePhotoUploadLabel" class="mt-4 inline-flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:text-blue-300">
                                                <span id="facePhotoUploadSpinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                                <svg id="facePhotoUploadIcon" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 20h16" />
                                                </svg>
                                                <span id="facePhotoUploadButtonText">Pilih Foto</span>
                                                <input id="facePhotoUpload" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                                            </label>

                                            <div class="mt-3 flex items-center justify-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400">
                                                <span id="facePhotoUploadStatusDot" class="h-2 w-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                                <span id="facePhotoUploadStatus">
                                                    Pilih foto yang jelas dan menghadap kamera.
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </template>

                @else

                <section class="grid gap-6 lg:grid-cols-[0.82fr_1.18fr]">

                    <aside class="space-y-4">
                        @if($canSelectStudent)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-950/30 sm:p-5">
                                <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                    Pilih Siswa
                                </div>

                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="selectedClass" class="hk-label">
                                            Kelas
                                        </label>

                                        <select
                                            id="selectedClass"
                                            wire:model.live="selectedClass"
                                            class="hk-input"
                                        >
                                            <option value="">
                                                Semua kelas
                                            </option>

                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="selectedStudentId" class="hk-label">
                                            Siswa
                                        </label>

                                        <select
                                            id="selectedStudentId"
                                            wire:model.live="selectedStudentId"
                                            class="hk-input"
                                        >
                                            <option value="">
                                                Pilih siswa
                                            </option>

                                            @foreach($students as $studentOption)
                                                <option value="{{ $studentOption->id }}">
                                                    {{ $studentOption->user?->name }} - {{ $studentOption->nis }} ({{ $studentOption->class?->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/20 sm:p-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 to-emerald-500 text-xl font-extrabold text-white shadow-glow">
                                    {{ $student?->user?->name ? strtoupper(substr($student->user->name, 0, 1)) : strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h2 class="truncate text-xl font-extrabold text-slate-900 dark:text-white">
                                        {{ $student?->user?->name ?? 'Belum ada siswa dipilih' }}
                                    </h2>

                                    <p class="mt-1 text-sm font-bold text-slate-500 dark:text-slate-400">
                                        {{ $student?->class?->name ?? 'Belum ada kelas' }} &middot; NIS {{ $student?->nis ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl bg-blue-50 px-4 py-3 text-sm font-extrabold text-blue-700 dark:bg-blue-500/10 dark:text-blue-200">
                                {{ $descriptorCount }}/{{ $maxDescriptors }} descriptor
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-950/30 sm:p-5">
                            <div class="flex items-center justify-between gap-3 text-sm font-bold">
                                <span class="text-slate-600 dark:text-slate-300">
                                    Progress descriptor
                                </span>

                                <span class="text-blue-600 dark:text-blue-300">
                                    Minimal 3 descriptor
                                </span>
                            </div>

                            <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-600 to-emerald-500"
                                     style="width: {{ min(100, ($descriptorCount / max($maxDescriptors, 1)) * 100) }}%"></div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                    Cahaya terang
                                </span>

                                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                    Wajah menghadap kamera
                                </span>

                                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                    Ambil beberapa sudut
                                </span>
                            </div>
                        </div>
                    </aside>

                    <div wire:ignore>
                        <div class="mb-3">
                            <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                Kamera
                            </div>
                            <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white">
                                Ambil Wajah
                            </h2>
                        </div>

                        <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-800 bg-slate-950 shadow-glow">
                            <video
                                id="video"
                                autoplay
                                muted
                                playsinline
                                class="aspect-[3/4] max-h-[68vh] w-full bg-slate-950 object-cover sm:aspect-video sm:max-h-none sm:object-contain"
                            ></video>

                            <canvas
                                id="faceOverlay"
                                class="pointer-events-none absolute inset-0 h-full w-full"
                            ></canvas>

                            <div
                                id="faceCaptureFlash"
                                class="pointer-events-none absolute inset-0 hidden bg-white/25"
                            ></div>

                            <div id="cameraInactiveState" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 px-6 text-center text-white">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                                    </svg>
                                </div>
                                <div class="mt-4 text-lg font-extrabold">
                                    Kamera belum aktif
                                </div>
                                <div class="mt-1 text-sm font-semibold text-slate-300">
                                    Klik tombol di bawah untuk memulai
                                </div>
                            </div>

                            <div class="pointer-events-none absolute inset-4 rounded-[1.35rem] border border-white/20"></div>

                            <div class="absolute left-4 top-4 rounded-2xl bg-slate-950/70 px-4 py-2 text-sm font-bold text-white backdrop-blur">
                                <span id="modelStatus">Menunggu model</span>
                            </div>

                            <button
                                type="button"
                                data-face-registration-camera-toggle
                                aria-label="Balik kamera"
                                title="Balik kamera"
                                class="absolute bottom-4 right-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-950/60 text-white shadow-lg ring-1 ring-white/20 backdrop-blur transition hover:bg-slate-950/80 disabled:cursor-not-allowed disabled:opacity-50 md:hidden"
                            >
                                <svg
                                    data-face-registration-camera-toggle-icon
                                    class="h-7 w-7"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.25"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0 1 13.66-5.66" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.66 6.34H14m3.66 0V2.68" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12A8 8 0 0 1 6.34 17.66" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.34 17.66H10m-3.66 0v3.66" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-5">
                            <div class="grid gap-3 sm:grid-cols-3">
                                <button
                                    id="startCamera"
                                    type="button"
                                    class="hk-btn-primary"
                                >
                                    <svg id="startCameraIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                    </svg>

                                    <svg id="stopCameraIcon" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h12v12H6V6Z" />
                                    </svg>

                                    <span id="cameraButtonText">
                                        Aktifkan Kamera
                                    </span>
                                </button>

                                <button
                                    id="captureFace"
                                    type="button"
                                    class="hk-btn-success"
                                >
                                    <span id="captureFaceSpinner" class="hidden h-5 w-5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                    <svg id="captureFaceIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                    </svg>
                                    <span id="captureFaceText">Ambil Wajah</span>
                                </button>

                                <button
                                    id="resetDescriptors"
                                    type="button"
                                    class="hk-btn-secondary"
                                >
                                    Reset Descriptor
                                </button>
                            </div>
                        </div>
                    </div>

                </section>

                @endif

            </div>

        </section>

    </div>

    <script>

        async function initFaceRegistrationPage() {

            const video = document.getElementById('video');

            const faceOverlay =
                document.getElementById('faceOverlay');

            const faceCaptureFlash =
                document.getElementById('faceCaptureFlash');

            const startCamera =
                document.getElementById('startCamera');

            const startCameraIcon =
                document.getElementById('startCameraIcon');

            const stopCameraIcon =
                document.getElementById('stopCameraIcon');

            const cameraButtonText =
                document.getElementById('cameraButtonText');

            const captureFace =
                document.getElementById('captureFace');

            const captureFaceSpinner =
                document.getElementById('captureFaceSpinner');

            const captureFaceIcon =
                document.getElementById('captureFaceIcon');

            const captureFaceText =
                document.getElementById('captureFaceText');

            const resetDescriptors =
                document.getElementById('resetDescriptors');

            const captureStatus =
                document.getElementById('captureStatus');

            const modelStatus =
                document.getElementById('modelStatus');

            const cameraInactiveState =
                document.getElementById('cameraInactiveState');

            const cameraToggleButton =
                document.querySelector('[data-face-registration-camera-toggle]');

            const cameraToggleIcon =
                document.querySelector('[data-face-registration-camera-toggle-icon]');

            const facePhotoUpload =
                document.getElementById('facePhotoUpload');

            const facePhotoPreview =
                document.getElementById('facePhotoPreview');

            const facePhotoUploadPlaceholder =
                document.getElementById('facePhotoUploadPlaceholder');

            const facePhotoUploadStatus =
                document.getElementById('facePhotoUploadStatus');

            const facePhotoUploadStatusDot =
                document.getElementById('facePhotoUploadStatusDot');

            const facePhotoUploadLabel =
                document.getElementById('facePhotoUploadLabel');

            const facePhotoUploadButtonText =
                document.getElementById('facePhotoUploadButtonText');

            const facePhotoUploadSpinner =
                document.getElementById('facePhotoUploadSpinner');

            const facePhotoUploadIcon =
                document.getElementById('facePhotoUploadIcon');

            const canSelectStudent =
                @js($canSelectStudent);

            if (!video || video.dataset.faceRegistrationReady === 'true') {
                return;
            }

            video.dataset.faceRegistrationReady = 'true';

            window.hadirkuFaceRegistrationModels ??= {
                loaded: false,
                loading: null,
                error: null,
            };

            const modelState = window.hadirkuFaceRegistrationModels;

            let facePreviewInterval = null;

            let lastFaceDetection = null;

            let lastFaceDetectedAt = 0;

            let captureProcessing = false;

            let switchingCamera = false;

            const cameraModeStorageKey =
                'hadirkuFaceRegistrationFacingMode';

            let preferredFacingMode =
                localStorage.getItem(cameraModeStorageKey) || 'user';

            if (!['user', 'environment'].includes(preferredFacingMode)) {
                preferredFacingMode = 'user';
            }

            if (modelState.loaded) {
                modelStatus.textContent = 'Model siap';
            } else if (modelState.loading) {
                modelStatus.textContent = 'Memuat model wajah';
            } else if (modelState.error) {
                modelStatus.textContent = 'Model gagal dimuat';
            }

            window.hadirkuUpdateFaceModalProgress = (count, maxDescriptors) => {
                const countElement = document.getElementById('faceModalCount');
                const progressElement = document.getElementById('faceModalProgress');
                const currentCount = Number(count || 0);
                const currentMax = Number(maxDescriptors || 10);

                if (countElement) {
                    countElement.textContent = `${currentCount} / ${currentMax}`;
                }

                if (progressElement) {
                    progressElement.style.width = `${Math.min(100, (currentCount / Math.max(currentMax, 1)) * 100)}%`;
                }
            };

            function setCaptureStatus(message) {
                if (captureStatus) {
                    captureStatus.textContent = message;
                }
            }

            function setUploadStatus(message, tone = 'slate') {
                if (facePhotoUploadStatus) {
                    facePhotoUploadStatus.textContent = message;
                }

                if (!facePhotoUploadStatusDot) {
                    return;
                }

                facePhotoUploadStatusDot.className = 'h-2 w-2 rounded-full';

                if (tone === 'blue') {
                    facePhotoUploadStatusDot.classList.add('bg-blue-500');
                    return;
                }

                if (tone === 'green') {
                    facePhotoUploadStatusDot.classList.add('bg-emerald-500');
                    return;
                }

                if (tone === 'red') {
                    facePhotoUploadStatusDot.classList.add('bg-rose-500');
                    return;
                }

                facePhotoUploadStatusDot.classList.add('bg-slate-300', 'dark:bg-slate-600');
            }

            function setUploadProcessing(processing, idleLabel = 'Pilih Foto') {
                if (facePhotoUpload) {
                    facePhotoUpload.disabled = processing;
                }

                if (facePhotoUploadLabel) {
                    facePhotoUploadLabel.classList.toggle('pointer-events-none', processing);
                    facePhotoUploadLabel.classList.toggle('opacity-70', processing);
                }

                if (facePhotoUploadButtonText) {
                    facePhotoUploadButtonText.textContent = processing
                        ? 'Memproses...'
                        : idleLabel;
                }

                facePhotoUploadSpinner?.classList.toggle('hidden', !processing);
                facePhotoUploadIcon?.classList.toggle('hidden', processing);
            }

            function setCaptureButtonState({
                processing = false,
                ready = false,
                text = null,
                force = false,
            } = {}) {
                if (captureProcessing && !processing && !force && text !== 'Tersimpan') {
                    return;
                }

                captureProcessing = processing;

                if (captureFace) {
                    captureFace.disabled = processing;
                    captureFace.classList.toggle('opacity-75', processing);
                    captureFace.classList.toggle('cursor-wait', processing);
                }

                captureFaceSpinner?.classList.toggle('hidden', !processing);
                captureFaceIcon?.classList.toggle('hidden', processing);

                if (captureFaceText) {
                    captureFaceText.textContent = text || (
                        processing
                            ? 'Menyimpan...'
                            : (ready ? 'Ambil Wajah Ini' : 'Ambil Wajah')
                    );
                }
            }

            function flashCapture() {
                if (!faceCaptureFlash) return;

                faceCaptureFlash.classList.remove('hidden');
                faceCaptureFlash.classList.add('opacity-100');

                setTimeout(() => {
                    faceCaptureFlash.classList.add('hidden');
                    faceCaptureFlash.classList.remove('opacity-100');
                }, 150);
            }

            function clearFaceOverlay() {
                if (!faceOverlay) return;

                const context = faceOverlay.getContext('2d');
                const boxX = isFrontCamera()
                    ? displaySize.width - box.x - box.width
                    : box.x;

                context.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
            }

            function drawFaceOverlay(detection, label = 'Wajah terdeteksi', color = '#38bdf8') {
                if (!faceOverlay || !detection || !video.videoWidth) return;

                const displaySize = {
                    width: video.clientWidth,
                    height: video.clientHeight,
                };

                faceapi.matchDimensions(faceOverlay, displaySize);

                const resizedDetection =
                    faceapi.resizeResults(detection, displaySize);

                const box = resizedDetection.box || resizedDetection.detection?.box;

                if (!box) return;

                const context = faceOverlay.getContext('2d');

                context.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
                context.lineWidth = 3;
                context.strokeStyle = color;
                context.shadowColor = 'rgba(15, 23, 42, .8)';
                context.shadowBlur = 8;
                context.strokeRect(boxX, box.y, box.width, box.height);
                context.shadowBlur = 0;

                const labelWidth = Math.max(context.measureText(label).width + 18, 112);
                const labelY = Math.max(box.y - 30, 8);

                context.fillStyle = color;
                context.fillRect(boxX, labelY, labelWidth, 24);
                context.fillStyle = '#ffffff';
                context.font = '700 12px sans-serif';
                context.fillText(label, boxX + 9, labelY + 16);
            }

            function stopFacePreviewLoop() {
                clearInterval(facePreviewInterval);
                facePreviewInterval = null;
                lastFaceDetection = null;
                lastFaceDetectedAt = 0;
                setCaptureButtonState({
                    force: true,
                });
                clearFaceOverlay();
            }

            function startFacePreviewLoop() {
                if (facePreviewInterval || !video.srcObject || !modelState.loaded) {
                    return;
                }

                facePreviewInterval = setInterval(async () => {
                    if (!video.srcObject || video.readyState < 2) {
                        clearFaceOverlay();
                        return;
                    }

                    try {
                        const detection =
                            await faceapi
                                .detectSingleFace(
                                    video,
                                    new faceapi.TinyFaceDetectorOptions({
                                        inputSize: 224,
                                        scoreThreshold: 0.5,
                                    })
                                )
                                .withFaceLandmarks()
                                .withFaceDescriptor();

                        if (!detection) {
                            lastFaceDetection = null;
                            lastFaceDetectedAt = 0;
                            clearFaceOverlay();
                            setCaptureButtonState();
                            setCaptureStatus('Mencari wajah');
                            return;
                        }

                        lastFaceDetection = detection;
                        lastFaceDetectedAt = Date.now();

                        drawFaceOverlay(detection);
                        setCaptureButtonState({
                            ready: true,
                        });
                        setCaptureStatus('Wajah terdeteksi');
                    } catch (error) {
                        lastFaceDetection = null;
                        lastFaceDetectedAt = 0;
                        setCaptureButtonState();
                        clearFaceOverlay();
                    }
                }, 650);
            }

            async function loadModels() {

                if (modelState.loaded) {
                    modelStatus.textContent = 'Model siap';
                    return;
                }

                if (modelState.loading) {
                    modelStatus.textContent = 'Memuat model wajah';
                    await modelState.loading;
                    modelStatus.textContent = 'Model siap';
                    return;
                }

                modelState.error = null;

                modelState.loading = (async () => {

                    modelStatus.textContent = 'Memuat FaceAPI';

                    await window.loadHadirkuFaceApi();

                    modelStatus.textContent = 'Memuat model wajah';

                    await faceapi.nets.tinyFaceDetector.loadFromUri('/models');

                    await faceapi.nets.faceLandmark68Net.loadFromUri('/models');

                    await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

                    modelState.loaded = true;

                    modelStatus.textContent = 'Model siap';

                })();

                try {
                    await modelState.loading;
                } catch (error) {
                    modelState.error = error;
                    throw error;
                } finally {
                    modelState.loading = null;
                }

            }

            function readableModelError(error) {

                if (!window.loadHadirkuFaceApi) {
                    return 'Pemuat FaceAPI belum tersedia. Muat ulang halaman.';
                }

                const message = error?.message || '';
                const lowerMessage = message.toLowerCase();

                if (lowerMessage.includes('face-api')) {
                    return 'FaceAPI gagal dimuat. Periksa koneksi internet lalu muat ulang halaman.';
                }

                if (message.includes('404') || lowerMessage.includes('not found')) {
                    return 'Model wajah tidak ditemukan. Pastikan folder public/models berisi model FaceAPI.';
                }

                return 'Model wajah gagal dimuat. Pastikan folder public/models tersedia lalu muat ulang halaman.';

            }

            function readableCameraError(error) {

                if (!window.isSecureContext && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
                    return 'Browser hanya mengizinkan kamera pada HTTPS. Aktifkan HTTPS di domain ini.';
                }

                if (error?.name === 'NotAllowedError') {
                    return 'Izin kamera ditolak. Izinkan akses kamera dari pengaturan browser.';
                }

                if (error?.name === 'NotFoundError') {
                    return 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera aktif.';
                }

                if (error?.name === 'NotReadableError') {
                    return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi lain lalu coba lagi.';
                }

                return error?.message || 'Kamera gagal diaktifkan.';

            }

            function getOppositeFacingMode() {
                return preferredFacingMode === 'environment'
                    ? 'user'
                    : 'environment';
            }

            function getCameraTargetLabel(mode) {
                return mode === 'environment'
                    ? 'kamera belakang'
                    : 'kamera depan';
            }

            function updateCameraToggle(disabled = false) {
                if (!cameraToggleButton) return;

                const targetLabel =
                    getCameraTargetLabel(getOppositeFacingMode());

                cameraToggleButton.disabled =
                    disabled || !video.srcObject;

                cameraToggleButton.setAttribute('aria-label', `Ganti ke ${targetLabel}`);
                cameraToggleButton.title = `Ganti ke ${targetLabel}`;
                cameraToggleIcon?.classList.toggle('animate-spin', switchingCamera);
            }

            function setPreferredFacingMode(mode) {
                preferredFacingMode = mode === 'environment'
                    ? 'environment'
                    : 'user';

                localStorage.setItem(cameraModeStorageKey, preferredFacingMode);
                updateVideoMirror();
                updateCameraToggle(switchingCamera);
            }

            function isFrontCamera() {
                return preferredFacingMode === 'user';
            }

            function updateVideoMirror() {
                video.classList.toggle('-scale-x-100', isFrontCamera());
            }

            function getCameraConstraints(facingMode, strictFacingMode = false) {
                const forcePortrait =
                    facingMode === 'environment'
                    || window.matchMedia('(max-width: 767px)').matches;

                return {
                    width: {
                        ideal: forcePortrait ? 720 : 640,
                    },
                    height: {
                        ideal: forcePortrait ? 1280 : 480,
                    },
                    aspectRatio: {
                        ideal: forcePortrait ? 9 / 16 : 4 / 3,
                    },
                    resizeMode: 'crop-and-scale',
                    facingMode: strictFacingMode
                        ? { exact: facingMode }
                        : { ideal: facingMode },
                };
            }

            function stopStreamTracks(stream) {
                stream?.getTracks().forEach(track => {
                    track.stop();
                });
            }

            function cameraTimeout(message = 'Kamera terlalu lama merespons. Coba ulangi.') {
                return new Promise((_, reject) => {
                    setTimeout(() => reject(new Error(message)), 8000);
                });
            }

            function wait(ms) {
                return new Promise(resolve => {
                    setTimeout(resolve, ms);
                });
            }

            async function requestCameraStream(facingMode, strictFacingMode) {
                let timedOut = false;

                const mediaPromise =
                    navigator.mediaDevices
                        .getUserMedia({
                            video:
                                getCameraConstraints(facingMode, strictFacingMode),
                            audio: false,
                        })
                        .then(stream => {
                            if (timedOut) {
                                stopStreamTracks(stream);
                                throw new Error('Kamera terlalu lama merespons. Coba ulangi.');
                            }

                            return stream;
                        });

                const timeoutPromise =
                    new Promise((_, reject) => {
                        setTimeout(() => {
                            timedOut = true;
                            reject(new Error('Kamera terlalu lama merespons. Coba ulangi.'));
                        }, 8000);
                    });

                return Promise.race([
                    mediaPromise,
                    timeoutPromise,
                ]);
            }

            async function attachCameraStream(stream) {
                video.srcObject = stream;
                updateVideoMirror();

                try {
                    await Promise.race([
                        video.play(),
                        cameraTimeout('Kamera aktif, tetapi video tidak tampil. Coba ulangi.'),
                    ]);
                } catch (error) {
                    video.srcObject = null;
                    stopStreamTracks(stream);
                    throw error;
                }
            }

            async function getCameraStream(options = {}) {
                const facingMode =
                    preferredFacingMode === 'environment' ? 'environment' : 'user';

                const strictFacingMode =
                    Boolean(options.strictFacingMode);

                try {
                    return await requestCameraStream(facingMode, strictFacingMode);
                } catch (error) {
                    if (
                        facingMode === 'environment'
                        && ['NotFoundError', 'OverconstrainedError', 'ConstraintNotSatisfiedError'].includes(error?.name)
                    ) {
                        setPreferredFacingMode('user');
                        throw new Error('Kamera belakang tidak tersedia. Pilih kamera depan lalu coba lagi.');
                    }

                    throw error;
                }
            }

            function setCameraButtonActive(active) {

                startCamera.classList.toggle('hk-btn-primary', !active);
                startCamera.classList.toggle('hk-btn-danger', active);

                startCameraIcon.classList.toggle('hidden', active);
                stopCameraIcon.classList.toggle('hidden', !active);

                if (cameraInactiveState) {
                    cameraInactiveState.classList.toggle('hidden', active);
                }

                cameraButtonText.textContent = active
                    ? 'Matikan Kamera'
                    : 'Aktifkan Kamera';

                updateCameraToggle(!active);

            }

            function stopCameraStream(options = {}) {

                const silent = Boolean(options.silent);

                const stream = video.srcObject;

                if (!stream) {
                    stopFacePreviewLoop();
                    setCameraButtonActive(false);
                    clearFaceOverlay();
                    return;
                }

                stopStreamTracks(stream);

                video.srcObject = null;

                setCaptureStatus('Kamera dihentikan');

                setCameraButtonActive(false);
                stopFacePreviewLoop();

                if (!silent) {
                    showToast('success', 'Kamera dihentikan.');
                }

            }

            window.hadirkuStopFaceRegistrationCamera = stopCameraStream;

            updateVideoMirror();
            updateCameraToggle(!video.srcObject);

            async function toggleCameraFacingMode() {
                if (switchingCamera || !video.srcObject) {
                    return;
                }

                const previousFacingMode =
                    preferredFacingMode;

                const nextFacingMode =
                    getOppositeFacingMode();

                switchingCamera = true;
                setPreferredFacingMode(nextFacingMode);
                updateCameraToggle(true);
                setCaptureStatus('Mengganti kamera');

                stopFacePreviewLoop();

                const currentStream =
                    video.srcObject;

                if (currentStream) {
                    stopStreamTracks(currentStream);
                }

                video.srcObject = null;
                await wait(300);

                try {
                    const stream =
                        await getCameraStream({
                            strictFacingMode: true,
                        });

                    await attachCameraStream(stream);
                    await loadModels();

                    setCameraButtonActive(true);
                    startFacePreviewLoop();
                    setCaptureStatus('Kamera aktif - mencari wajah');
                } catch (error) {
                    setPreferredFacingMode(previousFacingMode);

                    try {
                        const stream =
                            await getCameraStream({
                                strictFacingMode: true,
                            });

                        await attachCameraStream(stream);
                        await loadModels();

                        setCameraButtonActive(true);
                        startFacePreviewLoop();
                        setCaptureStatus('Kamera aktif - mencari wajah');
                    } catch (restoreError) {
                        setCameraButtonActive(false);
                        setCaptureStatus('Kamera gagal aktif');
                    }

                    showToast(
                        'error',
                        error?.message || 'Kamera gagal diganti.'
                    );
                } finally {
                    switchingCamera = false;
                    updateCameraToggle(!video.srcObject);
                }
            }

            cameraToggleButton?.addEventListener('click', toggleCameraFacingMode);

            loadModels().catch(error => {

                modelStatus.textContent = 'Model gagal dimuat';

                showToast(
                    'error',
                    readableModelError(error)
                );

            });

            if (!window.hadirkuFaceRegistrationSavedListener) {

                window.hadirkuFaceRegistrationSavedListener = true;

                window.addEventListener('descriptor-saved', event => {

                    const status =
                        document.getElementById('captureStatus');

                    if (status) {
                        status.textContent = 'Descriptor tersimpan';
                    }

                    const detail = Array.isArray(event.detail)
                        ? event.detail[0]
                        : event.detail;

                    if (detail?.count !== undefined) {
                        window.hadirkuSetFaceModalCount?.(detail.count);
                    }

                    showToast('success', 'Descriptor wajah tersimpan.');

                });

                window.addEventListener('descriptor-reset', event => {
                    const detail = Array.isArray(event.detail)
                        ? event.detail[0]
                        : event.detail;

                    window.hadirkuSetFaceModalCount?.(detail?.count || 0);
                });

            }

            async function saveDescriptorFromDetection(detection) {

                const selectedStudent =
                    document.getElementById('selectedStudentId');

                if (selectedStudent && !selectedStudent.value) {
                    showToast('error', 'Pilih siswa terlebih dahulu.');
                    return false;
                }

                const descriptor =
                    Array.from(detection.descriptor);

                if (selectedStudent?.value) {
                    await @this.call('saveDescriptorForStudent', selectedStudent.value, descriptor);
                } else {
                    await @this.call('saveDescriptor', descriptor);
                }

                return true;

            }

            function createLightweightDetectionImage(image) {

                const maxSide = 640;
                const longestSide = Math.max(image.naturalWidth, image.naturalHeight);

                if (!longestSide || longestSide <= maxSide) {
                    return image;
                }

                const scale = maxSide / longestSide;
                const canvas = document.createElement('canvas');

                canvas.width = Math.round(image.naturalWidth * scale);
                canvas.height = Math.round(image.naturalHeight * scale);

                const context = canvas.getContext('2d', {
                    alpha: false,
                });

                context.drawImage(
                    image,
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                return canvas;

            }

            startCamera.addEventListener('click', async () => {

                try {

                    if (video.srcObject) {
                        stopCameraStream();
                        return;
                    }

                    startCamera.disabled = true;
                    updateCameraToggle(true);
                    cameraButtonText.textContent = 'Mengaktifkan...';

                    setCaptureStatus('Meminta akses kamera');

                    if (!window.isSecureContext && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
                        throw new Error('Kamera membutuhkan HTTPS.');
                    }

                    if (!navigator.mediaDevices?.getUserMedia) {
                        throw new Error('Browser tidak mendukung akses kamera.');
                    }

                    const stream =
                        await getCameraStream();

                    await attachCameraStream(stream);

                    setCaptureStatus('Menyiapkan kotak deteksi');

                    await loadModels();

                    startFacePreviewLoop();

                    setCaptureStatus('Kamera aktif - mencari wajah');

                    setCameraButtonActive(true);

                    showToast('success', 'Kamera aktif.');

                } catch (error) {

                    setCaptureStatus('Kamera gagal aktif');

                    showToast(
                        'error',
                        readableCameraError(error)
                    );

                } finally {

                    startCamera.disabled = false;

                    if (!video.srcObject) {
                        setCameraButtonActive(false);
                    }

                }

            });

            captureFace.addEventListener('click', async () => {

                if (captureProcessing) {
                    return;
                }

                const selectedStudent =
                    document.getElementById('selectedStudentId');

                if (selectedStudent && !selectedStudent.value) {
                    setCaptureStatus('Pilih siswa terlebih dahulu');
                    showToast('error', 'Pilih siswa terlebih dahulu.');
                    return;
                }

                try {

                    if (!video.srcObject) {
                        setCaptureStatus('Aktifkan kamera dahulu');
                        showToast('error', 'Aktifkan kamera terlebih dahulu.');
                        return;
                    }

                    setCaptureButtonState({
                        processing: true,
                        text: 'Menyiapkan...',
                    });

                    flashCapture();

                    setCaptureStatus('Menyiapkan Face ID');

                    await loadModels();

                    const cachedDetectionIsFresh =
                        lastFaceDetection && (Date.now() - lastFaceDetectedAt) < 1500;

                    let detection = cachedDetectionIsFresh
                        ? lastFaceDetection
                        : null;

                    if (!detection) {
                        setCaptureStatus('Mendeteksi wajah');
                        setCaptureButtonState({
                            processing: true,
                            text: 'Mendeteksi...',
                        });

                        detection =
                            await faceapi
                                .detectSingleFace(
                                    video,
                                    new faceapi.TinyFaceDetectorOptions({
                                        inputSize: 320,
                                        scoreThreshold: 0.5,
                                    })
                                )
                                .withFaceLandmarks()
                                .withFaceDescriptor();
                    }

                    if (!detection) {
                        lastFaceDetection = null;
                        lastFaceDetectedAt = 0;
                        clearFaceOverlay();
                        setCaptureStatus('Wajah tidak terdeteksi');
                        setCaptureButtonState({
                            force: true,
                        });
                        showToast('error', 'Wajah tidak terdeteksi.');
                        return;
                    }

                    drawFaceOverlay(detection, 'Wajah siap', '#34d399');

                    setCaptureStatus('Menyimpan Face ID...');
                    setCaptureButtonState({
                        processing: true,
                        text: 'Menyimpan...',
                    });

                    const saved =
                        await saveDescriptorFromDetection(detection);

                    if (!saved) {
                        setCaptureButtonState({
                            ready: true,
                            force: true,
                        });

                        return;
                    }

                    setCaptureStatus('Descriptor tersimpan');
                    setCaptureButtonState({
                        ready: true,
                        text: 'Tersimpan',
                    });

                    setTimeout(() => {
                        setCaptureButtonState({
                            ready: Boolean(video.srcObject && lastFaceDetection),
                            force: true,
                        });
                    }, 1000);

                } catch (error) {

                    setCaptureStatus('Registrasi gagal');
                    setCaptureButtonState({
                        ready: Boolean(video.srcObject && lastFaceDetection),
                        force: true,
                    });

                    showToast(
                        'error',
                        modelState.loaded
                            ? (error?.message || 'Registrasi wajah gagal diproses.')
                            : readableModelError(error)
                    );

                }

            });

            if (facePhotoUpload) {
                facePhotoUpload.addEventListener('change', async event => {
                    window.hadirkuFaceRegistrationMode = 'upload';

                    const file = event.target.files?.[0];

                    if (!file) {
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        setUploadStatus('Berkas harus berupa gambar.', 'red');
                        showToast('error', 'Berkas harus berupa gambar.');
                        return;
                    }

                    const imageUrl = URL.createObjectURL(file);

                    setUploadProcessing(true);
                    setUploadStatus('Foto dipilih. Menyiapkan pratinjau...', 'blue');

                    if (facePhotoPreview) {
                        facePhotoPreview.src = imageUrl;
                        facePhotoPreview.classList.remove('hidden');
                    }

                    if (facePhotoUploadPlaceholder) {
                        facePhotoUploadPlaceholder.classList.add('hidden');
                    }

                    try {

                        setUploadStatus('Memuat foto wajah...', 'blue');

                        const image = new Image();
                        image.src = imageUrl;

                        await new Promise((resolve, reject) => {
                            image.onload = resolve;
                            image.onerror = reject;
                        });

                        const detectionImage =
                            createLightweightDetectionImage(image);

                        setUploadStatus('Menyiapkan model wajah...', 'blue');

                        await loadModels();

                        setUploadStatus('Mendeteksi wajah pada foto...', 'blue');

                        const detection =
                            await faceapi
                                .detectSingleFace(
                                    detectionImage,
                                    new faceapi.TinyFaceDetectorOptions({
                                        inputSize: 224,
                                        scoreThreshold: 0.5,
                                    })
                                )
                                .withFaceLandmarks()
                                .withFaceDescriptor();

                        if (!detection) {
                            setUploadStatus('Wajah tidak terdeteksi pada foto.', 'red');
                            showToast('error', 'Wajah tidak terdeteksi pada foto.');
                            return;
                        }

                        setUploadStatus('Wajah terdeteksi. Menyimpan Face ID...', 'green');

                        await saveDescriptorFromDetection(detection);

                        setUploadStatus('Face ID dari foto tersimpan.', 'green');

                    } catch (error) {

                        setUploadStatus('Foto gagal diproses.', 'red');

                        showToast(
                            'error',
                            modelState.loaded
                                ? (error?.message || 'Foto gagal diproses.')
                                : readableModelError(error)
                        );

                    } finally {

                        URL.revokeObjectURL(imageUrl);
                        facePhotoUpload.value = '';
                        setUploadProcessing(false, 'Pilih Foto Lain');

                    }

                });
            }

            resetDescriptors.addEventListener('click', () => {
                if (!canSelectStudent) {
                    confirmAction({
                        title: 'Reset descriptor wajah?',
                        text: 'Semua data Face ID yang sudah tersimpan akan dihapus.',
                        confirmText: 'Reset',
                        icon: 'warning',
                        tone: 'danger',
                    }).then(confirmed => {
                        if (confirmed) {
                            @this.call('resetDescriptors');
                        }
                    });

                    return;
                }

                window.hadirkuStopFaceRegistrationCamera?.({ silent: true });
                window.dispatchEvent(new CustomEvent('hadirku-close-face-modal'));

            });

        }

        document.addEventListener('DOMContentLoaded', initFaceRegistrationPage);
        document.addEventListener('livewire:navigated', initFaceRegistrationPage);
        window.hadirkuInitFaceRegistrationPage = initFaceRegistrationPage;
        initFaceRegistrationPage();

    </script>

</div>

