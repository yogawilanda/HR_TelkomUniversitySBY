@php
$moduls = [
['Data Kepegawaian', 'Master Data', route('manage.view'), 'fa-solid fa-users-gear', '#2E86AB'], // biru lembut profesional
['Kinerja Pegawai', 'Analisis TPA', 'tes', 'fa-solid fa-chart-line', '#28B463'], // hijau seimbang dan produktif
['DUPAK Dosen', 'Analisis Kedosenan', route('dupak.dashboard'), 'fa-solid fa-file-circle-check', '#AF7AC5'],
];
@endphp

<x-app-layout>
    <x-slot name="header">



        {{-- List Aplikasi — App Icons Grid (Tailwind-only, view-only) --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white/90 p-6 shadow-md backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900/80">
            <div class="mb-6">
                <h3 class="text-lg font-semibold tracking-wide text-gray-900 dark:text-gray-100">Aplikasi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih aplikasi untuk membuka.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                <!-- App: Dashboard -->
                @foreach ($moduls as $modul)
                <a href="{{ $modul[2] }}"
                    class="group route_pop_up relative flex flex-col items-center justify-center rounded-2xl bg-white p-6 border border-gray-200 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-blue-400 dark:bg-gray-900 dark:border-gray-700">

                    <!-- Icon -->
                    <div
                        class="relative mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-500 text-white shadow-md transition-transform duration-300 group-hover:scale-110">
                        <i class="{{ $modul[3] }} fa-2x"></i>
                        <div class="absolute inset-0 rounded-2xl bg-white/10"></div>
                    </div>

                    <!-- Text -->
                    <div class="text-center">
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-100 leading-snug">
                            {{ $modul[0] }}
                        </p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 font-medium">
                            {{ $modul[1] }}
                        </p>
                    </div>

                    <!-- Hover highlight -->
                    <div
                        class="pointer-events-none absolute inset-0 rounded-2xl bg-blue-100/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 dark:bg-blue-900/20">
                    </div>
                </a>
                @endforeach
            </div>
        </div>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>