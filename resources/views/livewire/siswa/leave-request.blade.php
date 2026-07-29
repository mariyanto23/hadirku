<div>

    <div class="hk-page max-w-6xl">

        <section class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">

            <aside class="hk-card overflow-hidden">
                <div class="border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:px-6">
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Pengajuan Siswa
                    </div>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        Ajukan Izin/Sakit
                    </h1>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-500 dark:text-slate-400">
                        Pengajuan akan masuk ke admin/guru untuk disetujui terlebih dahulu.
                    </p>
                </div>

                <form wire:submit="submit"
                      class="space-y-5 p-5 sm:p-6">

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-500/20 dark:bg-blue-500/10">
                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="mt-1 text-xs font-bold text-blue-700 dark:text-blue-300">
                            NIS {{ $student?->nis ?? '-' }} &middot; {{ $student?->class?->name ?? 'Belum ada kelas' }}
                        </div>
                    </div>

                    <div>
                        <label class="hk-label">Tanggal</label>
                        <input
                            type="date"
                            wire:model="attendance_date"
                            class="hk-input"
                        >
                        @error('attendance_date')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Jenis pengajuan</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex min-h-12 cursor-pointer items-center justify-between gap-3 rounded-2xl border px-4 py-3 transition
                                {{ $status === 'izin'
                                    ? 'border-blue-400 bg-blue-50 text-blue-700 shadow-sm dark:border-blue-500/60 dark:bg-blue-500/10 dark:text-blue-200'
                                    : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                <span class="text-sm font-extrabold">
                                    Izin
                                </span>
                                <input
                                    type="radio"
                                    wire:model="status"
                                    value="izin"
                                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                >
                            </label>

                            <label class="flex min-h-12 cursor-pointer items-center justify-between gap-3 rounded-2xl border px-4 py-3 transition
                                {{ $status === 'sakit'
                                    ? 'border-indigo-400 bg-indigo-50 text-indigo-700 shadow-sm dark:border-indigo-500/60 dark:bg-indigo-500/10 dark:text-indigo-200'
                                    : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                <span class="text-sm font-extrabold">
                                    Sakit
                                </span>
                                <input
                                    type="radio"
                                    wire:model="status"
                                    value="sakit"
                                    class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </label>
                        </div>
                        @error('status')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Keterangan</label>
                        <textarea
                            wire:model="notes"
                            placeholder="Contoh: sakit demam, izin acara keluarga, atau keperluan penting."
                            class="hk-input min-h-32"
                        ></textarea>
                        @error('notes')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="hk-label">Lampiran <span class="font-semibold text-slate-400">(opsional)</span></label>
                        <input
                            type="file"
                            wire:model="attachment"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf"
                            class="hk-input file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-blue-300"
                        >
                        <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Format PDF, JPG, PNG, atau WebP. Maksimal 2 MB.
                        </div>
                        @if($attachment)
                            <div class="mt-2 truncate text-xs font-bold text-blue-600 dark:text-blue-300">
                                {{ $attachment->getClientOriginalName() }}
                            </div>
                        @endif
                        @error('attachment')
                            <div class="hk-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="hk-btn-primary w-full"
                    >
                        <span wire:loading.remove>
                            Kirim Pengajuan
                        </span>

                        <span wire:loading>
                            Mengirim...
                        </span>
                    </button>

                </form>
            </aside>

            <section class="hk-card overflow-hidden">
                <div class="border-b border-slate-200/70 px-5 py-5 dark:border-slate-800 sm:px-6">
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Riwayat
                    </div>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                        Status Pengajuan
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-slate-50/90 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-4 text-left font-bold">Tanggal</th>
                                <th class="px-5 py-4 text-left font-bold">Jenis</th>
                                <th class="px-5 py-4 text-left font-bold">Persetujuan</th>
                                <th class="px-5 py-4 text-left font-bold">Keterangan</th>
                                <th class="px-5 py-4 text-left font-bold">Tinjauan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($requests as $request)
                                <tr class="transition hover:bg-blue-50/60 dark:hover:bg-slate-800/45">
                                    <td class="px-5 py-4 font-bold text-slate-700 dark:text-slate-200">
                                        {{ $request->attendance_date->translatedFormat('d F Y') }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="hk-badge
                                            @if($request->status === 'sakit')
                                                bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300
                                            @elseif($request->status === 'alpha')
                                                bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                                            @else
                                                bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                                            @endif
                                        ">
                                            {{ $request->status === 'alpha' ? 'DITOLAK' : strtoupper($request->status) }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="hk-badge
                                            @if($request->approval_status === 'approved')
                                                bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                                            @elseif($request->approval_status === 'rejected')
                                                bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                                            @else
                                                bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                                            @endif
                                        ">
                                            @if($request->approval_status === 'approved')
                                                Disetujui
                                            @elseif($request->approval_status === 'rejected')
                                                Ditolak
                                            @else
                                                Menunggu
                                            @endif
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="max-w-xs space-y-1 font-semibold text-slate-600 dark:text-slate-300">
                                            <div class="truncate">
                                                {{ $request->notes ?: '-' }}
                                            </div>

                                            @if($request->attachment_path)
                                                <a
                                                    href="{{ $request->attachmentUrl() }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                    class="inline-flex items-center gap-1 text-xs font-extrabold text-blue-600 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200"
                                                >
                                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.48-8.49" />
                                                    </svg>
                                                    Lihat Lampiran
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="max-w-xs font-semibold text-slate-600 dark:text-slate-300">
                                            @if($request->reviewedBy)
                                                {{ $request->reviewedBy->name }}
                                                <div class="mt-1 text-xs text-slate-400">
                                                    {{ $request->reviewed_at?->translatedFormat('d F Y H:i') }}
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5V4Zm4 5h6M9 13h6M9 17h3" />
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-300">
                                            Belum ada pengajuan.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200/70 px-5 py-4 dark:border-slate-800 sm:px-6">
                    {{ $requests->links() }}
                </div>
            </section>

        </section>

    </div>

</div>
