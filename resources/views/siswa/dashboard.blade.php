<x-app-layout>
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

        $descriptorCount = $student?->descriptors_count ?? 0;
        $faceReady = $descriptorCount >= $minimumDescriptors;
        $faceStatus = $faceReady
            ? 'Siap Digunakan'
            : ($descriptorCount > 0 ? 'Perlu Ditambah' : 'Belum Ada');
        $latestAttendance = $recentAttendances->first();
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
                <div class="mt-2 text-sm font-semibold text-slate-500 dark:text-slate-400">
                    {{ auth()->user()->name }}{{ $student?->class ? ' - '.$student->class->name : '' }}
                </div>
            </div>

            <x-realtime-clock />
        </section>

        <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                            Status Wajah
                        </div>
                        <div class="mt-1 truncate text-lg font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-xl">
                            {{ $faceStatus }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl {{ $faceReady ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300' }} sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 1 1 8 0m-10 9a6 6 0 0 1 12 0M4 4h4m8 0h4M4 20h4m8 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="hk-card p-3 sm:p-5">
                <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300 sm:text-sm">
                            Presensi Hari Ini
                        </div>
                        <div class="mt-1 truncate text-lg font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-xl">
                            {{ $todayAttendance ? $statusLabel($todayAttendance->status) : 'Belum Ada' }}
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
                            Pengajuan Aktif
                        </div>
                        <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                            {{ $activeRequests->count() }}
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
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300 sm:text-sm">
                            Riwayat Terakhir
                        </div>
                        <div class="mt-1 truncate text-lg font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-xl">
                            {{ $latestAttendance ? $statusLabel($latestAttendance->status) : '-' }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300 sm:h-12 sm:w-12">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        @unless($student)
            <section class="rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4 text-sm font-semibold leading-6 text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                Data siswa belum ditemukan. Hubungi admin agar beranda dapat menampilkan data presensi.
            </section>
        @endunless

        <section class="space-y-4">
            <div class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                Aksi Utama
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <a href="{{ route('siswa.face-registration') }}" class="hk-card group p-5 transition hover:-translate-y-0.5 hover:shadow-glow sm:p-6">
                    <div class="flex min-h-32 items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                Face ID
                            </div>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white">
                                Registrasi Wajah
                            </h2>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-500 dark:text-slate-400">
                                {{ $descriptorCount }}/{{ $minimumDescriptors }} descriptor minimal. Batas maksimal {{ $maxDescriptors }} descriptor.
                            </p>
                        </div>
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-500/20 dark:text-blue-300">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 1 1 8 0m-10 9a6 6 0 0 1 12 0M4 4h4m8 0h4M4 20h4m8 0h4" />
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('siswa.leave-request') }}" class="hk-card group p-5 transition hover:-translate-y-0.5 hover:shadow-glow sm:p-6">
                    <div class="flex min-h-32 items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">
                                Pengajuan
                            </div>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white">
                                Izin/Sakit
                            </h2>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-500 dark:text-slate-400">
                                Kirim pengajuan saat tidak bisa mengikuti presensi wajah.
                            </p>
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
                    <div class="text-sm font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300">
                        Pengajuan Aktif
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($activeRequests as $request)
                        <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                    {{ $statusLabel($request->status) }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $request->attendance_date?->translatedFormat('d F Y') }}
                                </div>
                            </div>

                            <span class="hk-badge shrink-0 {{ $approvalBadge($request->approval_status) }}">
                                {{ $approvalLabel($request->approval_status) }}
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

            <div class="hk-card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200/70 px-5 py-4 dark:border-slate-800 sm:px-6">
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Riwayat Presensi
                    </div>
                    <a href="{{ route('siswa.attendance.report') }}" class="text-xs font-extrabold text-blue-600 transition hover:text-blue-700 dark:text-blue-300">
                        Lihat Rekap
                    </a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentAttendances as $attendance)
                        <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                    {{ $attendance->attendance_date?->translatedFormat('d F Y') }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ substr((string) $attendance->attendance_time, 0, 5) }} &middot; {{ $approvalLabel($attendance->approval_status) }}
                                </div>
                            </div>

                            <span class="hk-badge shrink-0 {{ $statusBadge($attendance->status) }}">
                                {{ $statusLabel($attendance->status) }}
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
                                Belum ada riwayat presensi.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
