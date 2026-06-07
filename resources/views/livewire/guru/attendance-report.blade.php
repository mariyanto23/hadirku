<div
    x-data="{ detailOpen: @entangle('showAttendanceDetailModal'), filterOpen: @entangle('showMobileFilterModal'), exportOpen: false }"
    x-on:keydown.escape.window="if (detailOpen) $wire.closeAttendanceDetail(); else if (filterOpen) $wire.closeMobileFilters(); else exportOpen = false"
>
    @php
        $statusLabel = fn ($status) => match ($status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpa',
            default => '-',
        };

        $approvalLabel = fn ($approval) => match ($approval) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => '-',
        };

        $statusBadge = fn ($status) => match ($status) {
            'hadir' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
            'terlambat' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
            'izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            'sakit' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300',
            default => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
        };

        $approvalBadge = fn ($approval) => match ($approval) {
            'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
            default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        };
    @endphp

    <div class="hk-page">
        <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
            @foreach([
                [
                    'label' => 'Total Data',
                    'value' => $summary['total'],
                    'labelClass' => 'text-blue-600 dark:text-blue-300',
                    'iconClass' => 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300',
                    'icon' => 'M4 6h16M4 12h16M4 18h10',
                ],
                [
                    'label' => 'Hadir',
                    'value' => $summary['present'],
                    'labelClass' => 'text-emerald-600 dark:text-emerald-300',
                    'iconClass' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300',
                    'icon' => 'M20 6 9 17l-5-5',
                ],
                [
                    'label' => 'Terlambat',
                    'value' => $summary['late'],
                    'labelClass' => 'text-amber-600 dark:text-amber-300',
                    'iconClass' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300',
                    'icon' => 'M12 6v6l4 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                ],
                [
                    'label' => 'Alpa',
                    'value' => $summary['absent'],
                    'labelClass' => 'text-rose-600 dark:text-rose-300',
                    'iconClass' => 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-300',
                    'icon' => 'M18 6 6 18M6 6l12 12',
                ],
            ] as $card)
                <div class="hk-card p-3 sm:p-5">
                    <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wide {{ $card['labelClass'] }} sm:text-sm">
                                {{ $card['label'] }}
                            </div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                                {{ $card['value'] }}
                            </div>
                        </div>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl {{ $card['iconClass'] }} sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="hk-card p-5 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Rekap
                    </div>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:text-3xl">
                        Rekap Presensi
                    </h1>
                    <div class="mt-2 text-sm font-semibold text-slate-500 dark:text-slate-400">
                        {{ $selectedClass?->name ?: 'Semua Kelas' }}
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('guru.manual.attendance') }}" class="hk-btn-secondary w-full sm:w-auto">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>
                        Input Izin/Sakit
                    </a>

                    <div class="relative w-full sm:w-auto" x-on:click.outside="exportOpen = false">
                        <button
                            type="button"
                            x-on:click="exportOpen = ! exportOpen"
                            wire:loading.attr="disabled"
                            wire:target="exportExcel,exportPdf"
                            class="hk-btn-success w-full sm:w-auto"
                            @disabled($classes->isEmpty())
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                            </svg>
                            Ekspor
                            <svg class="h-4 w-4 transition" :class="{ 'rotate-180': exportOpen }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-show="exportOpen"
                            x-transition.opacity
                            x-cloak
                            class="absolute right-0 z-40 mt-2 w-full min-w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white p-1 shadow-2xl dark:border-slate-700 dark:bg-slate-900 sm:w-44"
                        >
                            <button type="button" wire:click="exportExcel" x-on:click="exportOpen = false" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-bold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-200 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-300">
                                Excel
                            </button>
                            <button type="button" wire:click="exportPdf" x-on:click="exportOpen = false" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700 dark:text-slate-200 dark:hover:bg-blue-500/10 dark:hover:text-blue-300">
                                PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex gap-2 md:hidden">
                    <input type="text" wire:model.live="search" placeholder="Cari nama atau NIS..." class="hk-input min-w-0 flex-1">
                    <button type="button" wire:click="openMobileFilters" class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300" aria-label="Buka filter">
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

                <div class="hidden space-y-3 md:block">
                    <div class="max-w-2xl">
                        <input type="text" wire:model.live="search" placeholder="Cari nama atau NIS..." class="hk-input w-full">
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(8.5rem,.95fr)_minmax(8.5rem,.95fr)_minmax(9.5rem,.95fr)_auto]">
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

                        <button type="button" wire:click="resetFilters" class="hk-btn-secondary w-full lg:w-auto">
                            Atur Ulang
                        </button>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-3 shadow-sm dark:border-slate-800 dark:bg-slate-950/70">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="mr-1 text-[10px] font-extrabold uppercase tracking-wide text-slate-400 dark:text-slate-500">Periode</span>
                            @foreach(['today' => 'Hari Ini', 'seven_days' => '7 Hari', 'this_month' => 'Bulan Ini', 'custom' => 'Kustom'] as $presetValue => $presetLabel)
                                <button type="button" wire:click="applyDatePreset('{{ $presetValue }}')" class="rounded-full border px-3 py-2 text-xs font-extrabold transition {{ $datePreset === $presetValue ? 'border-blue-600 bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-blue-500/40 dark:hover:bg-blue-500/10 dark:hover:text-blue-300' }}">
                                    {{ $presetLabel }}
                                </button>
                            @endforeach

                            @if($datePreset === 'custom')
                                <div class="grid min-w-[20rem] flex-1 grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-2">
                                    <input type="date" wire:model.live="dateStartFilter" aria-label="Tanggal mulai" class="hk-input">
                                    <span class="flex h-11 w-6 items-center justify-center text-sm font-extrabold text-slate-400 dark:text-slate-500">-</span>
                                    <input type="date" wire:model.live="dateEndFilter" aria-label="Tanggal selesai" class="hk-input">
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(count($activeFilters) > 0)
                        <div class="hidden flex-wrap items-center gap-2 md:flex">
                            @foreach($activeFilters as $filter)
                                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                                    {{ $filter }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                <span>{{ $resultText }}</span>
                @if($attendances->hasPages())
                    <span>Halaman {{ $attendances->currentPage() }} dari {{ $attendances->lastPage() }}</span>
                @endif
            </div>

            <div class="mt-4 space-y-3 md:hidden">
                @forelse($attendances as $attendance)
                    <button type="button" wire:click="openAttendanceDetail({{ $attendance->id }})" class="w-full rounded-2xl border border-slate-200 bg-white/80 p-4 text-left shadow-sm transition active:scale-[0.99] dark:border-slate-800 dark:bg-slate-950/35">
                        <div class="flex items-start gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                {{ strtoupper(substr($attendance->student->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">{{ $attendance->student->user->name }}</div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">NIS {{ $attendance->student->nis }} &middot; {{ $attendance->student->class->name }}</div>
                                <div class="mt-2 text-xs font-bold text-slate-400 dark:text-slate-500">{{ $attendance->attendance_date->translatedFormat('d F Y') }}</div>
                            </div>
                            <span class="hk-badge shrink-0 {{ $statusBadge($attendance->status) }}">{{ $statusLabel($attendance->status) }}</span>
                        </div>
                    </button>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm font-bold text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        Tidak ada presensi untuk filter yang dipilih.
                    </div>
                @endforelse
            </div>

            <div class="mt-4 hidden overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 md:block">
                <table class="hk-table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Waktu</th>
                            <th>Presensi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>
                                    <div class="font-extrabold text-slate-900 dark:text-white">{{ $attendance->student->user->name }}</div>
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">NIS {{ $attendance->student->nis }} &middot; {{ $attendance->student->class->name }}</div>
                                </td>
                                <td>
                                    <div class="font-bold">{{ $attendance->attendance_date->translatedFormat('d F Y') }}</div>
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ substr((string) $attendance->attendance_time, 0, 5) }}</div>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="hk-badge {{ $statusBadge($attendance->status) }}">{{ $statusLabel($attendance->status) }}</span>
                                        <span class="hk-badge {{ $approvalBadge($attendance->approval_status) }}">{{ $approvalLabel($attendance->approval_status) }}</span>
                                    </div>
                                </td>
                                <td>{{ $attendance->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-sm font-bold text-slate-500 dark:text-slate-400">
                                    Tidak ada presensi untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $attendances->links() }}
            </div>
        </section>

        @if($showAttendanceDetailModal && $selectedAttendance)
            <div class="fixed inset-0 z-[80] flex items-end bg-slate-950/60 px-3 pb-3 pt-16 backdrop-blur-sm md:hidden">
                <button type="button" wire:click="closeAttendanceDetail" class="absolute inset-0" aria-label="Tutup detail presensi"></button>
                <div class="relative max-h-[85vh] w-full overflow-y-auto rounded-3xl border border-white/70 bg-white p-4 shadow-2xl dark:border-slate-800 dark:bg-slate-950">
                    <div class="mx-auto h-1.5 w-12 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                    <div class="mt-4 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white">Detail Presensi</div>
                            <div class="mt-1 truncate text-lg font-extrabold text-slate-900 dark:text-white">{{ $selectedAttendance->student->user->name }}</div>
                            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">NIS {{ $selectedAttendance->student->nis }} &middot; {{ $selectedAttendance->student->class->name }}</div>
                        </div>
                        <button type="button" wire:click="closeAttendanceDetail" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Tutup detail presensi">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">Tanggal</div>
                            <div class="mt-1 text-sm font-extrabold text-slate-800 dark:text-slate-100">{{ $selectedAttendance->attendance_date->translatedFormat('d F Y') }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">Jam</div>
                            <div class="mt-1 text-sm font-extrabold text-slate-800 dark:text-slate-100">{{ substr((string) $selectedAttendance->attendance_time, 0, 5) }}</div>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="hk-badge {{ $statusBadge($selectedAttendance->status) }}">{{ $statusLabel($selectedAttendance->status) }}</span>
                        <span class="hk-badge {{ $approvalBadge($selectedAttendance->approval_status) }}">{{ $approvalLabel($selectedAttendance->approval_status) }}</span>
                    </div>
                    <div class="mt-3 rounded-2xl border border-slate-100 bg-white/70 p-3 dark:border-slate-800 dark:bg-slate-900/45">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">Keterangan</div>
                        <div class="mt-1 break-words text-sm font-semibold leading-5 text-slate-600 dark:text-slate-300">{{ $selectedAttendance->notes ?: '-' }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if($showMobileFilterModal)
            <div class="fixed inset-0 z-[80] flex items-end bg-slate-950/60 px-3 pb-3 pt-16 backdrop-blur-sm md:hidden">
                <button type="button" wire:click="closeMobileFilters" class="absolute inset-0" aria-label="Tutup filter"></button>
                <div class="relative w-full rounded-3xl border border-white/70 bg-white p-4 shadow-2xl dark:border-slate-800 dark:bg-slate-950">
                    <div class="mx-auto h-1.5 w-12 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">Filter Rekap</div>
                        <button type="button" wire:click="closeMobileFilters" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-slate-900 dark:text-slate-300" aria-label="Tutup filter">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="mt-5 space-y-3">
                        <select wire:model="mobileClassFilter" class="hk-input">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>

                        <select wire:model="mobileStatusFilter" class="hk-input">
                            <option value="">Semua Status</option>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpa</option>
                        </select>
                        <select wire:model="mobileApprovalFilter" class="hk-input">
                            <option value="">Semua Persetujuan</option>
                            <option value="pending">Menunggu</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3 dark:border-slate-800 dark:bg-slate-900/70">
                            <div class="mb-2 text-[10px] font-extrabold uppercase tracking-wide text-slate-400 dark:text-slate-500">Periode</div>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['today' => 'Hari Ini', 'seven_days' => '7 Hari', 'this_month' => 'Bulan Ini', 'custom' => 'Kustom'] as $presetValue => $presetLabel)
                                    <button type="button" wire:click="applyMobileDatePreset('{{ $presetValue }}')" class="rounded-2xl border px-3 py-2.5 text-xs font-extrabold transition {{ $mobileDatePreset === $presetValue ? 'border-blue-600 bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'border-slate-200 bg-white text-slate-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300' }}">
                                        {{ $presetLabel }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @if($mobileDatePreset === 'custom')
                            <div class="grid grid-cols-2 gap-3">
                                <label>
                                    <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">Mulai</span>
                                    <input type="date" wire:model="mobileDateStartFilter" class="hk-input">
                                </label>
                                <label>
                                    <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">Selesai</span>
                                    <input type="date" wire:model="mobileDateEndFilter" class="hk-input">
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <button type="button" wire:click="resetMobileFilters" class="hk-btn-secondary">Atur Ulang</button>
                        <button type="button" wire:click="applyMobileFilters" class="hk-btn-primary">Terapkan</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
