<div class="hk-page max-w-full overflow-x-hidden">

    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-gradient-to-br from-blue-600 via-indigo-600 to-emerald-500 p-6 text-white shadow-glow sm:p-8">
        <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center rounded-full bg-white/20 px-4 py-2 text-sm font-bold backdrop-blur">
                        Dashboard SDN 01 Jatipurwo
                    </div>

                    <x-realtime-clock variant="hero" />
                </div>

                <h1 class="mt-5 max-w-2xl text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Pantau presensi siswa hari ini dengan cepat.
                </h1>
            </div>

            <div class="rounded-2xl bg-white/20 p-5 backdrop-blur">
                <div class="text-sm font-bold text-blue-50">
                    Kehadiran tercatat hari ini
                </div>

                <div class="mt-4 flex items-end gap-4">
                    <div class="text-5xl font-extrabold">
                        {{ $attendanceRate }}%
                    </div>
                    <div class="pb-2 text-sm font-semibold text-blue-50">
                        {{ $todayPresent }} dari {{ $totalStudents }} siswa
                    </div>
                </div>

                <div class="mt-5 h-3 overflow-hidden rounded-full bg-white/20">
                    <div class="h-full rounded-full bg-white"
                         style="width: {{ min($attendanceRate, 100) }}%"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 sm:text-sm">
                        Total Siswa
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $totalStudents }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0m8 0a4 4 0 1 1-8 0M4 20a8 8 0 0 1 16 0" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300 sm:text-sm">
                        Hadir
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $todayPresent }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300 sm:text-sm">
                        Terlambat
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $todayLate }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                        Izin
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $todayIzin }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300 sm:text-sm">
                        Sakit
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $todaySakit }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M5 12h14" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-rose-600 dark:text-rose-300 sm:text-sm">
                        Alpa
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $todayAlpha }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.3 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.3a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-sky-600 dark:text-sky-300 sm:text-sm">
                        Menunggu
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $pendingLeaveRequests }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hk-card p-3 sm:p-5">
            <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wide text-fuchsia-600 dark:text-fuchsia-300 sm:text-sm">
                        Face ID Kurang
                    </div>
                    <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                        {{ $faceIncompleteStudents }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-fuchsia-100 text-fuchsia-600 dark:bg-fuchsia-500/20 dark:text-fuchsia-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 1 1 8 0m-10 9a6 6 0 0 1 12 0M4 4h4m8 0h4M4 20h4m8 0h4" />
                    </svg>
                </div>
            </div>
        </div>

    </section>

    <section class="grid min-w-0 max-w-full gap-6 overflow-hidden lg:grid-cols-[0.9fr_1.1fr]">

        <div class="hk-card min-w-0 overflow-hidden p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                        Aksi Cepat
                    </h2>
                </div>
            </div>

            <div class="mt-6 grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-1">
                <a href="{{ route('admin.students') }}" class="hk-btn-primary w-full min-w-0 justify-start text-left">
                    Kelola Siswa
                </a>
                <a href="{{ route('admin.face-registration') }}" class="hk-btn-secondary w-full min-w-0 justify-start text-left">
                    Registrasi Wajah
                </a>
                <a href="{{ route('admin.manual.attendance') }}" class="hk-btn-secondary w-full min-w-0 justify-start text-left">
                    Izin/Sakit
                </a>
                <a href="{{ route('admin.gurus') }}" class="hk-btn-secondary w-full min-w-0 justify-start text-left">
                    Kelola Guru
                </a>
            </div>
        </div>

        <div class="hk-card min-w-0 overflow-hidden p-5 sm:p-6">
            <div class="flex min-w-0 items-center justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                        Aktivitas Presensi
                    </h2>
                </div>
                <div class="hidden rounded-2xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300 sm:block">
                    {{ $totalClasses }} kelas
                </div>
            </div>

            <div class="mt-6 min-w-0 space-y-3">
                @forelse($recentAttendances as $attendance)
                    @php
                        $studentName = $attendance->student?->user?->name ?? 'Siswa';
                    @endphp

                    <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-100 bg-white/70 p-3 dark:border-slate-800 dark:bg-slate-950/30 sm:gap-4 sm:p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-11 sm:w-11">
                            {{ strtoupper(substr($studentName, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                {{ $studentName }}
                            </div>
                            <div class="mt-0.5 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                                {{ $attendance->student?->class?->name ?? '-' }}
                                &middot;
                                {{ $attendance->attendance_date?->format('d/m/Y') }}
                                &middot;
                                {{ $attendance->attendance_time }}
                            </div>
                        </div>
                        <span class="hk-badge max-w-[6.5rem] shrink-0 truncate
                            @if($attendance->status === 'hadir')
                                bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                            @elseif($attendance->status === 'terlambat')
                                bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                            @elseif($attendance->status === 'alpha')
                                bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                            @elseif($attendance->status === 'sakit')
                                bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300
                            @else
                                bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                            @endif
                        ">
                            {{ $attendance->status === 'alpha' ? 'ALPA' : strtoupper($attendance->status) }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700">
                        <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                            Belum ada aktivitas presensi.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

    </section>

    <section class="hk-card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                    Ringkasan Per Kelas
                </h2>
            </div>

            <a href="{{ route('admin.attendance.report') }}" class="hk-btn-secondary w-full sm:w-auto">
                Lihat Rekap
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="bg-slate-50/90 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-4 text-left font-bold">Kelas</th>
                        <th class="px-5 py-4 text-center font-bold">Siswa</th>
                        <th class="px-5 py-4 text-center font-bold">Hadir</th>
                        <th class="px-5 py-4 text-center font-bold">Terlambat</th>
                        <th class="px-5 py-4 text-center font-bold">Izin/Sakit</th>
                        <th class="px-5 py-4 text-center font-bold">Alpa</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($classSummaries as $classSummary)
                        <tr class="transition hover:bg-blue-50/60 dark:hover:bg-slate-800/45">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900 dark:text-white">
                                    {{ $classSummary['name'] }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-600 dark:text-slate-300">
                                {{ $classSummary['students_count'] }}
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-emerald-600 dark:text-emerald-300">
                                {{ $classSummary['hadir'] }}
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-amber-600 dark:text-amber-300">
                                {{ $classSummary['terlambat'] }}
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-blue-600 dark:text-blue-300">
                                {{ $classSummary['izin_sakit'] }}
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-rose-600 dark:text-rose-300">
                                {{ $classSummary['alpha'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                    Belum ada kelas.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
