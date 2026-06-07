@php
    $settings = \App\Models\AttendanceSetting::current();
    $navItems = [];
    $isGuru = auth()->user()->hasRole('guru');
    $isSiswa = auth()->user()->hasRole('siswa');
    $usesMobileBottomNav = $isGuru || $isSiswa;

    if (auth()->user()->hasRole('admin')) {
        $navItems = [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
                'icon' => 'dashboard',
            ],
            [
                'label' => 'Presensi',
                'route' => 'admin.face.attendance',
                'active' => 'admin.face.attendance',
                'icon' => 'scan',
            ],
            [
                'label' => 'Kelola Kelas',
                'route' => 'admin.classes',
                'active' => 'admin.classes',
                'icon' => 'classes',
            ],
            [
                'label' => 'Kelola Guru',
                'route' => 'admin.gurus',
                'active' => 'admin.gurus',
                'icon' => 'guru',
            ],
            [
                'label' => 'Kelola Siswa',
                'route' => 'admin.students',
                'active' => 'admin.students',
                'icon' => 'students',
            ],
            [
                'label' => 'Registrasi Wajah',
                'route' => 'admin.face-registration',
                'active' => 'admin.face-registration',
                'icon' => 'face',
            ],
            [
                'label' => 'Izin/Sakit',
                'route' => 'admin.manual.attendance',
                'active' => 'admin.manual.attendance',
                'icon' => 'manual',
            ],
            [
                'label' => 'Rekap',
                'route' => 'admin.attendance.report',
                'active' => 'admin.attendance.report',
                'icon' => 'report',
            ],
            [
                'label' => 'Kalender Akademik',
                'route' => 'admin.academic-calendar',
                'active' => 'admin.academic-calendar',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Pengaturan',
                'route' => 'admin.attendance.settings',
                'active' => 'admin.attendance.settings',
                'icon' => 'settings',
            ],
        ];
    }

    if (auth()->user()->hasRole('guru')) {
        $navItems = [
            [
                'label' => 'Beranda',
                'route' => 'guru.dashboard',
                'active' => 'guru.dashboard',
                'icon' => 'dashboard',
            ],
            [
                'label' => 'Presensi',
                'route' => 'guru.face.attendance',
                'active' => 'guru.face.attendance',
                'icon' => 'scan',
            ],
            [
                'label' => 'Registrasi Wajah',
                'route' => 'guru.face-registration',
                'active' => 'guru.face-registration',
                'icon' => 'face',
            ],
            [
                'label' => 'Izin/Sakit',
                'route' => 'guru.manual.attendance',
                'active' => 'guru.manual.attendance',
                'icon' => 'manual',
            ],
            [
                'label' => 'Rekap',
                'route' => 'guru.attendance.report',
                'active' => 'guru.attendance.report',
                'icon' => 'report',
            ],
        ];
    }

    if (auth()->user()->hasRole('siswa')) {
        $navItems = [
            [
                'label' => 'Beranda',
                'route' => 'siswa.dashboard',
                'active' => 'siswa.dashboard',
                'icon' => 'dashboard',
            ],
            [
                'label' => 'Registrasi Wajah',
                'route' => 'siswa.face-registration',
                'active' => 'siswa.face-registration',
                'icon' => 'face',
            ],
            [
                'label' => 'Izin/Sakit',
                'route' => 'siswa.leave-request',
                'active' => 'siswa.leave-request',
                'icon' => 'manual',
            ],
            [
                'label' => 'Rekap',
                'route' => 'siswa.attendance.report',
                'active' => 'siswa.attendance.report',
                'icon' => 'report',
            ],
        ];
    }
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 {{ $usesMobileBottomNav ? 'hidden md:flex' : 'flex' }} w-72 max-w-[84vw] -translate-x-full flex-col border-r border-white/70 bg-white/80 shadow-2xl backdrop-blur-2xl transition-all duration-300 dark:border-slate-700/70 dark:bg-slate-900/90 md:sticky md:top-0 md:h-screen md:translate-x-0 md:shadow-none"
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'md:w-20': sidebarCollapsed,
        'md:w-72': !sidebarCollapsed
    }"
