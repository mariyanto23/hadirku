@php
    $authUser = auth()->user();
    $profilePhotoUrl = $authUser->photo ? asset('storage/' . $authUser->photo) : null;
    $usesMobileBottomNav = $authUser->hasRole('guru') || $authUser->hasRole('siswa');
    $pageTitle = $authUser->hasRole('guru') || $authUser->hasRole('siswa')
        ? 'Beranda'
        : 'Dashboard';

    if (request()->routeIs('admin.classes')) {
        $pageTitle = 'Manajemen Kelas';
    } elseif (request()->routeIs('admin.gurus')) {
        $pageTitle = 'Manajemen Guru';
    } elseif (request()->routeIs('admin.students')) {
        $pageTitle = 'Manajemen Siswa';
    } elseif (request()->routeIs('admin.attendance.settings')) {
        $pageTitle = 'Pengaturan Presensi';
    } elseif (request()->routeIs('admin.attendance.report')) {
        $pageTitle = 'Rekap Presensi';
    } elseif (request()->routeIs('admin.academic-calendar')) {
        $pageTitle = 'Kalender Akademik';
    } elseif (request()->routeIs('guru.attendance.report')) {
        $pageTitle = 'Rekap Presensi';
    } elseif (request()->routeIs('siswa.attendance.report')) {
        $pageTitle = 'Rekap Bulan '.today()->translatedFormat('F');
    } elseif (request()->routeIs('admin.manual.attendance') || request()->routeIs('guru.manual.attendance')) {
        $pageTitle = 'Izin/Sakit';
    } elseif (request()->routeIs('admin.face.attendance') || request()->routeIs('guru.face.attendance')) {
        $pageTitle = 'Presensi Wajah';
    } elseif (request()->routeIs('admin.face-registration') || request()->routeIs('guru.face-registration') || request()->routeIs('siswa.face-registration')) {
        $pageTitle = 'Registrasi Wajah';
    } elseif (request()->routeIs('siswa.leave-request')) {
        $pageTitle = 'Pengajuan Izin/Sakit';
    } elseif (request()->routeIs('profile.edit')) {
        $pageTitle = 'Ubah Profil';
    }
@endphp

<nav class="sticky top-0 z-30 border-b border-white/60 bg-white/70 px-4 py-3 backdrop-blur-2xl dark:border-slate-800/70 dark:bg-slate-950/60 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between gap-4">

        <div class="flex min-w-0 items-center gap-3">

            <button
                type="button"
                class="{{ $usesMobileBottomNav ? 'hidden' : 'flex' }} h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 md:hidden"
                @click="sidebarOpen = true"
                aria-label="Buka menu"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>

            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                    HadirKu
                </p>
                <h2 class="truncate text-lg font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-xl">
                    {{ $pageTitle }}
                </h2>
            </div>

        </div>

        <div class="flex items-center gap-2 sm:gap-3">

            <button
                type="button"
                @click="darkMode = !darkMode"
                class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                aria-label="Ubah tema"
            >
                <svg x-show="!darkMode" class="h-5 w-5" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.06 16.94l-1.42 1.42m12.72 0-1.42-1.42M7.06 7.06 5.64 5.64M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" />
                </svg>

                <svg x-show="darkMode" class="h-5 w-5" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.5 6.5 0 0 0 9.8 9.8Z" />
                </svg>
            </button>

            <div
                x-data="{ profileOpen: false }"
                class="relative"
                @click.outside="profileOpen = false"
            >
                <button
                    type="button"
                    @click="profileOpen = !profileOpen"
                    class="flex min-h-11 min-w-12 items-center gap-3 rounded-2xl border border-slate-200 bg-white/85 px-3 py-1.5 text-left shadow-sm transition hover:bg-blue-50 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-900/80 dark:hover:bg-slate-800 sm:min-w-60 sm:px-4"
                    :aria-expanded="profileOpen.toString()"
                    aria-haspopup="menu"
                >
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-blue-600 to-emerald-500 text-xs font-extrabold text-white"
                        style="width: 2.25rem; height: 2.25rem;"
                    >
                        @if($profilePhotoUrl)
                            <img
                                src="{{ $profilePhotoUrl }}"
                                alt="Foto profil {{ $authUser->name }}"
                                class="h-full w-full rounded-xl object-cover"
                                style="display: block; width: 100%; height: 100%; object-fit: cover;"
                            >
                        @else
                            {{ strtoupper(substr($authUser->name, 0, 1)) }}
                        @endif
                    </span>

                    <span class="hidden min-w-0 flex-1 sm:block">
                        <span class="block truncate text-sm font-bold text-slate-800 dark:text-white">
                            {{ $authUser->name }}
                        </span>
                        <span class="block truncate text-xs font-medium text-slate-500 dark:text-slate-400">
                            {{ $authUser->roles->pluck('name')->first() ?? 'user' }}
                        </span>
                    </span>

                    <svg
                        class="hidden h-4 w-4 shrink-0 text-slate-400 transition sm:block"
                        :class="profileOpen ? 'rotate-180' : ''"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <div
                    x-show="profileOpen"
                    x-transition.origin.top.right
                    x-cloak
                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
                    role="menu"
                >
                    <div class="p-2">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-blue-50 hover:text-blue-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                            role="menuitem"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                            </svg>
                            Ubah Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-bold text-rose-600 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/40"
                                role="menuitem"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17l5-5-5-5M20 12H9m3 8H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>

</nav>
