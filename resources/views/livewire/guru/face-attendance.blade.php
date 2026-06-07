<div x-data="{ classPickerOpen: false }">
    @php
        $availability = $attendanceAvailability ?? [
            'can_scan' => true,
            'tone' => 'blue',
            'title' => 'Presensi siap digunakan',
            'message' => 'Hari ini termasuk hari sekolah.',
            'detail' => null,
        ];

        $availabilityClass = match ($availability['tone']) {
            'rose' => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200',
            'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200',
            'slate' => 'border-slate-200 bg-slate-100 text-slate-700 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-200',
            default => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200',
        };

        $availabilityIconClass = match ($availability['tone']) {
            'rose' => 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-300',
            'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300',
            'slate' => 'bg-white text-slate-600 dark:bg-slate-900 dark:text-slate-300',
            default => 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300',
        };
    @endphp

    <div class="hk-page">

        <section class="hk-card p-5 sm:p-6">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="text-sm font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Presensi
                    </div>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:text-3xl">
                        {{ $faceAttendanceTitle ?? 'Presensi Wajah' }}
                    </h1>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-slate-200 bg-white/75 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/40 sm:p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="text-[10px] font-extrabold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                            Kelas Pemindaian
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            <h2 class="truncate text-xl font-extrabold text-slate-900 dark:text-white sm:text-2xl">
                                {{ $selectedClassStats['name'] ?? 'Belum memilih kelas' }}
                            </h2>

                            @if($selectedClassStats)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                    {{ $selectedClassStats['ready_students'] }} siap dipindai
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 text-sm font-semibold leading-6 text-slate-500 dark:text-slate-400">
                            Pastikan kelas sudah benar sebelum pemindaian. Siswa tanpa data wajah tidak dapat dikenali.
                        </div>
                    </div>

                    <button
                        id="classPickerToggle"
                        type="button"
                        x-on:click="classPickerOpen = ! classPickerOpen"
                        class="hk-btn-secondary w-full shrink-0 sm:w-auto"
                        :aria-expanded="classPickerOpen.toString()"
                        aria-controls="classPickerPanel"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12M7 9h10M7 13h6M5 19h14" />
                        </svg>
                        {{ $selectedClassStats ? 'Ganti Kelas' : 'Pilih Kelas' }}
                    </button>
                </div>

                <div
                    id="classPickerPanel"
                    x-show="classPickerOpen"
                    x-transition.opacity
                    x-cloak
                    class="mt-4 rounded-2xl border border-blue-100 bg-blue-50/80 p-3 dark:border-blue-500/20 dark:bg-blue-500/10"
                >
                    <label for="classSelect" class="mb-2 block text-[10px] font-extrabold uppercase tracking-wide text-blue-700 dark:text-blue-200">
                        Pilih Kelas
                    </label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <select
                            id="classSelect"
                            wire:model.live="selectedClass"
                            class="hk-input min-w-0 flex-1"
                        >
                            <option value="">
                                Pilih Kelas
                            </option>

                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button" x-on:click="classPickerOpen = false" class="hk-btn-primary shrink-0">
                            Terapkan
                        </button>
                    </div>
                </div>

                @if($selectedClassStats)
                    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/30">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                Siswa Kelas
                            </div>
                            <div class="mt-1 text-lg font-extrabold text-slate-900 dark:text-white">
                                {{ $selectedClassStats['students'] }}
                            </div>
                        </div>

                        <div class="hidden rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/30 lg:block">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-sky-600 dark:text-sky-300">
                                Punya Data Wajah
                            </div>
                            <div class="mt-1 text-lg font-extrabold text-slate-900 dark:text-white">
                                {{ $selectedClassStats['descriptor_students'] }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/30">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">
                                Siap Dipindai
                            </div>
                            <div class="mt-1 text-lg font-extrabold text-slate-900 dark:text-white">
                                {{ $selectedClassStats['ready_students'] }}
                            </div>
                        </div>

                        <div class="hidden rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/30 lg:block">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">
                                Descriptor
                            </div>
                            <div class="mt-1 text-lg font-extrabold text-slate-900 dark:text-white">
                                {{ $selectedClassStats['descriptors'] }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-300 px-4 py-5 text-sm font-bold text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        Pilih kelas untuk melihat kesiapan pemindaian.
                    </div>
                @endif
            </div>

            @if(! $availability['can_scan'] || $availability['tone'] === 'emerald')
                <div class="mt-5 rounded-2xl border p-4 {{ $availabilityClass }}">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $availabilityIconClass }}">
                            @if(! $availability['can_scan'])
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12" />
                                </svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-extrabold">
                                {{ $availability['title'] }}
                            </div>
                            <div class="mt-1 text-sm font-semibold leading-6">
                                {{ $availability['message'] }}
                            </div>
                            @if($availability['detail'])
                                <div class="mt-1 text-xs font-semibold leading-5 opacity-80">
                                    {{ $availability['detail'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.45fr_0.75fr]">

            <section class="hk-card p-4 sm:p-6" wire:ignore>
                <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-800 bg-slate-950 shadow-glow">
                    <video
                        id="video"
                        autoplay
                        muted
                        playsinline
                        class="aspect-video w-full bg-slate-950 object-contain"
                    ></video>

                    <canvas
                        id="faceOverlay"
                        class="pointer-events-none absolute inset-0 h-full w-full"
                    ></canvas>

                    <div
                        id="faceAttendanceIdleState"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 px-6 text-center text-white"
                    >
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h4l2-3h4l2 3h4v13H4V7Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                            </svg>
                        </div>
                        <div class="mt-4 text-lg font-extrabold">
                            {{ $availability['can_scan'] ? 'Kamera belum aktif' : $availability['title'] }}
                        </div>
                        <div class="mt-1 max-w-sm text-sm font-semibold text-slate-300">
                            {{ $availability['can_scan'] ? 'Pilih kelas, lalu mulai pemindaian.' : $availability['message'] }}
                        </div>
                    </div>

                    <div class="pointer-events-none absolute inset-4 rounded-[1.35rem] border border-white/20"></div>

                    <div class="absolute left-4 top-4 rounded-2xl bg-slate-950/70 px-4 py-2 text-sm font-bold text-white backdrop-blur">
                        <span id="scanDot" class="mr-2 inline-block h-2 w-2 rounded-full bg-slate-400"></span>
                        <span id="scanStatus">Siap memindai</span>
                    </div>
                </div>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <button
                        id="startScan"
                        type="button"
                        @disabled(! $availability['can_scan'])
                        class="hk-btn-primary flex-1 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3H5a2 2 0 0 0-2 2v2m14-4h2a2 2 0 0 1 2 2v2M7 21H5a2 2 0 0 1-2-2v-2m18 0v2a2 2 0 0 1-2 2h-2M8 11a4 4 0 0 1 8 0m-9 6a5 5 0 0 1 10 0" />
                        </svg>
                        {{ $availability['can_scan'] ? 'Mulai Pemindaian' : 'Presensi Ditutup' }}
                    </button>

                    <button
                        id="stopScan"
                        type="button"
                        disabled
                        class="hk-btn-secondary flex-1 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h12v12H6V6Z" />
                        </svg>
                        Hentikan Pemindaian
                    </button>
                </div>
            </section>

            <aside class="hk-card p-5 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                        Presensi Terbaru
                    </h2>

                    <div class="rounded-2xl bg-emerald-100 px-3 py-2 text-sm font-extrabold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                        {{ $recentAttendances->count() }}
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($recentAttendances as $attendance)
                        @php
                            $statusLabel = $attendance->status === 'alpha' ? 'Alpa' : ucfirst($attendance->status);
                        @endphp

                        <div class="rounded-2xl border border-slate-100 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/30">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-sm font-extrabold text-blue-600 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ strtoupper(substr($attendance->student->user->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white">
                                        {{ $attendance->student->user->name }}
                                    </div>
                                    <div class="mt-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                        {{ $attendance->student->class->name }} &middot; {{ substr((string) $attendance->attendance_time, 0, 5) }}
                                    </div>
                                </div>

                                <span class="hk-badge shrink-0
                                    @if($attendance->status === 'hadir')
                                        bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300
                                    @elseif($attendance->status === 'terlambat')
                                        bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                                    @elseif($attendance->status === 'sakit')
                                        bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300
                                    @elseif($attendance->status === 'izin')
                                        bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                                    @else
                                        bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                                    @endif
                                ">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700">
                            <div class="text-sm font-bold text-slate-500 dark:text-slate-400">
                                Belum ada presensi hari ini.
                            </div>
                        </div>
                    @endforelse
                </div>
            </aside>

        </section>

    </div>

<script>

    async function initFaceAttendancePage() {

        const video =
            document.getElementById('video');

        const startScan =
            document.getElementById('startScan');

        const stopScan =
            document.getElementById('stopScan');

        const scanStatus =
            document.getElementById('scanStatus');

        const scanDot =
            document.getElementById('scanDot');

        const faceOverlay =
            document.getElementById('faceOverlay');

        const idleState =
            document.getElementById('faceAttendanceIdleState');

        const classPickerToggle =
            document.getElementById('classPickerToggle');

        if (!video || video.dataset.faceAttendanceReady === 'true') {
            return;
        }

        video.dataset.faceAttendanceReady = 'true';

        const successSound =
            new Audio('/sounds/success.mp3');

        const errorSound =
            new Audio('/sounds/error.mp3');

        let scanInterval = null;

        let labeledDescriptors = [];

        let processing = false;

        let modelsLoaded = false;

        let lastUnknownSoundAt = 0;

        const attendanceCanScan =
            @js((bool) $availability['can_scan']);

        const attendanceClosedMessage =
            @js($availability['message']);

        function playSuccessSound() {
            successSound.currentTime = 0;
            successSound.play().catch(() => {});
        }

        function playErrorSound() {
            errorSound.currentTime = 0;
            errorSound.play().catch(() => {});
        }

        function playUnknownSound() {
            const now = Date.now();

            if (now - lastUnknownSoundAt < 2500) {
                return;
            }

            lastUnknownSoundAt = now;

            playErrorSound();
        }

        function getClassSelect() {
            return document.getElementById('classSelect');
        }

        function setScanningState(active) {
            const classSelect = getClassSelect();

            startScan.disabled = active || !attendanceCanScan;
            stopScan.disabled = !active;

            if (classSelect) {
                classSelect.disabled = active;
            }

            if (classPickerToggle) {
                classPickerToggle.disabled = active;
                classPickerToggle.classList.toggle('cursor-not-allowed', active);
                classPickerToggle.classList.toggle('opacity-60', active);
            }

            if (idleState) {
                idleState.classList.toggle('hidden', active);
            }
        }

        function clearFaceOverlay() {
            if (!faceOverlay) return;

            const context = faceOverlay.getContext('2d');

            context.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
        }

        function drawFaceOverlay(detection, label = 'Wajah', color = '#38bdf8') {
            if (!faceOverlay || !detection || !video.videoWidth) return;

            const displaySize = {
                width: video.clientWidth,
                height: video.clientHeight,
            };

            faceapi.matchDimensions(faceOverlay, displaySize);

            const resizedDetection =
                faceapi.resizeResults(detection, displaySize);

            const box = resizedDetection.box || resizedDetection.detection?.box;

            if (!box) return;

            const context = faceOverlay.getContext('2d');

            context.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
            context.lineWidth = 3;
            context.strokeStyle = color;
            context.shadowColor = 'rgba(15, 23, 42, .8)';
            context.shadowBlur = 8;
            context.strokeRect(box.x, box.y, box.width, box.height);
            context.shadowBlur = 0;

            const labelText = label || 'Wajah';
            const labelWidth = Math.max(context.measureText(labelText).width + 18, 92);
            const labelY = Math.max(box.y - 30, 8);

            context.fillStyle = color;
            context.fillRect(box.x, labelY, labelWidth, 24);
            context.fillStyle = '#ffffff';
            context.font = '700 12px sans-serif';
            context.fillText(labelText, box.x + 9, labelY + 16);
        }

        function setStatus(text, color = 'slate') {
            scanStatus.textContent = text;

            scanDot.className = 'mr-2 inline-block h-2 w-2 rounded-full';

            if (color === 'green') {
                scanDot.classList.add('bg-emerald-400');
                return;
            }

            if (color === 'blue') {
                scanDot.classList.add('bg-blue-400');
                return;
            }

            if (color === 'red') {
                scanDot.classList.add('bg-rose-400');
                return;
            }

            scanDot.classList.add('bg-slate-400');
        }

        async function loadModels() {

            if (modelsLoaded) return;

            setStatus('Memuat pustaka wajah', 'blue');

            await window.loadHadirkuFaceApi();

            setStatus('Memuat model wajah', 'blue');

            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');

            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');

            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

            modelsLoaded = true;

            setStatus('Model siap digunakan', 'green');

        }

        async function loadDescriptors(classId) {

            setStatus('Memuat descriptor kelas', 'blue');

            const descriptorBaseUrl =
                @js($descriptorBaseUrl ?? url('/guru/class-descriptors'));

            const response =
                await fetch(`${descriptorBaseUrl}/${classId}`);

            if (!response.ok) {
                throw new Error('Descriptor kelas gagal dimuat.');
            }

            const data = await response.json();

            labeledDescriptors = data
                .filter(student => student.descriptors.length > 0)
                .map(student => {

                    return new faceapi.LabeledFaceDescriptors(
                        student.label,
                        student.descriptors.map(
                            d => new Float32Array(d)
                        )
                    );

                });

            setStatus(`${labeledDescriptors.length} siswa siap dipindai`, 'green');

        }

        async function startCameraStream() {

            if (!navigator.mediaDevices?.getUserMedia) {
                throw new Error('Browser tidak mendukung akses kamera.');
            }

            const stream =
                await navigator.mediaDevices.getUserMedia({

                    video: {
                        width: {
                            ideal: 640,
                        },
                        height: {
                            ideal: 480,
                        },
                        facingMode: 'user',
                    },

                    audio: false,

                });

            video.srcObject = stream;

        }

        function stopCameraStream() {

            const stream = video.srcObject;

            if (!stream) return;

            stream.getTracks().forEach(track => {
                track.stop();
            });

            video.srcObject = null;
            clearFaceOverlay();

        }

        setScanningState(false);

        startScan.addEventListener('click', async () => {

            if (!attendanceCanScan) {

                playErrorSound();

                showToast(
                    'error',
                    attendanceClosedMessage || 'Presensi tidak dibuka hari ini.'
                );

                setStatus('Presensi tidak dibuka', 'red');

                return;
            }

            const classSelect = getClassSelect();
            const classId = classSelect?.value;

            if (!classId) {

                playErrorSound();

                showToast(
                    'error',
                    'Pilih kelas terlebih dahulu.'
                );

                setStatus('Kelas belum dipilih', 'red');

                return;
            }

            startScan.disabled = true;
            stopScan.disabled = true;

            try {

                showToast(
                    'success',
                    'Memulai pemindaian wajah...'
                );

                setStatus('Meminta akses kamera', 'blue');

                await startCameraStream();

                setStatus('Kamera aktif', 'green');
                setScanningState(true);

                await loadModels();

                await loadDescriptors(classId);

                if (labeledDescriptors.length === 0) {

                    playErrorSound();

                    setStatus('Belum ada descriptor siswa', 'red');

                    showToast(
                        'error',
                        'Kelas ini belum memiliki descriptor wajah siswa.'
                    );

                    stopCameraStream();

                    setScanningState(false);

                    return;

                }

                setStatus('Pemindaian aktif', 'green');

                const faceMatcher =
                    new faceapi.FaceMatcher(
                        labeledDescriptors,
                        {{ $settings->face_match_threshold }}
                    );

                scanInterval = setInterval(async () => {

                    if (processing) return;

                    const detection =
                        await faceapi
                            .detectSingleFace(
                                video,
                                new faceapi.TinyFaceDetectorOptions({
                                    inputSize: 320,
                                    scoreThreshold: 0.5,
                                })
                            )
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                    if (!detection) {
                        clearFaceOverlay();
                        setStatus('Mencari wajah', 'blue');
                        return;
                    }

                    processing = true;
                    drawFaceOverlay(detection, 'Wajah terdeteksi', '#38bdf8');

                    setStatus('Wajah terdeteksi', 'blue');

                    const result =
                        faceMatcher.findBestMatch(
                            detection.descriptor
                        );

                    if (result.label === 'unknown') {

                        playUnknownSound();

                        drawFaceOverlay(detection, 'Tidak dikenal', '#fb7185');

                        setStatus('Wajah belum dikenali', 'red');

                        processing = false;

                        return;
                    }

                    drawFaceOverlay(detection, 'Dikenali', '#34d399');

                    const confidence =
                        Number(result.distance.toFixed(4));

                    const saveResult = await @this.call(
                        'saveAttendance',
                        result.label,
                        confidence,
                        classId
                    );

                    if (!saveResult?.saved) {
                        setScanningState(true);

                        playErrorSound();

                        showToast(
                            'error',
                            saveResult?.message || 'Presensi tidak dapat disimpan.'
                        );

                        setStatus(saveResult?.message || 'Presensi tidak tersimpan', 'red');

                        setTimeout(() => {
                            processing = false;
                        }, 1500);

                        return;
                    }

                    setScanningState(true);

                    playSuccessSound();

                    showToast(
                        'success',
                        `${saveResult.message} (${confidence})`
                    );

                    setStatus('Presensi berhasil disimpan', 'green');

                    setTimeout(() => {

                        processing = false;

                    }, 3000);

                }, {{ $settings->scan_interval }});

            } catch (error) {

                playErrorSound();

                stopCameraStream();

                setScanningState(false);

                setStatus('Pemindaian gagal dimulai', 'red');

                showToast(
                    'error',
                    error?.message || 'Pemindaian wajah gagal dimulai.'
                );

            }

        });

        stopScan.addEventListener('click', () => {

            clearInterval(scanInterval);
            scanInterval = null;

            stopCameraStream();
            clearFaceOverlay();

            setScanningState(false);

            setStatus('Pemindaian dihentikan', 'slate');

            showToast(
                'success',
                'Pemindaian dihentikan.'
            );

        });

    }

    document.addEventListener('DOMContentLoaded', initFaceAttendancePage);
    document.addEventListener('livewire:navigated', initFaceAttendancePage);
    initFaceAttendancePage();

</script>

</div>
