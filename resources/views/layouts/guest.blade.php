@php
    $guestSettings = \App\Models\AttendanceSetting::current();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HadirKu') }}</title>

    @if($guestSettings->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $guestSettings->favicon_path) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @livewireScriptConfig

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-slate-50 to-emerald-50 font-sans text-slate-900 antialiased dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

    <main class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        @if(request()->routeIs('login'))
            <div class="mx-auto flex min-h-[calc(100vh-3rem)] max-w-6xl items-center">
                <section class="grid w-full overflow-hidden rounded-[2rem] border border-white/80 bg-white/75 p-3 shadow-soft backdrop-blur-xl dark:border-slate-700/70 dark:bg-slate-900/75 lg:grid-cols-[1.05fr_0.95fr] lg:gap-6 lg:p-4">
                    <div class="hidden min-h-[560px] flex-col justify-between p-8 lg:flex">
                        <div>
                            <div class="inline-flex items-center rounded-2xl border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-extrabold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                                SDN 01 Jatipurwo
                            </div>

                            <h1 class="mt-8 max-w-xl text-5xl font-extrabold leading-tight tracking-tight text-slate-950 dark:text-white">
                                HadirKu
                            </h1>

                            <p class="mt-4 max-w-lg text-lg font-semibold leading-8 text-slate-600 dark:text-slate-300">
                                Sistem presensi siswa berbasis pengenalan wajah untuk membantu kegiatan presensi harian di sekolah.
                            </p>
                        </div>
                    </div>

                    <div class="mx-auto w-full max-w-md lg:ml-auto lg:py-4 lg:pr-4">
                        <div class="rounded-[2rem] border border-white/90 bg-white/90 p-6 shadow-soft backdrop-blur-xl dark:border-slate-700/70 dark:bg-slate-900/90 sm:p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </section>
            </div>
        @else
            <div class="mx-auto grid min-h-[calc(100vh-3rem)] max-w-6xl items-center gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="hidden lg:block">
                    <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 p-8 shadow-soft backdrop-blur-xl dark:border-slate-700/70 dark:bg-slate-900/80">
                        <div class="flex min-h-[520px] flex-col justify-between">
                            <div>
                                <div class="inline-flex items-center rounded-2xl border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-extrabold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                                    SDN 01 Jatipurwo
                                </div>

                                <h1 class="mt-8 max-w-xl text-5xl font-extrabold leading-tight tracking-tight text-slate-950 dark:text-white">
                                    HadirKu
                                </h1>

                                <p class="mt-4 max-w-lg text-lg font-semibold leading-8 text-slate-600 dark:text-slate-300">
                                    Sistem presensi siswa berbasis pengenalan wajah untuk membantu kegiatan presensi harian di sekolah.
                                </p>
                            </div>

                            <div></div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto w-full max-w-md">
                    <div class="mb-6 text-center lg:hidden">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-glow">
                            <x-app-logo-mark
                                :logo-path="$guestSettings->logo_path"
                                class="h-10 w-10"
                            />
                        </div>
                        <h1 class="mt-4 text-3xl font-extrabold text-slate-900 dark:text-white">
                            HadirKu
                        </h1>
                        <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                            SDN 01 Jatipurwo
                        </p>
                    </div>

                    <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-soft backdrop-blur-xl dark:border-slate-700/70 dark:bg-slate-900/80 sm:p-8">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        @endif
    </main>

</body>
</html>
