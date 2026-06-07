<x-app-layout>
    @php
        $statusLabels = [
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpa',
        ];

        $statusClasses = [
            'hadir' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
            'terlambat' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20',
            'izin' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20',
            'sakit' => 'bg-violet-50 text-violet-700 border-violet-100 dark:bg-violet-500/10 dark:text-violet-300 dark:border-violet-500/20',
            'alpha' => 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-500/10 dark:text-rose-300 dark:border-rose-500/20',
        ];
    @endphp

    <div class="hk-page">
        <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                    HadirKu
                </p>
                <h1 class="mt-1 text-2xl font-black text-slate-950 dark:text-white sm:text-3xl">
                    Beranda
                </h1>
            </div>

            <x-realtime-clock />
        </section>

        <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                            Kelas Bawaan
                        </div>
                        <div class="mt-1 truncate text-xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-2xl">
                            {{ $defaultClass?->name ?: '-' }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12M7 9h10M7 13h6M5 19h14" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300 sm:text-sm">
                            Presensi Hari Ini
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $attendanceTodayCount }}
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
                            Menunggu
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $pendingRequestsCount }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300 sm:text-sm">
                            Siap Dipindai
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $readyStudentsCount }}/{{ $studentsCount }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3H5a2 2 0 0 0-2 2v2m14-4h2a2 2 0 0 1 2 2v2M7 21H5a2 2 0 0 1-2-2v-2m18 0v2a2 2 0 0 1-2 2h-2M8 11a4 4 0 0 1 8 0m-9 6a5 5 0 0 1 10 0" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        @unless($defaultClass)
            <section class="rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4 text-sm font-semibold leading-6 text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                Kelas bawaan belum diatur. Hubungi admin agar beranda dapat menampilkan data kelas.
            </section>
        @endunless

        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Aksi Utama
                    </p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <a href="{{ route('guru.face.attendance') }}" class="hk-card group p-5 transition hover:-translate-y-0.5 hover:shadow-glow sm:p-6">
                    <div class="flex min-h-28 items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                Presensi
                            </div>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white">
                                Presensi Wajah
                            </h2>
                        </div>
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-500/20 dark:text-blue-300">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 3H5a2 2 0 0 0-2 2v2m14-4h2a2 2 0 0 1 2 2v2M7 21H5a2 2 0 0 1-2-2v-2m18 0v2a2 2 0 0 1-2 2h-2M8 11a4 4 0 0 1 8 0m-9 6a5 5 0 0 1 10 0" />
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('guru.face-registration') }}" class="hk-card group p-5 transition hover:-translate-y-0.5 hover:shadow-glow sm:p-6">
                    <div class="flex min-h-28 items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">
                                Data Wajah
                            </div>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white">
                                Registrasi Wajah
                            </h2>
                        </div>
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-500/20 dark:text-emerald-300">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 1 1 8 0m-10 9a6 6 0 0 1 12 0M4 4h4m8 0h4M4 20h4m8 0h4" />
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('guru.manual.attendance') }}" class="hk-card group p-5 transition hover:-translate-y-0.5 hover:shadow-glow sm:p-6">
                    <div class="flex min-h-28 items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">
                                Manual
                            </div>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white">
                                Izin/Sakit
                            </h2>
                        </div>
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white dark:bg-indigo-500/20 dark:text-indigo-300">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="hk-card overflow-hidden">
                <div class="border-b border-slate-200/70 px-5 py-4 dark:border-slate-800 sm:px-6">
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Aktivitas Terakhir
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentAttendances as $attendance)
                        <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                    {{ $attendance->student?->user?->name ?: 'Siswa tidak ditemukan' }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $attendance->attendance_date?->translatedFormat('d F Y') }} · {{ \Illuminate\Support\Str::of($attendance->attendance_time)->substr(0, 5) }}
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full border px-3 py-1 text-xs font-extrabold {{ $statusClasses[$attendance->status] ?? $statusClasses['alpha'] }}">
                                {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center sm:px-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-bold text-slate-500 dark:text-slate-400">
                                Belum ada aktivitas presensi.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="hk-card overflow-hidden">
                <div class="border-b border-slate-200/70 px-5 py-4 dark:border-slate-800 sm:px-6">
                    <div class="text-sm font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300">
                        Pengajuan Menunggu
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($pendingRequests as $attendance)
                        <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                    {{ $attendance->student?->user?->name ?: 'Siswa tidak ditemukan' }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $attendance->attendance_date?->translatedFormat('d F Y') }}
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full border px-3 py-1 text-xs font-extrabold {{ $statusClasses[$attendance->status] ?? $statusClasses['izin'] }}">
                                {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center sm:px-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-bold text-slate-500 dark:text-slate-400">
                                Tidak ada pengajuan yang menunggu.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
