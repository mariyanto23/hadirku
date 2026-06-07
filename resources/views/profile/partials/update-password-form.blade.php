<section>
    <div class="border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:px-6">
        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
            Keamanan
        </div>
        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
            Ubah Kata Sandi
        </h2>
    </div>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="space-y-5 p-5 sm:p-6"
        x-data="{
            submitting: false,
            currentPassword: '',
            password: '',
            passwordConfirmation: '',
            get hasMinLength() {
                return this.password.length >= 8;
            },
            get hasLetter() {
                return /[A-Za-z]/.test(this.password);
            },
            get hasNumber() {
                return /[0-9]/.test(this.password);
            },
            get hasLongPassword() {
                return this.password.length >= 12;
            },
            get hasSymbol() {
                return /[^A-Za-z0-9]/.test(this.password);
            },
            get confirmationMatches() {
                return this.passwordConfirmation.length > 0 && this.password === this.passwordConfirmation;
            },
            get confirmationMismatch() {
                return this.passwordConfirmation.length > 0 && this.password !== this.passwordConfirmation;
            },
            get strengthScore() {
                return [
                    this.hasMinLength,
                    this.hasLetter,
                    this.hasNumber,
                    this.hasLongPassword,
                    this.hasSymbol
                ].filter(Boolean).length;
            },
            get strengthLabel() {
                if (!this.password.length) {
                    return 'Belum diisi';
                }

                if (this.strengthScore <= 2) {
                    return 'Lemah';
                }

                if (this.strengthScore <= 4) {
                    return 'Cukup';
                }

                return 'Kuat';
            },
            get strengthColor() {
                if (!this.password.length) {
                    return 'bg-slate-200 dark:bg-slate-700';
                }

                if (this.strengthScore <= 2) {
                    return 'bg-rose-500';
                }

                if (this.strengthScore <= 4) {
                    return 'bg-amber-500';
                }

                return 'bg-emerald-500';
            },
            get canSubmit() {
                return this.currentPassword.length > 0
                    && this.hasMinLength
                    && this.hasLetter
                    && this.hasNumber
                    && this.confirmationMatches
                    && !this.submitting;
            }
        }"
        x-on:submit="submitting = true"
    >
        @csrf
        @method('put')

        <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold leading-6 text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
            Kata sandi baru minimal 8 karakter, berisi huruf dan angka, serta berbeda dari kata sandi saat ini.
        </div>

        <div>
            <label for="update_password_current_password" class="hk-label">Kata sandi saat ini</label>
            <x-password-input
                id="update_password_current_password"
                name="current_password"
                autocomplete="current-password"
                required
                x-model="currentPassword"
            />
            @if($errors->updatePassword->has('current_password'))
                <div class="hk-error">
                    {{ $errors->updatePassword->first('current_password') }}
                </div>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="hk-label">Kata sandi baru</label>
            <x-password-input
                id="update_password_password"
                name="password"
                autocomplete="new-password"
                required
                x-model="password"
            />

            <div class="mt-3 space-y-3 rounded-2xl border border-slate-200/70 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-900/40">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-extrabold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Kekuatan
                    </span>
                    <span
                        class="text-xs font-extrabold"
                        x-bind:class="{
                            'text-slate-500 dark:text-slate-400': !password.length,
                            'text-rose-600 dark:text-rose-300': password.length && strengthScore <= 2,
                            'text-amber-600 dark:text-amber-300': strengthScore > 2 && strengthScore <= 4,
                            'text-emerald-600 dark:text-emerald-300': strengthScore > 4
                        }"
                        x-text="strengthLabel"
                    ></span>
                </div>

                <div class="grid grid-cols-5 gap-1.5">
                    <template x-for="step in 5" x-bind:key="step">
                        <span
                            class="h-2 rounded-full transition"
                            x-bind:class="step <= strengthScore ? strengthColor : 'bg-slate-200 dark:bg-slate-700'"
                        ></span>
                    </template>
                </div>

                <div class="grid gap-2 text-xs font-bold sm:grid-cols-2">
                    <div
                        class="flex items-center gap-2"
                        x-bind:class="hasMinLength ? 'text-emerald-600 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400'"
                    >
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border text-[10px]"
                              x-bind:class="hasMinLength ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-950'">
                            <span x-show="hasMinLength">&#10003;</span>
                        </span>
                        Minimal 8 karakter
                    </div>

                    <div
                        class="flex items-center gap-2"
                        x-bind:class="hasLetter ? 'text-emerald-600 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400'"
                    >
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border text-[10px]"
                              x-bind:class="hasLetter ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-950'">
                            <span x-show="hasLetter">&#10003;</span>
                        </span>
                        Berisi huruf
                    </div>

                    <div
                        class="flex items-center gap-2"
                        x-bind:class="hasNumber ? 'text-emerald-600 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400'"
                    >
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border text-[10px]"
                              x-bind:class="hasNumber ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-950'">
                            <span x-show="hasNumber">&#10003;</span>
                        </span>
                        Berisi angka
                    </div>

                    <div
                        class="flex items-center gap-2"
                        x-bind:class="confirmationMatches ? 'text-emerald-600 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400'"
                    >
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border text-[10px]"
                              x-bind:class="confirmationMatches ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-950'">
                            <span x-show="confirmationMatches">&#10003;</span>
                        </span>
                        Konfirmasi sama
                    </div>
                </div>
            </div>

            @if($errors->updatePassword->has('password'))
                <div class="hk-error">
                    {{ $errors->updatePassword->first('password') }}
                </div>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="hk-label">Konfirmasi kata sandi baru</label>
            <x-password-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                required
                x-model="passwordConfirmation"
            />
            <div
                x-show="confirmationMismatch"
                x-transition
                class="mt-2 text-sm font-semibold text-rose-600 dark:text-rose-300"
            >
                Konfirmasi kata sandi belum sama.
            </div>
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="hk-error">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-end">
            <button
                type="submit"
                class="hk-btn-primary disabled:cursor-not-allowed disabled:opacity-70"
                x-bind:disabled="!canSubmit"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 17v.01M8 10V8a4 4 0 0 1 8 0v2m-9 0h10v10H7V10Z" />
                </svg>
                <span x-show="!submitting">Ubah Kata Sandi</span>
                <span x-show="submitting">Menyimpan...</span>
            </button>
        </div>
    </form>
</section>
