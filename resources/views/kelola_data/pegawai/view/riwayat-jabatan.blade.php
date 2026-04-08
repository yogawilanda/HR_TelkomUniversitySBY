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

    <div class="min-h-screen font-sans gap-5 antialiased">

        {{-- Background --}}
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div
                class="absolute -top-32 left-1/2 h-80 w-80 -translate-x-1/2 
                    rounded-full bg-indigo-500 blur-[120px] opacity-40">
            </div>

            <div
                class="absolute bottom-0 left-0 h-72 w-72 rounded-full 
                    bg-violet-600 blur-[120px] opacity-30">
            </div>

            <div
                class="absolute bottom-10 right-0 h-80 w-80 rounded-full 
                    bg-emerald-500 blur-[120px] opacity-30">
            </div>
        </div>

        <!-- Header Active Role -->
        <div class="mb-8 flex flex-col items-center gap-2 md:gap-0">
            <h2 class="text-lg md:text-2xl font-semibold text-gray-900">
                Jabatan Struktural Yang Sedang Aktif
            </h2>
            <p class="hidden md:block text-gray-500 text-sm">
                Urutan kronologis riwayat pemetaan saat ini.
            </p>
        </div>

        <!-- Active Cards -->
        <main class="flex justify-center pb-5 border-b border-gray-200/70">
            <div class="w-full flex flex-wrap gap-6 justify-center">

                @forelse ($user['pengawakans_aktif'] as $pemetaan)
                    {{-- @if ($pemetaan->tmt_selesai == null) --}}
                        {{-- {{ dd($pemetaan) }} --}}
                        <div
                            class="bg-white max-w-50 rounded-3xl shadow-lg border border-gray-100
                            transition-transform hover:scale-[1.02] hover:shadow-xl">
                            <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-500"></div>

                            <div class="flex flex-col items-center p-6 -mt-16">
                                <div
                                    class="w-28 h-28 rounded-full bg-gray-100 border-4 border-white shadow-md
                                    flex items-center justify-center">
                                    <svg class="w-14 h-14 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                        <path d="M4 6h16M4 12h16M4 18h7" stroke-linecap="round" />
                                    </svg>
                                </div>

                                <h2 class="mt-4 text-xl font-semibold text-gray-800">{{ $pemetaan->users->nama_lengkap }}
                                </h2>
                                <p class="text-gray-500 text-sm">{{ $pemetaan->formasi->nama_formasi }}</p>
                            </div>
                        </div>
                    {{-- @endif --}}
                @empty
                    <p class="text-gray-600 text-sm">Tidak Ada Jabatan Struktural Aktif</p>
                @endforelse
            </div>
        </main>

        <!-- History -->
        <main id="top" class="mt-10 max-w-6xl mx-auto px-2">

            <!-- Section Title -->
            <div class="mb-8 flex flex-col items-center">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900">
                    Riwayat Jabatan Struktural
                </h2>
                <p class="hidden md:block text-gray-500 text-sm">
                    Urutan riwayat pemetaan dari awal hingga saat ini.
                </p>
            </div>

            <!-- Filter -->
            <section class="mb-8">
                <div class="flex flex-col items-end gap-3">

                    <div class="text-end">
                        <h2 class="text-sm font-semibold text-gray-900">Filter</h2>
                        <p class="text-xs text-gray-700 max-w-md">
                            Gunakan filter kategori untuk fokus pada posisi kerja tertentu dalam riwayat pemetaan.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            class="history-filter-btn px-3 py-1.5 text-[11px] rounded-full 
                                   border border-gray-600 bg-gray-900 text-white
                                   data-[active=true]:bg-indigo-600 data-[active=true]:border-indigo-500"
                            data-category="all" data-active="true">Semua</button>

                        <button
                            class="history-filter-btn px-3 py-1.5 text-[11px] rounded-full 
                                   border border-gray-600 bg-gray-900/80 text-gray-200 hover:bg-gray-800"
                            data-category="Bagian">Struktural Bagian</button>

                        <button
                            class="history-filter-btn px-3 py-1.5 text-[11px] rounded-full 
                                   border border-gray-600 bg-gray-900/80 text-gray-200 hover:bg-gray-800"
                            data-category="Program Studi">Struktural Program Studi</button>

                        <button
                            class="history-filter-btn px-3 py-1.5 text-[11px] rounded-full 
                                   border border-gray-600 bg-gray-900/80 text-gray-200 hover:bg-gray-800"
                            data-category="Fakultas">Struktural Fakultas</button>
                    </div>
                </div>
            </section>

            <!-- Timeline -->
            <section id="timeline">
                <div class="relative pl-8">

                    <!-- Vertical Line -->
                    <div
                        class="absolute left-4 top-0 h-full w-[2px] bg-gradient-to-b 
                            from-indigo-500 via-slate-600 to-transparent">
                    </div>

                    <div class="space-y-7">

                        @forelse ($user['pengawakans'] as $pemetaan)
                            <article
                                class="history-item relative pl-8 transition-all duration-200
                                        {{ $pemetaan->tmt_selesai ? 'opacity-30' : '' }}"
                                data-category="{{ $pemetaan->formasi->bagian->type_work_position }}">

                                <!-- Marker -->
                                <div
                                    class="absolute left-0 top-2 w-3 h-3 rounded-full 
                                        bg-indigo-500 border-[3px] border-white shadow">
                                </div>

                                <!-- Date -->
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 text-white text-xs
                                        bg-gray-900/80 rounded-full border border-gray-700">
                                    {{ date('F Y', strtotime($pemetaan->tmt_mulai)) }} -
                                    {{ $pemetaan->tmt_selesai ? date('F Y', strtotime($pemetaan->tmt_selesai)) : 'Sekarang' }}
                                </div>

                                <!-- Title -->
                                <h3 class="mt-3 text-base font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="{{ $pemetaan->formasi->level_data->icon }} text-indigo-600"></i>
                                    {{ $pemetaan->formasi->nama_formasi }}
                                </h3>

                                <p class="mt-2 text-xs text-gray-700 leading-relaxed">
                                    Menjalankan tugas, fungsi, dan tanggung jawab sesuai jabatan yang ditempati.
                                </p>

                                @if (session('account')['is_admin'])
                                    <div class="flex items-center gap-2 mt-3">
                                        <span class="text-sm font-medium text-gray-800">SK Jabatan:</span>
                                        <button
                                            class="px-3 py-1 text-xs font-semibold bg-blue-100 
                                                   text-blue-700 rounded-full flex items-center gap-2">
                                            <i class="fa-solid fa-file"></i> SK
                                        </button>
                                    </div>
                                @endif

                            </article>

                        @empty
                            <p class="text-gray-600 text-sm">Belum Ada Riwayat Pemetaan.</p>
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
                    item.classList.toggle('opacity-30', !match);
                    item.classList.toggle('scale-[0.98]', !match);
                });
            });
        });
    </script>
@endsection
