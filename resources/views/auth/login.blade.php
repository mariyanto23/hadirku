<x-guest-layout>

    @php
        $settings = \App\Models\AttendanceSetting::current();
    @endphp

    @php
        $loginError = $errors->first('username') ?: $errors->first('password');
    @endphp

    <div class="mb-7 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 to-emerald-500 text-white shadow-glow">
            <x-app-logo-mark
                :logo-path="$settings->logo_path"
                class="h-14 w-14"
            />
        </div>

        <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Masuk
        </h1>

        <p class="mt-2 text-sm font-semibold leading-6 text-slate-500 dark:text-slate-400">
            Masuk menggunakan NIS atau nama pengguna.
        </p>
    </div>

    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    @if($loginError)
        <div class="mb-4 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-semibold leading-6 text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
            {{ $loginError }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('login') }}"
          class="space-y-5"
          x-data="{ submitting: false }"
          x-on:submit="submitting = true">

        @csrf

        <div>
            <label for="username" class="hk-label">
                Nama Pengguna atau NIS
            </label>

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0" />
                    </svg>
                </span>

                <input
                    id="username"
                    class="hk-input pl-12"
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Masukkan nama pengguna atau NIS"
                >
            </div>

            <x-input-error
                :messages="$errors->get('username')"
                class="mt-2"
            />
        </div>

        <div>
            <label for="password" class="hk-label">
                Kata Sandi
            </label>

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6v-9Z" />
                    </svg>
                </span>

                <x-password-input
                    id="password"
                    class="pl-12"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                />
            </div>

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div class="flex items-center justify-between">
            <label
                for="remember_me"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-300"
            >
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900"
                    name="remember"
                >

                Ingat saya
            </label>
        </div>

        <button
            type="submit"
            class="hk-btn-primary w-full disabled:cursor-not-allowed disabled:opacity-70"
            x-bind:disabled="submitting"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m12-7 7 7-7 7" />
            </svg>
            <span x-show="!submitting">Masuk</span>
            <span x-show="submitting">Masuk...</span>
        </button>

    </form>

</x-guest-layout>
