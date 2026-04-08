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
        ['Kinerja Pegawai', 'Analisis TPA', 'tes', 'fa-solid fa-chart-line', '#28B463'],
        ['DUPAK Dosen', 'Analisis Kedosenan', route('dupak.dashboard'), 'fa-solid fa-file-circle-check', '#AF7AC5'],
    ];

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

        <main role="main" class="w-full bg-gradient-to-b from-slate-50 to-white dark:from-gray-950 dark:to-gray-900">
            <section class="py-12 min-h-screen">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                    {{-- <header class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">SDM</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Sistem Pengelolaan Kinerja Pegawai</p>
      </header> --}}

                    {{-- Layout utama --}}
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-12">

                        {{-- Modul: prioritas lebar di desktop, penuh di mobile --}}
                        <section class="md:col-span-8 lg:col-span-7">
                            <div
                                class="rounded-2xl border mb-6 border-slate-200 supports-[backdrop-filter]:bg-white/80 bg-white shadow-lg backdrop-blur-sm dark:border-slate-800 dark:supports-[backdrop-filter]:bg-slate-900/70 dark:bg-slate-900">
                                <div
                                    class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-800">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Modul</h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Pilih modul untuk membuka.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-6">
                                    {{-- Auto-fit responsif: kartu selalu rapih tanpa mepet --}}
                                    <div
                                        class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(150px,1fr))] sm:[grid-template-columns:repeat(auto-fit,minmax(180px,1fr))]">
                                        @foreach ($moduls as $modul)
                                            <a href="{{ $modul[2] }}" aria-label="Buka {{ $modul[0] }}"
                                                class="group relative flex h-full flex-col items-center rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:shadow-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                                                {{-- Icon: tidak mem-fade, efek ring + scale saja --}}
                                                <div
                                                    class="relative mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-500 text-white shadow-md transition-transform duration-200 motion-safe:group-hover:scale-110 motion-safe:group-hover:rotate-1">
                                                    <i class="{{ $modul[3] }} fa-2x" aria-hidden="true"></i>
                                                </div>

                                                {{-- Teks: tetap solid saat hover --}}
                                                <div class="text-center">
                                                    <p
                                                        class="text-sm font-semibold leading-snug text-slate-900 dark:text-slate-100">
                                                        {{ $modul[0] }}
                                                    </p>
                                                    <p
                                                        class="mt-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">
                                                        {{ $modul[1] }}
                                                    </p>
                                                </div>

                                                {{-- Hover outline halus (tanpa overlay opacity) --}}
                                                <span
                                                    class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-transparent transition duration-200 group-hover:ring-indigo-300 dark:group-hover:ring-indigo-500/40"></span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <x-info-card title="Jumlah Pegawai" value="204" percentage="100%"
                                percentageColor="green" />

                        </section>

                        {{-- Ringkasan/KPI --}}
                        <aside class="md:col-span-4 lg:col-span-5">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-info-card title="Jumlah Pegawai" value="204" percentage="100%"
                                    percentageColor="green" />

                                <x-info-card title="Izin" value="5" percentage="3%" percentageColor="#dc3545">
                                    <x-slot name="icon">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                    </x-slot>
                                </x-info-card>

                                <x-info-card title="Cuti" value="12" percentage="8%" percentageColor="red" />
                                <x-info-card title="Hadir" value="187" percentage="92%" percentageColor="green" />
                            </div>

                            <div
                                class="mt-6 rounded-2xl border border-slate-200 supports-[backdrop-filter]:bg-white/80 bg-white p-5 shadow-lg backdrop-blur-sm dark:border-slate-800 dark:supports-[backdrop-filter]:bg-slate-900/70 dark:bg-slate-900">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Ringkasan
                                            Hari Ini</h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Update terakhir:
                                            {{ now()->format('d M Y, H:i') }}</p>
                                    </div>
                                    <a href="#"
                                        class="text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-400">Lihat
                                        detail</a>
                                </div>
                            </div>
                        </aside>

                    </div>
                </div>
            </section>
        </main>



    </div>
    <script></script>
</body>

</html>
