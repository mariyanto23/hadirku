<div>

    <div class="hk-page max-w-6xl">

        <section>
            <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                Konfigurasi Sistem
            </div>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:text-3xl">
                Pengaturan Presensi
            </h1>
        </section>

        <section
            class="hk-card p-5 sm:p-6"
            x-data="{
                logoPreview: null,
                logoName: '',
                removeLogo: false,
                faviconPreview: null,
                faviconName: '',
                removeFavicon: false,
                setLogoPreview(event) {
                    const file = event.target.files[0];

                    if (!file) {
                        this.logoPreview = null;
                        this.logoName = '';
                        return;
                    }

                    this.removeLogo = false;
                    this.logoName = file.name;
                    this.logoPreview = URL.createObjectURL(file);
                },
                setFaviconPreview(event) {
                    const file = event.target.files[0];

                    if (!file) {
                        this.faviconPreview = null;
                        this.faviconName = '';
                        return;
                    }

                    this.removeFavicon = false;
                    this.faviconName = file.name;
                    this.faviconPreview = URL.createObjectURL(file);
                }
            }"
        >
            <form
                method="POST"
                action="{{ route('admin.app-logo.update') }}"
                enctype="multipart/form-data"
                class="space-y-5"
            >
                @csrf

                <div>
                    <div class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Identitas Aplikasi
                    </div>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white">
                        Logo Aplikasi
                    </h2>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/35">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 text-white shadow-glow"
                                style="width: 5rem; height: 5rem;"
                            >
                                <template x-if="logoPreview">
                                    <img
                                        :src="logoPreview"
                                        alt="Pratinjau logo aplikasi"
                                        class="h-full w-full object-contain p-2"
                                        style="display: block; width: 100%; height: 100%; object-fit: contain;"
                                    >
                                </template>

                                @if($existingLogo)
                                    <img
                                        src="{{ asset('storage/' . $existingLogo) }}"
                                        alt="Logo aplikasi"
                                        class="h-full w-full object-contain p-2"
                                        x-show="!logoPreview && !removeLogo"
                                        style="display: block; width: 100%; height: 100%; object-fit: contain;"
                                    >
                                @endif

                                <svg
                                    x-show="!logoPreview && {{ $existingLogo ? 'removeLogo' : 'true' }}"
                                    class="h-10 w-10"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v10H4V6Zm2 14h12M9 16v4m6-4v4M8 10h8M9 13h6" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                                    Logo Aplikasi
                                </h3>
                                <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.
                                </p>
                                <div
                                    x-show="logoName"
                                    x-cloak
                                    class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-300"
                                >
                                    Pratinjau: <span x-text="logoName"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:items-end">
                            <label class="hk-btn-secondary cursor-pointer">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                </svg>
                                Pilih Logo
                                <input
                                    type="file"
                                    name="logo"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    x-on:change="setLogoPreview"
                                    class="sr-only"
                                >
                            </label>

                            @if($existingLogo)
                                <label class="hk-btn-secondary cursor-pointer text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-300 dark:hover:bg-rose-950/40">
                                    <input
                                        type="checkbox"
                                        name="remove_logo"
                                        value="1"
                                        x-model="removeLogo"
                                        class="sr-only"
                                    >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                    </svg>
                                    <span x-text="removeLogo ? 'Logo akan dihapus' : 'Hapus Logo'"></span>
                                </label>
                            @endif
                        </div>
                    </div>

                    @error('logo')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/35">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white text-blue-600 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-blue-300">
                                <template x-if="faviconPreview">
                                    <img
                                        :src="faviconPreview"
                                        alt="Pratinjau favicon"
                                        class="h-full w-full object-contain p-4"
                                    >
                                </template>

                                @if($existingFavicon)
                                    <img
                                        src="{{ asset('storage/' . $existingFavicon) }}"
                                        alt="Favicon aplikasi"
                                        class="h-full w-full object-contain p-4"
                                        x-show="!faviconPreview && !removeFavicon"
                                    >
                                @endif

                                <svg
                                    x-show="!faviconPreview && {{ $existingFavicon ? 'removeFavicon' : 'true' }}"
                                    class="h-9 w-9"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.7 5.47 6.03.88-4.36 4.25 1.03 6-5.4-2.84-5.4 2.84 1.03-6-4.36-4.25 6.03-.88L12 3Z" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                                    Favicon
                                </h3>
                                <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    Format ICO, JPG, PNG, atau WEBP. Ukuran maksimal 1 MB.
                                </p>
                                <div
                                    x-show="faviconName"
                                    x-cloak
                                    class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-300"
                                >
                                    Pratinjau favicon: <span x-text="faviconName"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:items-end">
                            <label class="hk-btn-secondary cursor-pointer">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.7 5.47 6.03.88-4.36 4.25 1.03 6-5.4-2.84-5.4 2.84 1.03-6-4.36-4.25 6.03-.88L12 3Z" />
                                </svg>
                                Pilih Favicon
                                <input
                                    type="file"
                                    name="favicon"
                                    accept=".ico,.jpg,.jpeg,.png,.webp"
                                    x-on:change="setFaviconPreview"
                                    class="sr-only"
                                >
                            </label>

                            @if($existingFavicon)
                                <label class="hk-btn-secondary cursor-pointer text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-300 dark:hover:bg-rose-950/40">
                                    <input
                                        type="checkbox"
                                        name="remove_favicon"
                                        value="1"
                                        x-model="removeFavicon"
                                        class="sr-only"
                                    >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                    </svg>
                                    <span x-text="removeFavicon ? 'Favicon akan dihapus' : 'Hapus Favicon'"></span>
                                </label>
                            @endif
                        </div>
                    </div>

                    @error('favicon')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        @if(session('status') === 'identity-updated')
                            <div class="text-xs font-bold text-emerald-600 dark:text-emerald-300">
                                Identitas aplikasi berhasil disimpan.
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="hk-btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" />
                        </svg>
                        Simpan Identitas
                    </button>
                </div>
            </form>
        </section>

        <form wire:submit="save" class="space-y-4">
            <section class="hk-card p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Pengenalan Wajah
                        </div>
                        <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white">
                            Akurasi dan Performa Pemindaian
                        </h2>
                    </div>

                    <div class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                        Nilai disarankan: 0,50 dan 1000 ms
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <label class="hk-label">
                            Ambang Kecocokan Wajah
                        </label>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950/45">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                    Ketat
                                </span>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-extrabold text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                                    {{ number_format((float) $face_match_threshold, 2, ',', '.') }}
                                </span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                    Longgar
                                </span>
                            </div>

                            <input
                                type="range"
                                min="0.3"
                                max="0.8"
                                step="0.01"
                                wire:model.live="face_match_threshold"
                                class="mt-4 w-full accent-blue-600"
                                aria-label="Ambang kecocokan wajah"
                            >

                            <div class="mt-3 grid grid-cols-3 text-[10px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                <span>Lebih ketat</span>
                                <span class="text-center">Seimbang</span>
                                <span class="text-right">Lebih toleran</span>
                            </div>
                        </div>

                        @error('face_match_threshold')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror

                        <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Nilai rendah lebih ketat, nilai tinggi lebih toleran terhadap variasi wajah.
                        </p>
                    </div>

                    <div>
                        <label class="hk-label">
                            Nilai Ambang
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0.3"
                            max="0.8"
                            wire:model.live="face_match_threshold"
                            class="hk-input"
                        >
                    </div>

                    <div>
                        <label class="hk-label">
                            Interval Pemindaian (ms)
                        </label>

                        <input
                            type="number"
                            min="500"
                            max="5000"
                            wire:model.live="scan_interval"
                            class="hk-input"
                        >

                        @error('scan_interval')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror

                        <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Interval lebih besar lebih ringan untuk perangkat Android.
                        </p>
                    </div>

                    <div>
                        <label class="hk-label">
                            Batas Data Wajah
                        </label>

                        <input
                            type="number"
                            min="3"
                            max="10"
                            wire:model.live="max_descriptors"
                            class="hk-input"
                        >

                        @error('max_descriptors')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror

                        <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Data lama otomatis dihapus saat batas terlewati.
                        </p>
                    </div>
                </div>
            </section>

            <section class="hk-card p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-sm font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Aturan Presensi
                        </div>
                        <h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white">
                            Jadwal dan Alpa Otomatis
                        </h2>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="hk-label">
                            Jam Mulai Presensi
                        </label>

                        <input
                            type="time"
                            wire:model.live="attendance_start_time"
                            class="hk-input"
                        >

                        @error('attendance_start_time')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">
                            Batas Terlambat
                        </label>

                        <input
                            type="time"
                            wire:model.live="late_after"
                            class="hk-input"
                        >

                        @error('late_after')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror

                        <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Batas terlambat harus sama dengan atau setelah jam mulai presensi.
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="hk-label">
                            Hari Sekolah
                        </label>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950/45">
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
                                @foreach($schoolDayOptions as $dayValue => $dayLabel)
                                    <label class="flex min-h-12 cursor-pointer items-center justify-between gap-2 rounded-2xl border px-3 py-2 transition {{ in_array($dayValue, array_map('intval', $school_days), true) ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/15 dark:text-blue-300' : 'border-slate-200 bg-white text-slate-500 hover:border-blue-200 hover:bg-blue-50/50 dark:border-slate-800 dark:bg-slate-950/35 dark:text-slate-400 dark:hover:border-blue-500/30 dark:hover:bg-blue-500/10' }}">
                                        <span class="text-sm font-extrabold">
                                            {{ $dayLabel }}
                                        </span>
                                        <input
                                            type="checkbox"
                                            wire:model.live="school_days"
                                            value="{{ $dayValue }}"
                                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900"
                                        >
                                    </label>
                                @endforeach
                            </div>

                            <p class="mt-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Hari di luar pilihan ini tidak membuka presensi dan tidak dibuat alpa otomatis, kecuali Kalender Akademik mengatur tanggal tersebut sebagai Presensi Buka.
                            </p>
                        </div>

                        @error('school_days')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                        @error('school_days.*')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="hk-label">
                            Alpa Otomatis
                        </label>

                        <label class="flex min-h-14 cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50 dark:border-slate-800 dark:bg-slate-950/45 dark:hover:border-blue-500/40 dark:hover:bg-blue-500/10">
                            <span>
                                <span class="block text-sm font-extrabold text-slate-800 dark:text-slate-100">
                                    Aktifkan alpa otomatis
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    Sistem dapat menandai siswa alpa jika tidak ada presensi sampai batas yang ditentukan.
                                </span>
                            </span>

                            <input
                                type="checkbox"
                                wire:model.live="auto_alpha"
                                class="peer sr-only"
                            >

                            <span class="relative h-7 w-12 shrink-0 rounded-full bg-slate-300 transition after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-5 dark:bg-slate-700"></span>
                        </label>
                    </div>

                    <div class="md:col-span-2 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 dark:border-blue-500/20 dark:bg-blue-500/10">
                        <div class="text-[10px] font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Pratinjau Aturan
                        </div>
                        <p class="mt-1 text-sm font-semibold leading-6 text-slate-700 dark:text-slate-200">
                            Presensi mulai pukul
                            <span class="font-extrabold">
                                {{ str_replace(':', '.', substr((string) $attendance_start_time, 0, 5)) }}
                            </span>.
                            Siswa setelah pukul
                            <span class="font-extrabold">
                                {{ str_replace(':', '.', substr((string) $late_after, 0, 5)) }}
                            </span>
                            dihitung terlambat.
                            Alpa otomatis
                            <span class="font-extrabold">
                                {{ $auto_alpha ? 'aktif' : 'tidak aktif' }}
                            </span>.
                            Hari sekolah:
                            <span class="font-extrabold">
                                {{ collect($schoolDayOptions)->only(array_map('intval', $school_days))->implode(', ') }}
                            </span>.
                        </p>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="hk-btn-primary w-full sm:w-auto"
                >
                    <svg wire:loading.remove wire:target="save" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" />
                    </svg>

                    <span wire:loading.remove wire:target="save">
                        Simpan Aturan Presensi
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>

    </div>

</div>
