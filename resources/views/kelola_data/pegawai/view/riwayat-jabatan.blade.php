@php
    $active_sidebar = 'History Pemetaan Jabatan';
@endphp

@extends('kelola_data.base-profile')

@section('content-profile')
    <style>
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 20px;
        }
    </style>

    <div class="min-h-screen font-sans antialiased relative px-4 md:px-6">

        <!-- Background -->
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div
                class="absolute -top-32 left-1/2 h-72 w-72 md:h-80 md:w-80 -translate-x-1/2 rounded-full bg-indigo-500 blur-[120px] opacity-40">
            </div>
            <div
                class="absolute bottom-0 left-0 h-60 w-60 md:h-72 md:w-72 rounded-full bg-violet-600 blur-[120px] opacity-30">
            </div>
            <div
                class="absolute bottom-10 right-0 h-64 w-64 md:h-80 md:w-80 rounded-full bg-emerald-500 blur-[120px] opacity-30">
            </div>
        </div>

        <!-- Header -->
        <div class="mb-6 md:mb-8 text-center">
            <h2 class="text-lg md:text-2xl font-semibold text-gray-900">
                Jabatan Struktural Yang Sedang Aktif
            </h2>
            <p class="hidden md:block text-gray-500 text-sm">
                Urutan kronologis riwayat pemetaan saat ini.
            </p>
        </div>

        <!-- Active Cards -->
        <main class="pb-6 border-b border-gray-200/70 flex justify-center">
            <div
                class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 justify-items-center sm:justify-items-stretch max-w-6xl mx-auto">

                @forelse ($user['pengawakans_aktif'] as $pemetaan)
                    <div
                        class="bg-white rounded-3xl shadow-lg border border-gray-100 hover:scale-[1.02] hover:shadow-xl transition">

                        <div class="h-28 md:h-32 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-t-3xl"></div>

                        <div class="flex flex-col items-center p-4 md:p-6 -mt-14">
                            <div
                                class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-gray-100 border-4 border-white shadow flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.6"
                                    viewBox="0 0 24 24">
                                    <path d="M4 6h16M4 12h16M4 18h7" stroke-linecap="round" />
                                </svg>
                            </div>

                            <h2 class="mt-3 text-base md:text-lg font-semibold text-gray-800 text-center">
                                {{ $pemetaan->users->nama_lengkap }}
                            </h2>

                            <p class="text-gray-500 text-xs md:text-sm text-center">
                                {{ $pemetaan->formasi->nama_formasi }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 text-sm text-center">Tidak Ada Jabatan Struktural Aktif</p>
                @endforelse

            </div>
        </main>

        <!-- Floating Button -->
        <button
            class="w-full md:w-auto mt-6 md:mt-0 md:fixed md:bottom-6 md:right-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-3 rounded-full shadow-lg transition hover:scale-105">
            + Tambah Pemetaan Pegawai Ini
        </button>

        <!-- History -->
        <main class="mt-10 max-w-6xl mx-auto">

            <!-- Title -->
            <div class="mb-6 md:mb-8 text-center">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900">
                    Riwayat Jabatan Struktural
                </h2>
                <p class="hidden md:block text-gray-500 text-sm">
                    Urutan riwayat pemetaan dari awal hingga saat ini.
                </p>
            </div>

            
            <!-- Filter -->
            <section class="mb-6">
                <div class="flex flex-col md:flex-row md:justify-between gap-4">

                    <div class="text-sm">
                        <h2 class="font-semibold text-gray-900">Filter</h2>
                        <p class="text-xs text-gray-600">
                            Gunakan filter kategori.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button data-category="all" data-active="true"
                            class="history-filter-btn px-3 py-1 text-xs rounded-full bg-indigo-600 text-white" >Semua</button>
                        <button data-category="Bagian"
                            class="history-filter-btn px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-200">Bagian</button>
                        <button data-category="Program Studi"
                            class="history-filter-btn px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-200">Program
                            Studi</button>
                        <button data-category="Fakultas"
                            class="history-filter-btn px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-200">Fakultas</button>
                    </div>
                </div>
            </section>

            <!-- Timeline -->
            <section>
                <div class="relative pl-6 md:pl-8">

                    <!-- Line -->
                    <div
                        class="absolute left-2 md:left-4 top-0 h-full w-[2px] bg-gradient-to-b from-indigo-500 via-slate-600 to-transparent">
                    </div>

                    <div class="space-y-6">

                        @forelse ($user['pengawakans'] as $pemetaan)
                            <article
                                class="history-item relative pl-6 md:pl-8 {{ $pemetaan->tmt_selesai ? 'opacity-40' : '' }}"
                                data-category="{{ $pemetaan->formasi->bagian->type_work_position ?? 'lainnya' }}">

                                <!-- Dot -->
                                <div class="absolute left-0 top-2 w-3 h-3 bg-indigo-500 rounded-full border-2 border-white">
                                </div>

                                <!-- Date -->
                                <div
                                    class="text-[11px] md:text-xs bg-gray-900 text-white px-2 py-1 rounded-full inline-block">
                                    {{ date('M Y', strtotime($pemetaan->tmt_mulai)) }} -
                                    {{ $pemetaan->tmt_selesai ? date('M Y', strtotime($pemetaan->tmt_selesai)) : 'Sekarang' }}
                                </div>

                                <!-- Title -->
                                <h3 class="mt-2 text-sm md:text-base font-semibold text-gray-900">
                                    {{ $pemetaan->formasi->nama_formasi }}
                                </h3>

                                <p class="text-xs text-gray-600 mt-1">
                                    Menjalankan tugas sesuai jabatan.
                                </p>

                            </article>
                        @empty
                            <p class="text-gray-600 text-sm">Belum Ada Riwayat.</p>
                        @endforelse

                    </div>
                </div>
            </section>

        </main>
    </div>

    {{-- FILTER SCRIPT --}}
    <script>
        const filterButtons = document.querySelectorAll('.history-filter-btn');
        const historyItems = document.querySelectorAll('.history-item');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const category = btn.dataset.category;

                filterButtons.forEach(b => b.dataset.active = false);
                btn.dataset.active = true;

                historyItems.forEach(item => {
                    const match = category === 'all' || item.dataset.category === category;
                    console.log(match, category,item.dataset.category );
                    item.classList.toggle('opacity-30', !match);
                    item.classList.toggle('scale-[0.98]', !match);
                });
            });
        });
    </script>
@endsection
