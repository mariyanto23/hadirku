@php
    $appSettings = \App\Models\AttendanceSetting::current();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
            darkMode: localStorage.getItem('darkMode') === 'true',
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
      }"
      x-init="
            darkMode
                ? document.documentElement.classList.add('dark')
                : document.documentElement.classList.remove('dark');

            $watch('darkMode', value => {

                localStorage.setItem('darkMode', value);

                value
                    ? document.documentElement.classList.add('dark')
                    : document.documentElement.classList.remove('dark');

            });

            $watch('sidebarCollapsed', value => {

                localStorage.setItem('sidebarCollapsed', value);

            });
      "
>

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'HadirKu') }}
    </title>

    @if($appSettings->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $appSettings->favicon_path) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <script>
        window.loadHadirkuFaceApi = (() => {
            let loadingPromise = null;

            const sources = [
                '{{ asset('vendor/face-api/face-api.min.js') }}',
                'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js',
                'https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js',
            ];

            function loadScript(src) {
                return new Promise((resolve, reject) => {
                    const existing = document.querySelector(`script[src="${src}"]`);

                    if (existing) {
                        if (existing.dataset.loaded === 'true') {
                            resolve();
                            return;
                        }

                        existing.addEventListener('load', resolve, { once: true });
                        existing.addEventListener('error', reject, { once: true });
                        return;
                    }

                    const script = document.createElement('script');

                    script.src = src;
                    script.async = true;
                    script.onload = () => {
                        script.dataset.loaded = 'true';
                        resolve();
                    };
                    script.onerror = reject;

                    document.head.appendChild(script);
                });
            }

            async function waitUntilReady(timeout = 15000) {
                const startedAt = Date.now();

                while (!window.faceapi) {
                    if (Date.now() - startedAt > timeout) {
                        throw new Error('face-api.js belum berhasil dimuat.');
                    }

                    await new Promise(resolve => setTimeout(resolve, 100));
                }
            }

            return async () => {
                if (window.faceapi) {
                    return window.faceapi;
                }

                if (loadingPromise) {
                    await loadingPromise;
                    await waitUntilReady();
                    return window.faceapi;
                }

                loadingPromise = (async () => {
                    let lastError = null;

                    for (const source of sources) {
                        try {
                            await loadScript(source);
                            await waitUntilReady(5000);
                            return;
                        } catch (error) {
                            lastError = error;
                        }
                    }

                    throw lastError || new Error('face-api.js belum berhasil dimuat.');
                })();

                await loadingPromise;
                await waitUntilReady();

                return window.faceapi;
            };
        })();
    </script>

    @livewireStyles

    @livewireScriptConfig

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="min-h-screen overflow-x-hidden bg-gradient-to-br from-slate-100 via-blue-50 to-emerald-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

<div class="min-h-screen md:flex">

    <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm md:hidden"
        @click="sidebarOpen = false"
        x-cloak
    ></div>

    @include('components.layouts.sidebar')

    <div class="flex min-h-screen flex-1 flex-col">

        @include('components.layouts.navbar')

        <main class="flex-1 px-4 py-5 pb-24 sm:px-6 lg:px-8 lg:py-8">
            {{ $slot }}
        </main>

    </div>

</div>

<script>
    window.addEventListener('hadirku-toast', event => {
        const detail = event.detail;
        const payload = Array.isArray(detail) ? detail[0] : detail;
        const message = payload?.message;

        if (!message) {
            return;
        }

        showToast(payload?.type || 'success', message);
    });

    @if(session()->has('hadirku-toast'))
        window.addEventListener('load', () => {
            const payload = @json(session('hadirku-toast'));

            showToast(payload?.type || 'success', payload?.message || '');
        });
    @endif
</script>

</body>
</html>
