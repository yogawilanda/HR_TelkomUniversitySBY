<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    {{-- {{ dd(session('account')) }} --}}
</head>
@php
    $moduls = [
        // ['Kinerja Pegawai', 'Analisis TPA', 'tes', 'fa-solid fa-chart-line', '#28B463'],
        // ['DUPAK Dosen', 'Analisis Kedosenan', route('dupak.dashboard'), 'fa-solid fa-file-circle-check', '#AF7AC5'],
    ];
    // {{ dd(session('account')) }}
    // {{ }}
    // dd(session('account'));
    // dd()
    if (session()->has('account') && session('account')['is_admin'] === true) {
        array_unshift($moduls, [
            'Data Kepegawaian',
            'Master Data',
            route('manage.view'),
            'fa-solid fa-users-gear',
            '#2E86AB',
        ]);
    }
@endphp

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        {{-- Navigation bar using the shared component --}}
        @include('layouts.navigation')

        <main role="main"
            class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 via-white to-blue-50 px-6 dark:from-gray-950 dark:via-gray-900 dark:to-slate-900">
            <div class="max-w-md text-center">
                <div class="mb-8 flex justify-center">
                    <div class="rounded-full bg-blue-100 p-6 dark:bg-blue-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <h1 class="mb-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                    Akses Terbatas
                </h1>

                <div class="mb-10 space-y-4">
                    <p class="text-lg leading-relaxed text-slate-600 dark:text-gray-400">
                        Silakan login untuk mengakses Sistem Informasi Manajemen Pegawai.
                    </p>
                    <div
                        class="rounded-lg bg-amber-50 p-4 border border-amber-100 dark:bg-amber-900/10 dark:border-amber-900/20">
                        <p class="text-sm text-amber-700 dark:text-amber-400">
                            <strong>Informasi:</strong> Akun dibuat secara terpusat. Jika belum memiliki akses, harap
                            hubungi bagian <b>Admin Kepegawaian</b> di unit kerja Anda.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-blue-600/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Masuk ke Akun
                    </a>
                </div>

                <p class="mt-12 text-xs text-slate-400 dark:text-gray-500 uppercase tracking-widest">
                    Sistem Informasi Manajemen Pegawai 1.0
                </p>
            </div>
        </main>



    </div>
        <x-footer></x-footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @include('components.js.pop-message')
    @include('components.js.route-pop-up-button')
    <script></script>




</body>

</html>
