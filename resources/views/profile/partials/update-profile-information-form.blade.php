<section>
    <div class="border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:px-6">
        <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
            Akun
        </div>
        <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
            Informasi Profil
        </h2>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="space-y-5 p-5 sm:p-6"
        x-data="{
            submitting: false,
            photoPreview: null,
            photoName: '',
            removePhoto: false,
            previewPhoto(event) {
                const file = event.target.files?.[0];

                if (!file) {
                    this.resetPhotoChoice();
                    return;
                }

                if (this.photoPreview) {
                    URL.revokeObjectURL(this.photoPreview);
                }

                this.removePhoto = false;
                this.photoName = file.name;
                this.photoPreview = URL.createObjectURL(file);
            },
            resetPhotoChoice() {
                if (this.photoPreview) {
                    URL.revokeObjectURL(this.photoPreview);
                }

                this.photoPreview = null;
                this.photoName = '';

                if (this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }
            },
            markPhotoForRemoval() {
                this.resetPhotoChoice();
                this.removePhoto = true;
            },
            undoPhotoRemoval() {
                this.removePhoto = false;
            }
        }"
        x-on:submit="submitting = true"
    >
        @csrf
        @method('patch')

        <div>
            <label class="hk-label">Foto profil</label>
            <input type="hidden" name="remove_photo" x-bind:value="removePhoto ? '1' : '0'">

            <div class="rounded-3xl border border-slate-200/70 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-900/40">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        <div
                            class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 to-emerald-500 text-2xl font-extrabold text-white shadow-glow"
                            style="width: 6rem; height: 6rem;"
                        >
                            <template x-if="photoPreview">
                                <img
                                    x-bind:src="photoPreview"
                                    alt="Pratinjau foto profil"
                                    class="h-full w-full object-cover"
                                    style="display: block; width: 100%; height: 100%; object-fit: cover;"
                                >
                            </template>

                            @if($user->photo)
                                <img
                                    x-show="!photoPreview && !removePhoto"
                                    src="{{ asset('storage/' . $user->photo) }}"
                                    alt="Foto profil {{ $user->name }}"
                                    class="h-full w-full object-cover"
                                    style="display: block; width: 100%; height: 100%; object-fit: cover;"
                                >
                            @endif

                            <span x-show="!photoPreview && {{ $user->photo ? 'removePhoto' : 'true' }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                {{ $user->name }}
                            </div>
                            <p class="mt-1 text-xs font-semibold leading-5 text-slate-500 dark:text-slate-400">
                                Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.
                            </p>
                            <p
                                x-show="photoName"
                                x-text="photoName"
                                class="mt-2 max-w-56 truncate text-xs font-bold text-blue-600 dark:text-blue-300"
                            >
                            </p>
                            <p
                                x-show="removePhoto"
                                class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-300"
                            >
                                Foto akan dihapus saat perubahan disimpan.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:items-end">
                        <label
                            class="hk-btn-secondary cursor-pointer"
                            x-bind:class="{ 'pointer-events-none opacity-60': submitting }"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                            </svg>
                            Pilih Foto
                            <input
                                type="file"
                                name="photo"
                                accept="image/jpeg,image/png,image/webp"
                                class="sr-only"
                                x-on:change="previewPhoto($event)"
                                x-ref="photoInput"
                            >
                        </label>

                        <button
                            type="button"
                            x-show="photoPreview"
                            x-on:click="resetPhotoChoice()"
                            x-bind:disabled="submitting"
                            class="text-sm font-bold text-slate-500 transition hover:text-blue-600 disabled:cursor-not-allowed disabled:opacity-60 dark:text-slate-400 dark:hover:text-blue-300"
                        >
                            Batalkan Pilihan
                        </button>

                        @if($user->photo)
                            <button
                                type="button"
                                x-show="!photoPreview && !removePhoto"
                                x-on:click="markPhotoForRemoval()"
                                x-bind:disabled="submitting"
                                class="text-sm font-bold text-rose-600 transition hover:text-rose-700 disabled:cursor-not-allowed disabled:opacity-60 dark:text-rose-300 dark:hover:text-rose-200"
                            >
                                Hapus Foto
                            </button>

                            <button
                                type="button"
                                x-show="removePhoto"
                                x-on:click="undoPhotoRemoval()"
                                x-bind:disabled="submitting"
                                class="text-sm font-bold text-slate-500 transition hover:text-blue-600 disabled:cursor-not-allowed disabled:opacity-60 dark:text-slate-400 dark:hover:text-blue-300"
                            >
                                Batal Hapus Foto
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            @error('photo')
                <div class="hk-error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="name" class="hk-label">Nama lengkap</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="hk-input"
            >
            @error('name')
                <div class="hk-error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="username" class="hk-label">Nama pengguna</label>
            <input
                id="username"
                type="text"
                value="{{ $user->username }}"
                disabled
                class="hk-input cursor-not-allowed bg-slate-100 text-slate-500 dark:bg-slate-900 dark:text-slate-400"
            >
            <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                Nama pengguna dipakai untuk masuk ke sistem dan dikelola oleh administrator.
            </p>
        </div>

        <div>
            <label for="email" class="hk-label">Alamat surel</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                autocomplete="email"
                placeholder="nama@example.com"
                class="hk-input"
            >
            @error('email')
                <div class="hk-error">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
                    Alamat surel Anda belum diverifikasi.

                    <button
                        form="send-verification"
                        class="ml-1 font-extrabold underline decoration-2 underline-offset-4"
                    >
                        Kirim ulang tautan verifikasi.
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                        Tautan verifikasi baru telah dikirim ke alamat surel Anda.
                    </div>
                @endif
            @endif
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-end">
            <button
                type="submit"
                class="hk-btn-primary disabled:cursor-not-allowed disabled:opacity-70"
                x-bind:disabled="submitting"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" />
                </svg>
                <span x-show="!submitting">Simpan Perubahan</span>
                <span x-show="submitting">Menyimpan...</span>
            </button>
        </div>
    </form>
</section>