>

    <div class="flex items-center justify-between px-5 py-5 transition-all duration-300"
         :class="sidebarCollapsed ? 'md:justify-center md:px-3' : ''">

        <a href="{{ route($navItems[0]['route'] ?? 'login') }}"
           class="group flex items-center gap-3"
           :class="sidebarCollapsed ? 'md:justify-center' : ''">
            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-glow">
                <x-app-logo-mark
                    :logo-path="$settings->logo_path"
                    class="h-8 w-8"
                />
            </div>

            <div class="transition"
                 :class="sidebarCollapsed ? 'md:hidden' : ''">
                <div class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    HadirKu
                </div>

                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    SDN 01 Jatipurwo
                </div>
            </div>
        </a>

        <button
            type="button"
            class="flex h-11 w-11 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white md:hidden"
            @click="sidebarOpen = false"
            aria-label="Tutup menu"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>

    </div>

    <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-5 transition-all duration-300"
         :class="sidebarCollapsed ? 'md:px-3' : 'md:px-4'">

        @foreach($navItems as $item)
            @php
                $active = request()->routeIs($item['active']);
            @endphp

            <a href="{{ route($item['route']) }}"
               class="group flex min-h-12 items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition
               {{ $active
                    ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow'
                    : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-soft dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}"
               :class="sidebarCollapsed ? 'md:justify-center md:px-3' : ''"
               title="{{ $item['label'] }}"
               @click="sidebarOpen = false">

                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                    {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-600 dark:bg-slate-800 dark:text-slate-300' }}">

                    @if($item['icon'] === 'dashboard')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" />
                        </svg>
                    @elseif($item['icon'] === 'classes')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12M7 9h10M7 13h6M5 19h14" />
                        </svg>
                    @elseif($item['icon'] === 'students')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0m8 0a4 4 0 1 1-8 0m8 0v1a4 4 0 0 1-8 0v-1M4 20a8 8 0 0 1 16 0" />
                        </svg>
                    @elseif($item['icon'] === 'guru')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0M20 7l-3 3-1.5-1.5" />
                        </svg>
                    @elseif($item['icon'] === 'settings')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.05.05a2 2 0 0 1-2.83 2.83l-.05-.05A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 0 1-4 0v-.08a1.7 1.7 0 0 0-.4-1.1 1.7 1.7 0 0 0-1-.6 1.7 1.7 0 0 0-1.88.34l-.05.05a2 2 0 0 1-2.83-2.83l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 0 1 0-4h.08a1.7 1.7 0 0 0 1.1-.4 1.7 1.7 0 0 0 .6-1 1.7 1.7 0 0 0-.34-1.88l-.05-.05a2 2 0 1 1 2.83-2.83l.05.05A1.7 1.7 0 0 0 9 4.6c.38-.1.72-.3 1-.6.25-.31.4-.7.4-1.1V3a2 2 0 0 1 4 0v.08c0 .4.15.79.4 1.1.28.3.62.5 1 .6a1.7 1.7 0 0 0 1.88-.34l.05-.05a2 2 0 1 1 2.83 2.83l-.05.05A1.7 1.7 0 0 0 19.4 9c.1.38.3.72.6 1 .31.25.7.4 1.1.4H21a2 2 0 0 1 0 4h-.08c-.4 0-.79.15-1.1.4-.3.28-.5.62-.6 1Z" />
                        </svg>
                    @elseif($item['icon'] === 'report')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                        </svg>
                    @elseif($item['icon'] === 'calendar')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v4M16 2v4M3 10h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                        </svg>
                    @elseif($item['icon'] === 'manual')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" />
                        </svg>
                    @elseif($item['icon'] === 'scan')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3H5a2 2 0 0 0-2 2v2m14-4h2a2 2 0 0 1 2 2v2M7 21H5a2 2 0 0 1-2-2v-2m18 0v2a2 2 0 0 1-2 2h-2M8 11a4 4 0 0 1 8 0m-9 6a5 5 0 0 1 10 0" />
                        </svg>
                    @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 1 1 8 0m-10 9a6 6 0 0 1 12 0M4 4h4m8 0h4M4 20h4m8 0h4" />
                        </svg>
                    @endif
                </span>

                <span class="truncate transition"
                      :class="sidebarCollapsed ? 'md:hidden' : ''">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

    </nav>

    <div class="p-4 transition-all duration-300"
         :class="sidebarCollapsed ? 'md:p-3' : ''">
        <button
            type="button"
            class="hidden min-h-11 w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white/75 px-3 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200 dark:hover:bg-slate-800 md:flex"
            @click="sidebarCollapsed = !sidebarCollapsed"
            :title="sidebarCollapsed ? 'Tampilkan menu lengkap' : 'Sembunyikan label menu'"
        >
            <svg
                x-show="!sidebarCollapsed"
                x-cloak
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6 9 12l6 6M20 4v16" />
            </svg>

            <svg
                x-show="sidebarCollapsed"
                x-cloak
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6M4 4v16" />
            </svg>

            <span :class="sidebarCollapsed ? 'md:hidden' : ''">
                Sembunyikan Menu
            </span>
        </button>
    </div>

</aside>

@if($usesMobileBottomNav)
    @php
        $bottomNavItems = $isGuru
            ? [
                [
                    'label' => 'Beranda',
                    'route' => 'guru.dashboard',
                    'active' => 'guru.dashboard',
                    'icon' => 'home',
                ],
                [
                    'label' => 'Izin',
                    'route' => 'guru.manual.attendance',
                    'active' => 'guru.manual.attendance',
                    'icon' => 'manual',
                ],
                [
                    'label' => 'Presensi',
                    'route' => 'guru.face.attendance',
                    'active' => 'guru.face.attendance',
                    'icon' => 'scan',
                    'primary' => true,
                ],
                [
                    'label' => 'Face ID',
                    'route' => 'guru.face-registration',
                    'active' => 'guru.face-registration',
                    'icon' => 'face',
                ],
                [
                    'label' => 'Rekap',
                    'route' => 'guru.attendance.report',
                    'active' => 'guru.attendance.report',
                    'icon' => 'report',
                ],
            ]
            : [
                [
                    'label' => 'Beranda',
                    'route' => 'siswa.dashboard',
                    'active' => 'siswa.dashboard',
                    'icon' => 'home',
                ],
                [
                    'label' => 'Izin',
                    'route' => 'siswa.leave-request',
                    'active' => 'siswa.leave-request',
                    'icon' => 'manual',
                ],
                [
                    'label' => 'Face ID',
                    'route' => 'siswa.face-registration',
                    'active' => 'siswa.face-registration',
                    'icon' => 'face',
                ],
                [
                    'label' => 'Rekap',
                    'route' => 'siswa.attendance.report',
                    'active' => 'siswa.attendance.report',
                    'icon' => 'report',
                ],
                [
                    'label' => 'Profil',
                    'route' => 'profile.edit',
                    'active' => 'profile.edit',
                    'icon' => 'profile',
                ],
            ];
    @endphp

    <nav
        class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200/80 bg-white/95 px-3 pt-2 shadow-[0_-16px_40px_rgba(15,23,42,0.12)] backdrop-blur-2xl dark:border-slate-800/80 dark:bg-slate-950/95 md:hidden"
        style="padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));"
        aria-label="Navigasi utama {{ $isGuru ? 'guru' : 'siswa' }}"
    >
        <div class="mx-auto grid max-w-md grid-cols-5 items-end gap-1">
            @foreach($bottomNavItems as $item)
                @php
                    $active = request()->routeIs($item['active']);
                    $primary = $item['primary'] ?? false;
                @endphp

                <a
                    href="{{ route($item['route']) }}"
                    class="group flex min-w-0 flex-col items-center justify-end gap-1 text-center text-[10px] font-extrabold leading-none transition {{ $primary ? '-mt-8' : '' }} {{ $active ? 'text-blue-700 dark:text-blue-300' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-300' }}"
                    aria-label="{{ $item['label'] }}"
                >
                    <span
                        class="flex shrink-0 items-center justify-center transition {{ $primary
                            ? 'h-14 w-14 rounded-full border-4 border-white bg-slate-900 text-white shadow-2xl shadow-slate-900/30 dark:border-slate-950 dark:bg-blue-600'
                            : 'h-8 w-8 rounded-2xl '.($active ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' : 'text-slate-500 group-hover:bg-blue-50 group-hover:text-blue-600 dark:text-slate-400 dark:group-hover:bg-blue-500/10 dark:group-hover:text-blue-300') }}"
                    >
                        @if($item['icon'] === 'home')
                            <svg class="{{ $primary ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 11 9-8 9 8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v10h5v-6h4v6h5V10" />
                            </svg>
                        @elseif($item['icon'] === 'manual')
                            <svg class="{{ $primary ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h9l5 5v13H6V3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v6h6M9 13h6M9 17h4" />
                            </svg>
                        @elseif($item['icon'] === 'scan')
                            <svg class="{{ $primary ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 3H5a2 2 0 0 0-2 2v2m14-4h2a2 2 0 0 1 2 2v2M7 21H5a2 2 0 0 1-2-2v-2m18 0v2a2 2 0 0 1-2 2h-2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 0 1 8 0m-9 6a5 5 0 0 1 10 0" />
                            </svg>
                        @elseif($item['icon'] === 'face')
                            <svg class="{{ $primary ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a4 4 0 1 1 8 0m-10 9a6 6 0 0 1 12 0" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4m8 0h4M4 20h4m8 0h4" />
                            </svg>
                        @elseif($item['icon'] === 'report')
                            <svg class="{{ $primary ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                            </svg>
                        @else
                            <svg class="{{ $primary ? 'h-6 w-6' : 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0 1 16 0" />
                            </svg>
                        @endif
                    </span>

                    <span class="{{ $primary ? 'rounded-full bg-slate-900 px-2 py-1 text-white shadow-lg shadow-slate-900/20 dark:bg-blue-600' : '' }}">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </nav>
@endif
