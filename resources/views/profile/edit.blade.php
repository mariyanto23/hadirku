<x-app-layout>
    <div class="hk-page max-w-5xl space-y-6">
        @php
            $roleName = $user->roles->pluck('name')->first() ?? 'pengguna';
            $roleLabel = match ($roleName) {
                'admin' => 'Administrator',
                'guru' => 'Guru',
                'siswa' => 'Siswa',
                default => ucfirst($roleName),
            };
            $backRoute = match ($roleName) {
                'admin' => route('admin.dashboard'),
                'guru' => route('guru.dashboard'),
                'siswa' => route('siswa.dashboard'),
                default => route('dashboard'),
            };
        @endphp

        <section class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                    Profil {{ $roleLabel }}
                </p>
                <h1 class="mt-1 text-2xl font-black text-slate-950 dark:text-white sm:text-3xl">
                    Ubah Profil
                </h1>
            </div>

            <a href="{{ $backRoute }}"
               class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700 active:scale-[0.98] dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:border-blue-500/40 sm:px-5">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6-6m-6 6 6 6" />
                </svg>
                <span class="hidden sm:inline">Kembali</span>
            </a>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="hk-card overflow-hidden">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="hk-card overflow-hidden">
                @include('profile.partials.update-password-form')
            </div>
        </section>
    </div>
</x-app-layout>
