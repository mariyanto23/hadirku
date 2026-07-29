<div
    x-data="{
        formOpen: @entangle('showFormModal'),
        filterOpen: @entangle('showMobileFilterModal'),
        detailOpen: @entangle('showAttendanceDetailModal'),
        attachmentPreview: {
            open: false,
            url: '',
            name: ''
        },
        previewAttachment(url, name, isImage) {
            if (!url) {
                return;
            }

            if (!isImage) {
                window.open(url, '_blank', 'noopener');
                return;
            }

            this.attachmentPreview = {
                open: true,
                url,
                name
            };
        },
        closeAttachmentPreview() {
            this.attachmentPreview.open = false;
            this.attachmentPreview.url = '';
            this.attachmentPreview.name = '';
        }
    }"
    x-on:keydown.escape.window="if (attachmentPreview.open) closeAttachmentPreview(); else if (formOpen) $wire.closeFormModal(); else if (filterOpen) $wire.closeMobileFilters(); else if (detailOpen) $wire.closeAttendanceDetail()"
>
    <div class="hk-page max-w-6xl">
        <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                            Total Filter
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $summary['total'] }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300 sm:text-sm">
                            Menunggu
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $summary['pending'] }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300 sm:text-sm">
                            Disetujui
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $summary['approved'] }}
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
                        <div class="text-[10px] font-bold uppercase tracking-wide text-rose-600 dark:text-rose-300 sm:text-sm">
                            Ditolak
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $summary['rejected'] }}
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

        <section class="hk-card overflow-hidden">
            <div class="border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:px-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Izin/Sakit
                        </div>
                        <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:text-3xl">
                            Pengajuan dan Presensi Manual
                        </h1>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            wire:click="openCreateModal"
                            class="hk-btn-primary"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                            </svg>
                            Input Izin/Sakit
                        </button>
                    </div>
                </div>

                <div class="mt-5 hidden flex-col gap-3 md:flex xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            Filter persetujuan:
                            {{ $approvalFilter === '' ? 'Semua' : ($approvalFilter === 'pending' ? 'Menunggu' : ($approvalFilter === 'approved' ? 'Disetujui' : 'Ditolak')) }}
                        </span>

                        @if($search)
                            <span class="rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                Cari: {{ $search }}
                            </span>
                        @endif

                        @if($dateFilter)
                            <span class="rounded-full bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                Tanggal: {{ $dateFilter }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="viewAllApprovals"
                            class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm transition hover:border-blue-200 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:text-blue-300"
                        >
                            Lihat Semua
                        </button>

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm transition hover:border-rose-200 hover:text-rose-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:text-rose-300"
                        >
                            Atur Ulang
                        </button>
                    </div>
                </div>

                <div class="mt-5 flex gap-2 md:hidden">
                    <label class="relative min-w-0 flex-1">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Cari nama atau NIS..."
                            class="hk-input pl-12"
                        >
                    </label>

                    <button
                        type="button"
                        wire:click="openMobileFilters"
                        class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-blue-500/40 dark:hover:bg-blue-500/10 dark:hover:text-blue-300"
                        aria-label="Buka filter"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4" />
                        </svg>

                        @if($activeFilterCount > 0)
                            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1 text-[10px] font-extrabold text-white shadow-sm">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>
                </div>

                <div class="mt-5 hidden gap-3 md:grid md:grid-cols-2 xl:grid-cols-4">
                    <label class="relative">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Cari nama atau NIS..."
                            class="hk-input pl-12"
                        >
                    </label>

                    <select wire:model.live="classFilter" class="hk-input">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="statusFilter" class="hk-input">
                        <option value="">Semua Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpha">Alpa</option>
                    </select>

                    <select wire:model.live="approvalFilter" class="hk-input">
                        <option value="">Semua Persetujuan</option>
                        <option value="pending">Menunggu</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>

                    <input type="date" wire:model.live="dateFilter" class="hk-input">
                </div>
            </div>

            <div class="space-y-3 p-4 md:hidden">
                @forelse($attendances as $attendance)
                    @php
                        $source = 'Manual';

                        if ($attendance->requested_by_user_id) {
                            $source = 'Pengajuan';
                        } elseif ($attendance->confidence_score !== null) {
                            $source = 'Face Scan';
                        } elseif (str_contains((string) $attendance->notes, 'Alpha otomatis') || str_contains((string) $attendance->notes, 'Alpa otomatis')) {
                            $source = 'Alpa Otomatis';
                        }
                    @endphp

                    <article
                        wire:key="manual-attendance-mobile-{{ $attendance->id }}"
                        class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/35 {{ $attendance->approval_status === 'pending' ? 'border-amber-200 bg-amber-50/60 dark:border-amber-500/25 dark:bg-amber-500/10' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                @if($attendance->student?->photo)
                                    <img src="{{ asset('storage/' . $attendance->student->photo) }}"
                                         alt="Foto {{ $attendance->student->user?->name }}"
                                         class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($attendance->student->user?->name ?? 'S', 0, 1)) }}
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                    {{ $attendance->student->user?->name }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    NIS {{ $attendance->student?->nis }} &middot; {{ $attendance->student->class?->name }}
                                </div>
                            </div>

                            <span class="hk-badge shrink-0
                                @if($attendance->status === 'hadir')
                                    bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                                @elseif($attendance->status === 'terlambat')
                                    bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                                @elseif($attendance->status === 'izin')
                                    bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                                @elseif($attendance->status === 'sakit')
                                    bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300
                                @else
                                    bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                                @endif
                            ">
                                {{ $attendance->status === 'alpha' ? 'ALPA' : strtoupper($attendance->status) }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    Tanggal
                                </div>
                                <div class="mt-1 text-sm font-extrabold text-slate-800 dark:text-slate-100">
                                    {{ $attendance->attendance_date->translatedFormat('d F Y') }}
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    Jam
                                </div>
                                <div class="mt-1 text-sm font-extrabold text-slate-800 dark:text-slate-100">
                                    {{ substr((string) $attendance->attendance_time, 0, 5) }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="hk-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                {{ $source }}
                            </span>

                            <span class="hk-badge
                                @if($attendance->approval_status === 'approved')
                                    bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                                @elseif($attendance->approval_status === 'rejected')
                                    bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                                @else
                                    bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                                @endif
                            ">
                                @if($attendance->approval_status === 'approved')
                                    Disetujui
                                @elseif($attendance->approval_status === 'rejected')
                                    Ditolak
                                @else
                                    Menunggu
                                @endif
                            </span>

                            @if($attendance->attachment_path)
                                <button
                                    type="button"
                                    x-on:click="previewAttachment(@js($attendance->attachmentUrl()), @js($attendance->attachmentDisplayName()), @js($attendance->attachmentIsImage()))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-700 transition hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30"
                                    title="Lihat lampiran"
                                    aria-label="Lihat lampiran"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <div class="mt-3 rounded-2xl border border-slate-100 bg-white/70 p-3 dark:border-slate-800 dark:bg-slate-900/45">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                Catatan
                            </div>
                            <div class="mt-1 break-words text-sm font-semibold leading-5 text-slate-600 dark:text-slate-300">
                                {{ $attendance->notes ?: '-' }}
                            </div>

                            @if($attendance->review_notes)
                                <div class="mt-2 break-words text-xs font-semibold leading-5 text-rose-600 dark:text-rose-300">
                                    Tinjauan: {{ $attendance->review_notes }}
                                </div>
                            @endif

                            @if($attendance->attachment_path)
                                <button
                                    type="button"
                                    x-on:click="previewAttachment(@js($attendance->attachmentUrl()), @js($attendance->attachmentDisplayName()), @js($attendance->attachmentIsImage()))"
                                    class="mt-2 inline-flex items-center gap-1 text-xs font-extrabold text-blue-600 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200"
                                >
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                    </svg>
                                    Lihat Lampiran
                                </button>
                            @endif
                        </div>

                        @if($attendance->requestedBy || $attendance->reviewedBy)
                            <div class="mt-3 space-y-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                @if($attendance->requestedBy)
                                    <div>oleh {{ $attendance->requestedBy->name }}</div>
                                @endif

                                @if($attendance->reviewedBy)
                                    <div>ditinjau {{ $attendance->reviewedBy->name }}</div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                wire:click="openAttendanceDetail({{ $attendance->id }})"
                                wire:loading.attr="disabled"
                                wire:target="openAttendanceDetail({{ $attendance->id }})"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl bg-slate-100 px-3 py-2 text-xs font-extrabold text-slate-700 transition hover:bg-slate-200 disabled:opacity-50 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            >
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5v14" />
                                </svg>
                                Detail
                            </button>

                            @if($attendance->approval_status === 'pending')
                                <button
                                    type="button"
                                    wire:loading.attr="disabled"
                                    wire:target="approve({{ $attendance->id }})"
                                    x-on:click="
                                        confirmAction({
                                            title: 'Setujui pengajuan?',
                                            text: 'Pengajuan ini akan masuk sebagai presensi resmi.',
                                            confirmText: 'Setujui',
                                            icon: 'question',
                                            tone: 'success'
                                        }).then(confirmed => {
                                            if (confirmed) $wire.approve({{ $attendance->id }});
                                        });
                                    "
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl bg-emerald-100 px-3 py-2 text-xs font-extrabold text-emerald-700 transition hover:bg-emerald-200 disabled:opacity-50 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30"
                                >
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                    </svg>
                                    Setujui
                                </button>

                                <button
                                    type="button"
                                    x-on:click="
                                        Swal.fire({
                                            title: 'Tolak pengajuan?',
                                            text: 'Pengajuan akan ditolak dan presensi ditandai alpa.',
                                            input: 'textarea',
                                            inputPlaceholder: 'Alasan penolakan (opsional)',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'Tolak',
                                            cancelButtonText: 'Batal',
                                            reverseButtons: true,
                                            confirmButtonColor: '#e11d48'
                                        }).then(result => {
                                            if (result.isConfirmed) $wire.reject({{ $attendance->id }}, result.value || '');
                                        });
                                    "
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl bg-rose-100 px-3 py-2 text-xs font-extrabold text-rose-700 transition hover:bg-rose-200 disabled:opacity-50 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30"
                                >
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </button>
                            @endif

                            <button
                                type="button"
                                wire:click="edit({{ $attendance->id }})"
                                wire:loading.attr="disabled"
                                wire:target="edit({{ $attendance->id }})"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl bg-amber-100 px-3 py-2 text-xs font-extrabold text-amber-700 transition hover:bg-amber-200 disabled:opacity-50 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                            >
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                </svg>
                                Edit
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="px-4 py-10 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                            </svg>
                        </div>
                        <div class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-300">
                            Belum ada data presensi untuk filter ini.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-slate-50/90 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">
                        <tr>
                            <th class="px-5 py-4 text-left font-bold">Siswa</th>
                            <th class="px-5 py-4 text-left font-bold">Waktu</th>
                            <th class="px-5 py-4 text-left font-bold">Status</th>
                            <th class="px-5 py-4 text-left font-bold">Asal</th>
                            <th class="px-5 py-4 text-left font-bold">Catatan</th>
                            <th class="w-40 px-5 py-4 text-center font-bold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($attendances as $attendance)
                            @php
                                $source = 'Manual';

                                if ($attendance->requested_by_user_id) {
                                    $source = 'Pengajuan';
                                } elseif ($attendance->confidence_score !== null) {
                                    $source = 'Face Scan';
                                } elseif (str_contains((string) $attendance->notes, 'Alpha otomatis') || str_contains((string) $attendance->notes, 'Alpa otomatis')) {
                                    $source = 'Alpa Otomatis';
                                }
                            @endphp

                            <tr
                                wire:key="manual-attendance-{{ $attendance->id }}"
                                class="transition hover:bg-blue-50/60 dark:hover:bg-slate-800/45 {{ $attendance->approval_status === 'pending' ? 'bg-amber-50/55 dark:bg-amber-500/5' : '' }}"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                            @if($attendance->student?->photo)
                                                <img src="{{ asset('storage/' . $attendance->student->photo) }}"
                                                     alt="Foto {{ $attendance->student->user?->name }}"
                                                     class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($attendance->student->user?->name ?? 'S', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate font-extrabold text-slate-900 dark:text-white">
                                                {{ $attendance->student->user?->name }}
                                            </div>
                                            <div class="mt-1 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                NIS {{ $attendance->student?->nis }} &middot; {{ $attendance->student->class?->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-200">
                                    <div>{{ $attendance->attendance_date->translatedFormat('d F Y') }}</div>
                                    <div class="mt-1 text-xs font-bold text-slate-400 dark:text-slate-500">
                                        {{ substr((string) $attendance->attendance_time, 0, 5) }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="space-y-2">
                                        <span class="hk-badge
                                            @if($attendance->status === 'hadir')
                                                bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                                            @elseif($attendance->status === 'terlambat')
                                                bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                                            @elseif($attendance->status === 'izin')
                                                bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                                            @elseif($attendance->status === 'sakit')
                                                bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300
                                            @else
                                                bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                                            @endif
                                        ">
                                            {{ $attendance->status === 'alpha' ? 'ALPA' : strtoupper($attendance->status) }}
                                        </span>

                                        <div class="text-xs font-bold
                                            @if($attendance->approval_status === 'approved')
                                                text-emerald-600 dark:text-emerald-300
                                            @elseif($attendance->approval_status === 'rejected')
                                                text-rose-600 dark:text-rose-300
                                            @else
                                                text-amber-600 dark:text-amber-300
                                            @endif
                                        ">
                                            @if($attendance->approval_status === 'approved')
                                                Disetujui
                                            @elseif($attendance->approval_status === 'rejected')
                                                Ditolak
                                            @else
                                                Menunggu persetujuan
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="space-y-1.5">
                                        <span class="hk-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $source }}
                                        </span>

                                        @if($attendance->requestedBy)
                                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                oleh {{ $attendance->requestedBy->name }}
                                            </div>
                                        @endif

                                        @if($attendance->reviewedBy)
                                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                ditinjau {{ $attendance->reviewedBy->name }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="max-w-sm space-y-1 font-semibold text-slate-600 dark:text-slate-300">
                                        <div class="truncate">
                                            {{ $attendance->notes ?: '-' }}
                                        </div>

                                        @if($attendance->review_notes)
                                            <div class="truncate text-xs text-rose-600 dark:text-rose-300">
                                                Tinjauan: {{ $attendance->review_notes }}
                                            </div>
                                        @endif

                                        @if($attendance->attachment_path)
                                            <button
                                                type="button"
                                                x-on:click="previewAttachment(@js($attendance->attachmentUrl()), @js($attendance->attachmentDisplayName()), @js($attendance->attachmentIsImage()))"
                                                class="inline-flex items-center gap-1 text-xs font-extrabold text-blue-600 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200"
                                            >
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                                </svg>
                                                Lihat Lampiran
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <td class="w-40 px-5 py-4">
                                    <div class="mx-auto flex w-32 flex-wrap justify-center gap-2">
                                        @if($attendance->attachment_path)
                                            <button
                                                type="button"
                                                x-on:click="previewAttachment(@js($attendance->attachmentUrl()), @js($attendance->attachmentDisplayName()), @js($attendance->attachmentIsImage()))"
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                                                title="Lihat lampiran"
                                                aria-label="Lihat lampiran"
                                            >
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                                </svg>
                                            </button>
                                        @endif

                                        @if($attendance->approval_status === 'pending')
                                            <button
                                                type="button"
                                                wire:loading.attr="disabled"
                                                wire:target="approve({{ $attendance->id }})"
                                                x-on:click="
                                                    confirmAction({
                                                        title: 'Setujui pengajuan?',
                                                        text: 'Pengajuan ini akan masuk sebagai presensi resmi.',
                                                        confirmText: 'Setujui',
                                                        icon: 'question',
                                                        tone: 'success'
                                                    }).then(confirmed => {
                                                        if (confirmed) $wire.approve({{ $attendance->id }});
                                                    });
                                                "
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-600 disabled:opacity-50 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-emerald-300"
                                                title="Setujui pengajuan"
                                                aria-label="Setujui pengajuan"
                                            >
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="
                                                    Swal.fire({
                                                        title: 'Tolak pengajuan?',
                                                        text: 'Pengajuan akan ditolak dan presensi ditandai alpa.',
                                                        input: 'textarea',
                                                        inputPlaceholder: 'Alasan penolakan (opsional)',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Tolak',
                                                        cancelButtonText: 'Batal',
                                                        reverseButtons: true,
                                                        confirmButtonColor: '#e11d48'
                                                    }).then(result => {
                                                        if (result.isConfirmed) $wire.reject({{ $attendance->id }}, result.value || '');
                                                    });
                                                "
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 disabled:opacity-50 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                                title="Tolak pengajuan"
                                                aria-label="Tolak pengajuan"
                                            >
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif

                                        <button
                                            type="button"
                                            wire:click="edit({{ $attendance->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="edit({{ $attendance->id }})"
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 disabled:opacity-50 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                            title="Edit presensi"
                                            aria-label="Edit presensi"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-300">
                                        Belum ada data presensi untuk filter ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200/70 px-5 py-4 dark:border-slate-800 sm:px-6">
                {{ $attendances->links() }}
            </div>
        </section>
    </div>

    @if($showMobileFilterModal)
        <div
            class="fixed inset-0 z-[80] flex items-end bg-slate-950/60 px-3 pb-3 pt-16 backdrop-blur-sm md:hidden"
            wire:key="manual-attendance-mobile-filter"
        >
            <button
                type="button"
                wire:click="closeMobileFilters"
                class="absolute inset-0"
                aria-label="Tutup filter"
            ></button>

            <div class="relative w-full rounded-3xl border border-white/70 bg-white p-4 shadow-2xl dark:border-slate-800 dark:bg-slate-950">
                <div class="mx-auto h-1.5 w-12 rounded-full bg-slate-200 dark:bg-slate-800"></div>

                <div class="mt-4 flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                            Filter Izin/Sakit
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closeMobileFilters"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Tutup filter"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 space-y-3">
                    <select
                        wire:model="mobileClassFilter"
                        class="hk-input"
                    >
                        <option value="">
                            Semua Kelas
                        </option>

                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>

                    <select
                        wire:model="mobileStatusFilter"
                        class="hk-input"
                    >
                        <option value="">
                            Semua Status
                        </option>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpha">Alpa</option>
                    </select>

                    <select
                        wire:model="mobileApprovalFilter"
                        class="hk-input"
                    >
                        <option value="">Semua Persetujuan</option>
                        <option value="pending">Menunggu</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>

                    <input
                        type="date"
                        wire:model="mobileDateFilter"
                        class="hk-input"
                    >
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <button
                        type="button"
                        wire:click="resetMobileFilters"
                        class="hk-btn-secondary"
                    >
                        Atur Ulang
                    </button>

                    <button
                        type="button"
                        wire:click="applyMobileFilters"
                        class="hk-btn-primary"
                    >
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div
        x-show="attachmentPreview.open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/75 px-4 py-6 backdrop-blur-sm"
        style="z-index: 90;"
    >
        <button
            type="button"
            x-on:click="closeAttachmentPreview()"
            class="absolute inset-0"
            aria-label="Tutup pratinjau lampiran"
        ></button>

        <section class="relative flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-white/15 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-950">
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                        Pratinjau Lampiran
                    </div>
                    <div class="mt-0.5 truncate text-xs font-semibold text-slate-500 dark:text-slate-400" x-text="attachmentPreview.name || 'Lampiran'"></div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <a
                        x-bind:href="attachmentPreview.url"
                        target="_blank"
                        rel="noopener"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 transition hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30"
                        title="Buka di tab baru"
                        aria-label="Buka lampiran di tab baru"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10v10M7 17 17 7" />
                        </svg>
                    </a>

                    <button
                        type="button"
                        x-on:click="closeAttachmentPreview()"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Tutup pratinjau lampiran"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="min-h-0 overflow-auto bg-slate-950/95 p-3">
                <img
                    x-bind:src="attachmentPreview.url"
                    x-bind:alt="attachmentPreview.name || 'Lampiran'"
                    class="mx-auto max-h-[72vh] w-auto max-w-full rounded-xl object-contain"
                >
            </div>
        </section>
    </div>

    @if($showAttendanceDetailModal && $selectedAttendance)
        @php
            $detailSource = 'Manual';

            if ($selectedAttendance->requested_by_user_id) {
                $detailSource = 'Pengajuan';
            } elseif ($selectedAttendance->confidence_score !== null) {
                $detailSource = 'Face Scan';
            } elseif (str_contains((string) $selectedAttendance->notes, 'Alpha otomatis') || str_contains((string) $selectedAttendance->notes, 'Alpa otomatis')) {
                $detailSource = 'Alpa Otomatis';
            }
        @endphp

        <div
            class="fixed inset-0 z-[80] flex items-end bg-slate-950/60 px-3 pb-3 pt-16 backdrop-blur-sm md:hidden"
            wire:key="manual-attendance-detail-{{ $selectedAttendance->id }}"
        >
            <button
                type="button"
                wire:click="closeAttendanceDetail"
                class="absolute inset-0"
                aria-label="Tutup detail"
            ></button>

            <div class="relative max-h-[85vh] w-full overflow-y-auto rounded-3xl border border-white/70 bg-white p-4 shadow-2xl dark:border-slate-800 dark:bg-slate-950">
                <div class="mx-auto h-1.5 w-12 rounded-full bg-slate-200 dark:bg-slate-800"></div>

                <div class="mt-4 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                            Detail Izin/Sakit
                        </div>
                        <div class="mt-1 truncate text-lg font-extrabold text-slate-900 dark:text-white">
                            {{ $selectedAttendance->student->user?->name }}
                        </div>
                        <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            NIS {{ $selectedAttendance->student?->nis }} &middot; {{ $selectedAttendance->student->class?->name }}
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closeAttendanceDetail"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Tutup detail"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-2">
                    <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                            Tanggal
                        </div>
                        <div class="mt-1 text-sm font-extrabold text-slate-800 dark:text-slate-100">
                            {{ $selectedAttendance->attendance_date->translatedFormat('d F Y') }}
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                            Jam
                        </div>
                        <div class="mt-1 text-sm font-extrabold text-slate-800 dark:text-slate-100">
                            {{ substr((string) $selectedAttendance->attendance_time, 0, 5) }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="hk-badge
                        @if($selectedAttendance->status === 'hadir')
                            bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                        @elseif($selectedAttendance->status === 'terlambat')
                            bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                        @elseif($selectedAttendance->status === 'izin')
                            bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                        @elseif($selectedAttendance->status === 'sakit')
                            bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300
                        @else
                            bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                        @endif
                    ">
                        {{ $selectedAttendance->status === 'alpha' ? 'ALPA' : strtoupper($selectedAttendance->status) }}
                    </span>

                    <span class="hk-badge
                        @if($selectedAttendance->approval_status === 'approved')
                            bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                        @elseif($selectedAttendance->approval_status === 'rejected')
                            bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                        @else
                            bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                        @endif
                    ">
                        @if($selectedAttendance->approval_status === 'approved')
                            Disetujui
                        @elseif($selectedAttendance->approval_status === 'rejected')
                            Ditolak
                        @else
                            Menunggu
                        @endif
                    </span>

                    <span class="hk-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        {{ $detailSource }}
                    </span>
                </div>

                <div class="mt-3 rounded-2xl border border-slate-100 bg-white/70 p-3 dark:border-slate-800 dark:bg-slate-900/45">
                    <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Keterangan
                    </div>
                    <div class="mt-1 break-words text-sm font-semibold leading-5 text-slate-600 dark:text-slate-300">
                        {{ $selectedAttendance->notes ?: '-' }}
                    </div>

                    @if($selectedAttendance->review_notes)
                        <div class="mt-2 break-words text-xs font-semibold leading-5 text-rose-600 dark:text-rose-300">
                            Tinjauan: {{ $selectedAttendance->review_notes }}
                        </div>
                    @endif
                </div>

                @if($selectedAttendance->requestedBy || $selectedAttendance->reviewedBy)
                    <div class="mt-3 space-y-1 rounded-2xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500 dark:bg-slate-900/70 dark:text-slate-400">
                        @if($selectedAttendance->requestedBy)
                            <div>Diajukan oleh {{ $selectedAttendance->requestedBy->name }}</div>
                        @endif

                        @if($selectedAttendance->reviewedBy)
                            <div>Ditinjau oleh {{ $selectedAttendance->reviewedBy->name }}</div>
                        @endif
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-2 gap-2">
                    @if($selectedAttendance->attachment_path)
                        <button
                            type="button"
                            x-on:click="previewAttachment(@js($selectedAttendance->attachmentUrl()), @js($selectedAttendance->attachmentDisplayName()), @js($selectedAttendance->attachmentIsImage()))"
                            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl bg-blue-100 px-3 py-2 text-xs font-extrabold text-blue-700 transition hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30"
                        >
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                            </svg>
                            Lampiran
                        </button>
                    @endif

                    <button
                        type="button"
                        wire:click="edit({{ $selectedAttendance->id }})"
                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl bg-amber-100 px-3 py-2 text-xs font-extrabold text-amber-700 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30 {{ $selectedAttendance->attachment_path ? '' : 'col-span-2' }}"
                    >
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                        </svg>
                        Edit
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div
        x-show="formOpen"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
    >
        <section
            x-on:click.outside="$wire.closeFormModal()"
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide {{ $isEdit ? 'text-amber-600 dark:text-amber-300' : 'text-blue-600 dark:text-blue-300' }}">
                        {{ $isEdit ? 'Koreksi Presensi' : 'Presensi Manual' }}
                    </div>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        {{ $isEdit ? 'Edit Data Presensi' : 'Input Izin/Sakit Manual' }}
                    </h2>
                </div>

                <button
                    type="button"
                    wire:click="closeFormModal"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Tutup form"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-5 p-5">
                <div>
                    <label class="hk-label">Kelas</label>
                    <select wire:model.live="selectedClass" class="hk-input">
                        <option value="">Semua kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedClass')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="hk-label">Siswa</label>
                    <select wire:model="student_id" class="hk-input">
                        <option value="">Pilih siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->user?->name }} - {{ $student->nis }} ({{ $student->class?->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="hk-label">Tanggal</label>
                        <input
                            type="date"
                            wire:model="attendance_date"
                            max="{{ today()->format('Y-m-d') }}"
                            class="hk-input"
                        >
                        @error('attendance_date')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Jam</label>
                        <input type="time" wire:model="attendance_time" class="hk-input">
                        @error('attendance_time')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="hk-label">Status</label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($statusOptions as $value => $label)
                            <label class="flex min-h-12 cursor-pointer items-center justify-between gap-3 rounded-2xl border px-4 py-3 transition
                                {{ $status === $value
                                    ? 'border-blue-400 bg-blue-50 text-blue-700 shadow-sm dark:border-blue-500/60 dark:bg-blue-500/10 dark:text-blue-200'
                                    : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                <span class="text-sm font-extrabold">{{ $label }}</span>
                                <input
                                    type="radio"
                                    wire:model.live="status"
                                    value="{{ $value }}"
                                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                >
                            </label>
                        @endforeach
                    </div>
                    @error('status')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="hk-label">Keterangan</label>
                    <textarea
                        wire:model="notes"
                        placeholder="{{ $isEdit ? 'Wajib isi alasan koreksi, misalnya salah status atau penyesuaian data.' : 'Contoh: izin acara keluarga atau sakit demam.' }}"
                        class="hk-input min-h-28"
                    ></textarea>
                    @error('notes')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="hk-label">Lampiran <span class="font-semibold text-slate-400">(opsional)</span></label>

                    @if($isEdit && $existingAttachmentPath)
                            <div class="mb-3 rounded-2xl border border-slate-200 bg-slate-50/80 p-3 dark:border-slate-800 dark:bg-slate-900/70">
                                <div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    Lampiran Saat Ini
                                </div>
                                <button
                                    type="button"
                                    x-on:click="previewAttachment(@js(asset('storage/' . $existingAttachmentPath)), @js($existingAttachmentName ?: 'Lampiran'), @js($existingAttachmentIsImage))"
                                    class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-2xl bg-blue-100 px-4 py-2 text-sm font-extrabold text-blue-700 transition hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30"
                                >
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                    </svg>
                                    Buka Lampiran
                                </button>

                                @if($existingAttachmentName)
                                    <div class="mt-2 truncate text-xs font-bold text-slate-500 dark:text-slate-400">
                                        {{ $existingAttachmentName }}
                                    </div>
                                @endif

                                <label class="mt-3 flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300">
                                    <input
                                        type="checkbox"
                                        wire:model="removeAttachment"
                                        class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                                    >
                                    Hapus lampiran saat disimpan
                                </label>
                            </div>
                    @endif

                    <input
                        type="file"
                        wire:model="attachment"
                        accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf"
                        class="hk-input file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-blue-300"
                    >
                    <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Format PDF, JPG, PNG, atau WebP. Maksimal 2 MB.
                    </div>
                    @if($attachment)
                        <div class="mt-2 truncate text-xs font-bold text-blue-600 dark:text-blue-300">
                            {{ $attachment->getClientOriginalName() }}
                        </div>
                    @endif
                    @error('attachment')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-semibold leading-6 text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                    {{ $isEdit
                        ? 'Keterangan wajib diisi agar koreksi punya jejak audit. Mode koreksi akan mengubah presensi menjadi disetujui dan mencatat peninjau.'
                        : 'Data manual akan langsung tersimpan sebagai disetujui. Satu siswa hanya boleh memiliki satu presensi per tanggal. Jika sudah ada, gunakan tombol edit di daftar presensi.' }}
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeFormModal"
                        class="hk-btn-secondary"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="hk-btn-primary"
                    >
                        <span wire:loading.remove wire:target="save">
                            {{ $isEdit ? 'Simpan Koreksi' : 'Simpan Presensi' }}
                        </span>

                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
