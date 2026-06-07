<div
    x-data="{
        formModalOpen: @entangle('showFormModal'),
        importModalOpen: @entangle('showImportModal')
    }"
    x-on:keydown.escape.window="
        if (formModalOpen) $wire.closeFormModal();
        if (importModalOpen) $wire.closeImportModal();
    "
>
    <div class="hk-page">
        <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
            @foreach([
                [
                    'label' => 'Total Libur',
                    'value' => $summary['total'],
                    'labelClass' => 'text-blue-600 dark:text-blue-300',
                    'iconClass' => 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300',
                    'icon' => 'M8 2v4M16 2v4M3 10h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z',
                ],
                [
                    'label' => 'Berlangsung',
                    'value' => $summary['active'],
                    'labelClass' => 'text-emerald-600 dark:text-emerald-300',
                    'iconClass' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300',
                    'icon' => 'M20 6 9 17l-5-5',
                ],
                [
                    'label' => 'Presensi Tutup',
                    'value' => $summary['blocked'],
                    'labelClass' => 'text-rose-600 dark:text-rose-300',
                    'iconClass' => 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-300',
                    'icon' => 'M18 6 6 18M6 6l12 12',
                ],
                [
                    'label' => 'Presensi Buka',
                    'value' => $summary['allowed'],
                    'labelClass' => 'text-amber-600 dark:text-amber-300',
                    'iconClass' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300',
                    'icon' => 'M12 6v6l4 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                ],
            ] as $card)
                <div class="hk-card p-3 sm:p-5">
                    <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wide {{ $card['labelClass'] }} sm:text-sm">
                                {{ $card['label'] }}
                            </div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                                {{ $card['value'] }}
                            </div>
                        </div>
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl {{ $card['iconClass'] }} sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="hk-card p-5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Kalender Akademik
                    </div>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:text-3xl">
                        Kelola Hari Libur
                    </h1>
                </div>

                <div class="flex flex-col gap-3 lg:items-end">
                    <div class="grid gap-2 sm:grid-cols-2">
                        <button
                            type="button"
                            wire:click="openImportModal"
                            class="hk-btn-secondary w-full"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16" />
                            </svg>
                            Impor
                        </button>

                        <button
                            type="button"
                            wire:click="openCreateModal"
                            class="hk-btn-primary w-full"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Libur
                        </button>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:w-[42rem] lg:grid-cols-[1fr_12rem_9rem_auto]">
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Cari nama libur..."
                            class="hk-input"
                        >

                        <select wire:model.live="typeFilter" class="hk-input">
                            <option value="">Semua Jenis</option>
                            @foreach($types as $typeValue => $typeLabel)
                                <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="yearFilter" class="hk-input">
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="hk-btn-secondary"
                        >
                            Atur Ulang
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-3 md:hidden">
                @forelse($holidays as $holiday)
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/35" wire:key="holiday-mobile-{{ $holiday->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-base font-extrabold text-slate-900 dark:text-white">
                                    {{ $holiday->title }}
                                </div>
                                <div class="mt-1 text-xs font-bold text-slate-500 dark:text-slate-400">
                                    {{ $holiday->start_date->translatedFormat('d F Y') }}
                                    @if(! $holiday->start_date->isSameDay($holiday->end_date))
                                        - {{ $holiday->end_date->translatedFormat('d F Y') }}
                                    @endif
                                </div>
                            </div>

                            <span class="hk-badge {{ $holiday->allow_attendance ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' }}">
                                {{ $holiday->allow_attendance ? 'Presensi Buka' : 'Presensi Tutup' }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="hk-badge bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                {{ $holiday->type_label }}
                            </span>
                            @if($holiday->notes)
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $holiday->notes }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button
                                type="button"
                                wire:click="edit({{ $holiday->id }})"
                                class="hk-btn-secondary px-3 py-2 text-xs"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                x-on:click="
                                    confirmAction({
                                        title: 'Hapus data libur?',
                                        text: 'Data ini akan dihapus dari kalender akademik.',
                                        confirmText: 'Hapus',
                                        icon: 'warning',
                                        tone: 'danger'
                                    }).then(confirmed => {
                                        if (confirmed) $wire.delete({{ $holiday->id }});
                                    });
                                "
                                class="hk-btn-secondary px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-300 dark:hover:bg-rose-500/10"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700">
                        <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                            Belum ada data libur.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="hk-table-wrap mt-6 hidden md:block">
                <div class="overflow-x-auto">
                    <table class="hk-table min-w-[820px]">
                        <thead>
                            <tr>
                                <th>Nama Libur</th>
                                <th>Jenis</th>
                                <th>Rentang Tanggal</th>
                                <th>Status Presensi</th>
                                <th>Keterangan</th>
                                <th class="w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($holidays as $holiday)
                                <tr wire:key="holiday-row-{{ $holiday->id }}">
                                    <td>
                                        <div class="font-extrabold text-slate-900 dark:text-white">
                                            {{ $holiday->title }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="hk-badge bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                            {{ $holiday->type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-bold text-slate-700 dark:text-slate-200">
                                            {{ $holiday->start_date->translatedFormat('d F Y') }}
                                        </div>
                                        @if(! $holiday->start_date->isSameDay($holiday->end_date))
                                            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                sampai {{ $holiday->end_date->translatedFormat('d F Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="hk-badge {{ $holiday->allow_attendance ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' }}">
                                            {{ $holiday->allow_attendance ? 'Dibuka' : 'Ditutup' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="line-clamp-2 text-sm font-semibold text-slate-600 dark:text-slate-300">
                                            {{ $holiday->notes ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <button
                                                type="button"
                                                wire:click="edit({{ $holiday->id }})"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-amber-300"
                                                title="Edit libur"
                                                aria-label="Edit libur"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="
                                                    confirmAction({
                                                        title: 'Hapus data libur?',
                                                        text: 'Data ini akan dihapus dari kalender akademik.',
                                                        confirmText: 'Hapus',
                                                        icon: 'warning',
                                                        tone: 'danger'
                                                    }).then(confirmed => {
                                                        if (confirmed) $wire.delete({{ $holiday->id }});
                                                    });
                                                "
                                                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-rose-300"
                                                title="Hapus libur"
                                                aria-label="Hapus libur"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5M14 11v5" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="py-10 text-center">
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v4M16 2v4M3 10h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                                                </svg>
                                            </div>
                                            <div class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-300">
                                                Belum ada data libur.
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $holidays->links() }}
            </div>
        </section>
    </div>

    <div
        x-show="formModalOpen"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
    >
        <section
            x-on:click.outside="$wire.closeFormModal()"
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Kalender Akademik
                    </div>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        {{ $isEdit ? 'Edit data libur' : 'Tambah data libur' }}
                    </h2>
                </div>

                <button
                    type="button"
                    wire:click="closeFormModal"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Tutup formulir libur"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-5 p-5">
                <div>
                    <label class="hk-label">Nama libur</label>
                    <input
                        type="text"
                        wire:model="title"
                        placeholder="Contoh: Libur Semester Genap"
                        class="hk-input"
                    >
                    @error('title')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="hk-label">Jenis libur</label>
                        <select wire:model="type" class="hk-input">
                            @foreach($types as $typeValue => $typeLabel)
                                <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Presensi saat libur</label>
                        <select wire:model.boolean="allow_attendance" class="hk-input">
                            <option value="0">Ditutup</option>
                            <option value="1">Dibuka</option>
                        </select>
                        <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Jika ditutup, pemindaian wajah dan pengajuan izin/sakit siswa ditolak pada tanggal tersebut.
                        </div>
                        @error('allow_attendance')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="hk-label">Tanggal mulai</label>
                        <input type="date" wire:model="start_date" class="hk-input">
                        @error('start_date')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Tanggal selesai</label>
                        <input type="date" wire:model="end_date" class="hk-input">
                        @error('end_date')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="hk-label">Keterangan</label>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Catatan tambahan bila diperlukan"
                        class="hk-input resize-none"
                    ></textarea>
                    @error('notes')
                        <div class="hk-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="closeFormModal" class="hk-btn-secondary">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="hk-btn-primary">
                        <span wire:loading.remove wire:target="save">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Libur' }}
                        </span>
                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </section>
    </div>

    <div
        x-show="importModalOpen"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
    >
        <section
            x-on:click.outside="$wire.closeImportModal()"
            class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Impor Kalender
                    </div>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        Impor hari libur
                    </h2>
                </div>

                <button
                    type="button"
                    wire:click="closeImportModal"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Tutup impor hari libur"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <div class="space-y-5 p-5">
                <div class="rounded-2xl border border-blue-100 bg-blue-50/80 p-4 text-sm font-semibold leading-6 text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                    Kolom wajib: <span class="font-extrabold">nama_libur</span>, <span class="font-extrabold">jenis</span>, <span class="font-extrabold">tanggal_mulai</span>, dan <span class="font-extrabold">tanggal_selesai</span>. Kolom opsional: <span class="font-extrabold">presensi</span> dan <span class="font-extrabold">keterangan</span>.
                </div>

                <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <label class="hk-label">Berkas impor</label>
                        <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-dashed border-slate-300 bg-white/70 px-4 py-4 transition hover:border-blue-300 hover:bg-blue-50/50 dark:border-slate-700 dark:bg-slate-950/35 dark:hover:border-blue-500/50 dark:hover:bg-blue-500/10">
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-extrabold text-slate-800 dark:text-white">
                                    {{ $importFile ? $importFile->getClientOriginalName() : 'Pilih berkas CSV atau Excel' }}
                                </span>
                                <span class="mt-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    Format CSV, XLS, atau XLSX. Maksimal 2 MB.
                                </span>
                            </span>

                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16" />
                                </svg>
                            </span>

                            <input
                                type="file"
                                wire:model="importFile"
                                accept=".csv,.txt,.xlsx,.xls"
                                class="hidden"
                            >
                        </label>

                        <div wire:loading wire:target="importFile,previewImportFile" class="mt-2 text-sm font-bold text-blue-600 dark:text-blue-300">
                            Membaca berkas...
                        </div>

                        @error('importFile')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button
                        type="button"
                        wire:click="downloadImportTemplate"
                        class="hk-btn-secondary"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9m0 12 4-4m-4 4-4-4M5 3h14" />
                        </svg>
                        Unduh Template
                    </button>
                </div>

                @if($importPreviewRows)
                    <div class="grid gap-2 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-white/70 p-3 dark:border-slate-800 dark:bg-slate-950/35">
                            <div class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Baris Dibaca</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">{{ count($importPreviewRows) }}</div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                            <div class="text-xs font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Valid</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">{{ $importValidCount }}</div>
                        </div>
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-500/20 dark:bg-rose-500/10">
                            <div class="text-xs font-bold uppercase tracking-wide text-rose-700 dark:text-rose-300">Perlu Diperbaiki</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">{{ $importInvalidCount }}</div>
                        </div>
                    </div>

                    <div class="max-h-72 overflow-y-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                        <table class="hk-table min-w-[760px]">
                            <thead>
                                <tr>
                                    <th>Baris</th>
                                    <th>Nama Libur</th>
                                    <th>Jenis</th>
                                    <th>Rentang</th>
                                    <th>Presensi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importPreviewRows as $previewRow)
                                    <tr>
                                        <td>{{ $previewRow['row'] }}</td>
                                        <td>
                                            <div class="font-extrabold text-slate-900 dark:text-white">
                                                {{ $previewRow['title'] ?: '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $previewRow['type'] }}</td>
                                        <td>
                                            {{ $previewRow['start_date'] }}
                                            @if($previewRow['start_date'] !== $previewRow['end_date'])
                                                - {{ $previewRow['end_date'] }}
                                            @endif
                                        </td>
                                        <td>{{ $previewRow['allow_attendance'] }}</td>
                                        <td>
                                            @if($previewRow['valid'])
                                                <span class="hk-badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                    OK
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold leading-5 text-rose-600 dark:text-rose-300">
                                                    {{ $previewRow['status'] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 text-xs font-semibold leading-6 text-slate-600 dark:border-slate-800 dark:bg-slate-950/35 dark:text-slate-300">
                    Nilai jenis yang diterima: Libur Nasional, Libur Semester, Libur Sekolah, Kegiatan Sekolah, atau Lainnya. Nilai presensi: <span class="font-extrabold">Tutup</span> atau <span class="font-extrabold">Buka</span>. Jika kosong, presensi dianggap Tutup.
                </div>

                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeImportModal"
                        class="hk-btn-secondary"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="importHolidays"
                        wire:loading.attr="disabled"
                        wire:target="importHolidays"
                        @disabled(! $importFile || $importValidCount === 0 || $importInvalidCount > 0)
                        class="hk-btn-primary disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="importHolidays">
                            Impor {{ $importValidCount }} Libur
                        </span>
                        <span wire:loading wire:target="importHolidays">
                            Mengimpor...
                        </span>
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>
