<div>
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
                    'label' => 'Izin/Sakit',
                    'value' => $summary['leave'],
                    'labelClass' => 'text-indigo-600 dark:text-indigo-300',
                    'iconClass' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300',
                    'icon' => 'M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01',
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
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Rekap
                    </div>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:text-3xl">
                        Rekap Bulan {{ $monthName }}
                    </h1>
                    <div class="mt-2 text-sm font-semibold text-slate-500 dark:text-slate-400">
                        {{ $monthLabel }}{{ $student?->class ? ' - '.$student->class->name : '' }}
                    </div>
                </div>

                <a href="{{ route('siswa.leave-request') }}" class="hk-btn-secondary w-full sm:w-auto">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    Ajukan Izin/Sakit
                </a>
            </div>

            @unless($student)
                <div class="mt-5 rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4 text-sm font-semibold leading-6 text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                    Data siswa belum ditemukan. Hubungi admin agar rekap dapat ditampilkan.
                </div>
            @endunless

            <div class="mt-6 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach([
                        '' => 'Semua',
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpa',
                    ] as $statusValue => $statusText)
                        <button
                            type="button"
                            wire:click="setStatusFilter('{{ $statusValue }}')"
                            class="rounded-full border px-3 py-2 text-xs font-extrabold transition {{ $statusFilter === $statusValue ? 'border-blue-600 bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-blue-500/40 dark:hover:bg-blue-500/10 dark:hover:text-blue-300' }}"
                        >
                            {{ $statusText }}
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center justify-between gap-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span>{{ $resultText }}</span>
                    @if($attendances->hasPages())
                        <span>Halaman {{ $attendances->currentPage() }} dari {{ $attendances->lastPage() }}</span>
                    @endif
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($attendances as $attendance)
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/35">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                                    {{ $attendance->attendance_date?->translatedFormat('d F Y') }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ substr((string) $attendance->attendance_time, 0, 5) ?: '-' }}
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-wrap justify-end gap-2">
                                <span class="hk-badge {{ $statusBadge($attendance->status) }}">
                                    {{ $statusLabel($attendance->status) }}
                                </span>
                                <span class="hk-badge {{ $approvalBadge($attendance->approval_status) }}">
                                    {{ $approvalLabel($attendance->approval_status) }}
                                </span>
                            </div>
                        </div>

                        @if($attendance->notes || $attendance->attachment_path)
                            <div class="mt-3 rounded-2xl bg-slate-50 px-3 py-2 text-sm font-semibold leading-6 text-slate-600 dark:bg-slate-900/70 dark:text-slate-300">
                                @if($attendance->notes)
                                    <div class="break-words">
                                        {{ $attendance->notes }}
                                    </div>
                                @endif

                                @if($attendance->attachment_path)
                                    <a
                                        href="{{ $attendance->attachmentUrl() }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="mt-1 inline-flex items-center gap-1 text-xs font-extrabold text-blue-600 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                        </svg>
                                        Lihat Lampiran
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-500 dark:text-slate-400">
                            Belum ada presensi pada bulan ini.
                        </p>
                        @if($statusFilter)
                            <button type="button" wire:click="resetFilters" class="mt-4 hk-btn-secondary">
                                Atur Ulang
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $attendances->links() }}
            </div>
        </section>

        <section class="hk-card p-5 sm:p-6">
            @php
                $calendarClass = fn ($status, $holiday = null, $isSchoolDay = true) => match ($status) {
                    'hadir' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300',
                    'terlambat' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300',
                    'izin' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/15 dark:text-blue-300',
                    'sakit' => 'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-500/30 dark:bg-indigo-500/15 dark:text-indigo-300',
                    'alpha' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/15 dark:text-rose-300',
                    default => $holiday
                        ? 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/15 dark:text-sky-300'
                        : ($isSchoolDay
                            ? 'border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-slate-950/35 dark:text-slate-500'
                            : 'border-slate-200 bg-slate-100 text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400'),
                };

                $calendarDotClass = fn ($status, $holiday = null, $isSchoolDay = true) => match ($status) {
                    'hadir' => 'bg-emerald-500',
                    'terlambat' => 'bg-amber-500',
                    'izin' => 'bg-blue-500',
                    'sakit' => 'bg-indigo-500',
                    'alpha' => 'bg-rose-500',
                    default => $holiday ? 'bg-sky-500' : ($isSchoolDay ? 'bg-slate-300 dark:bg-slate-700' : 'bg-slate-500 dark:bg-slate-400'),
                };
            @endphp

            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Kalender
                    </div>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white sm:text-2xl">
                        Kalender Bulan {{ $monthName }}
                    </h2>
                </div>

                <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                    {{ $calendar['total'] }} data bulan ini
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-950/35 sm:p-5">
                <div class="grid grid-cols-7 gap-1.5 text-center text-[10px] font-extrabold uppercase tracking-wide text-slate-400 dark:text-slate-500 sm:gap-2 sm:text-xs">
                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                        <div class="py-1">{{ $dayName }}</div>
                    @endforeach
                </div>

                <div class="mt-2 space-y-1.5 sm:space-y-2">
                    @foreach($calendar['weeks'] as $week)
                        <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                            @foreach($week as $day)
                                @if($day['blank'])
                                    <div class="aspect-square rounded-2xl border border-transparent"></div>
                                @else
                                    <div
                                        class="aspect-square rounded-2xl border p-1.5 {{ $calendarClass($day['status'], $day['holiday'] ?? null, $day['is_school_day'] ?? true) }} {{ $day['is_today'] ? 'ring-2 ring-blue-500 ring-offset-2 ring-offset-slate-50 dark:ring-offset-slate-950' : '' }} sm:p-2"
                                        title="{{ $day['holiday']['title'] ?? (($day['is_school_day'] ?? true) ? '' : 'Bukan hari sekolah') }}"
                                    >
                                        <div class="flex h-full flex-col justify-between">
                                            <div class="text-left text-xs font-extrabold sm:text-sm">
                                                {{ $day['day'] }}
                                            </div>

                                            <div class="flex items-center justify-end">
                                                <span class="h-2 w-2 rounded-full {{ $calendarDotClass($day['status'], $day['holiday'] ?? null, $day['is_school_day'] ?? true) }}"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    @foreach([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpa',
                    ] as $status => $label)
                        <div class="flex items-center gap-2 text-xs font-extrabold text-slate-600 dark:text-slate-300">
                            <span class="h-2.5 w-2.5 rounded-full {{ $calendarDotClass($status) }}"></span>
                            {{ $label }}
                        </div>
                    @endforeach

                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-600 dark:text-slate-300">
                        <span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span>
                        Libur
                    </div>

                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-600 dark:text-slate-300">
                        <span class="h-2.5 w-2.5 rounded-full bg-slate-500 dark:bg-slate-400"></span>
                        Bukan Hari Sekolah
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
