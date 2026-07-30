@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Str;
$stats = [
'belum_ditunjuk' => $pengajuan->count(),
'sedang_review' => $penunjukanTpak->total(),
'total_dosen' => $dosens->count(),
'selesai' => 0
];
@endphp

<div class="pt-16 p-6">
    <div class="mx-auto max-w-7xl">
        @include('dupak.penunjukan_tpak.partials.header')

        @include('dupak.penunjukan_tpak.partials.card_data_tpak')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 text-left">Tips Admin</h3>
                            <p class="text-xs text-blue-700 dark:text-blue-400 mt-1 leading-relaxed text-left">
                                Klik tombol "Tunjuk Penilai" pada antrean pengajuan untuk membuka form penunjukan TPAK.
                                Sistem akan otomatis menyaring dosen yang tidak valid.
                            </p>
                        </div>
                    </div>
                </div>

                @include('dupak.penunjukan_tpak.partials.panel_tambah_tpak')

                <!-- Widget ringkasan beban kerja TPAK (top 5) -->
                <div class="flex items-center">
                    <div class="p-2 bg-blue-600 rounded-lg mr-3"><i class="fas fa-clock text-white"></i></div>
                    <h2 class="text-xl font-bold dark:text-white">Beban Kerja TPAK</h2>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    </div>
                    <div class="p-4 space-y-3 max-h-64 overflow-y-auto">
                        @php
                        // Cek apakah dosen ada di array $dosenWorkload (termasuk jika nilainya 0)
                        $sortedDosens = $dosens
                        ->reject(fn($d) => $d->username === 'SYSTEM_MASTER') // Hilangkan user system/master
                        ->sortByDesc(fn($d) => (array_key_exists($d->id, $dosenWorkload) ? 1000 : 0) + ($dosenWorkload[$d->id] ?? 0))
                        ->take(50);
                        @endphp

                        @forelse($sortedDosens as $d)
                        @php $wl = $dosenWorkload[$d->id] ?? 0; @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-700 dark:text-gray-300 truncate w-32">{{ $d->nama_lengkap }}</span>
                            <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full"
                                    style="width: {{ min(100, ($wl / 5) * 100) }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-blue-700 dark:text-blue-300 w-6 text-right">{{ $wl }}</span>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400 italic">Belum ada data penugasan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-600 rounded-lg mr-3"><i class="fas fa-clock text-white"></i></div>
                            <h2 class="text-xl font-bold dark:text-white">Antrean Pengajuan</h2>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-blue-800 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-blue-700">ID</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-blue-700">Dosen Pengaju</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-blue-700">Jabatan Saat Ini</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-blue-700">Jabatan Tujuan</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-blue-700 text-center">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($pengajuan as $p)
                                @php
                                $count = $tpakCounts[$p->id] ?? 0;
                                $isFull = $count >= 5;
                                @endphp
                                <tr class="hover:bg-blue-50/50 {{ $isFull ? 'opacity-60 bg-gray-50' : '' }}">
                                    <td class="px-6 py-4 text-sm dark:text-white">#{{ $p->id }}</td>
                                    <td class="px-6 py-4 text-sm font-bold dark:text-white text-left">
                                        {{ $p->dosen->user->nama_lengkap ?? $p->nama_dosen }}
                                        <span
                                            class="ml-2 text-[10px] px-2 py-0.5 rounded-full {{ $isFull ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $count }}/5
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                            {{ $jfaGlobalNames[$p->jfaAsal] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-emerald-800 bg-emerald-100 rounded-full">
                                            {{ $jfaGlobalNames[$p->jfaTujuan] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($isFull)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-check mr-1"></i> Lengkap
                                        </span>
                                        @else
                                        <button onclick="quickAssign('{{ $p->id }}', '{{ $p->idDosen }}')"
                                            class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700">
                                            <i class="fas fa-user-plus mr-1"></i> Tunjuk Penilai
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Semua pengajuan
                                        sudah memiliki penilai.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-600 rounded-lg mr-3"><i class="fas fa-clock text-white"></i></div>
                        <h2 class="text-xl font-bold dark:text-white">Daftar Penugasan Aktif</h2>
                    </div>
                    <!-- <form action="{{ route('dupak.penunjukan_tpak.index') }}" method="GET" class="relative">
                                <input type="text" name="antrean_search" value="{{ request('antrean_search') }}"
                                    placeholder="Cari pengaju..."
                                    class="pl-8 pr-3 py-1.5 text-xs rounded-full border-gray-300 dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <i class="fas fa-search absolute left-2.5 top-2 text-gray-400 text-[10px]"></i>
                            </form> -->
                </div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-blue-50 dark:bg-blue-900/20">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-bold text-blue-700 uppercase tracking-wider">
                                        Dosen Pengaju</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-bold text-blue-700 uppercase tracking-wider">
                                        Dosen Penilai</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-bold text-blue-700 uppercase tracking-wider">
                                        Progress</th>
                                    <th
                                        class="px-6 py-4 text-center text-[10px] font-bold text-blue-700 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($penunjukanTpak as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $item->pengaju_nama ?? 'N/A' }}
                                        </div>
                                        <div class="text-[10px] text-blue-600 font-medium uppercase tracking-tighter">
                                            ID: #{{ $item->pengajuan_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <i class="fas fa-user-shield text-gray-500"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                                    {{ $item->tpak_nama_lengkap ?? 'N/A' }}
                                                </div>
                                                <div class="text-[10px] text-gray-500 italic">
                                                    {{ Str::limit($item->catatan ?? '', 25) }}
                                                </div>
                                                <div class="text-[10px] text-blue-600">
                                                    <i class="fas fa-user-tag mr-0.5"></i>
                                                    Ditunjuk oleh: {{ $item->creator->nama_lengkap ?? 'Sistem' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->progress_total > 0)
                                        <div class="w-full max-w-[140px]">
                                            <div class="flex justify-between text-[10px] text-gray-600 mb-0.5">
                                                <span>{{ $item->progress_evaluated }}/{{ $item->progress_total }}
                                                    dinilai</span>
                                                <span>{{ $item->progress_percent }}%</span>
                                            </div>
                                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full {{ $item->progress_percent >= 100 ? 'bg-green-500' : 'bg-blue-600' }} rounded-full"
                                                    style="width: {{ $item->progress_percent }}%"></div>
                                            </div>
                                        </div>
                                        @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <span class="h-2 w-2 rounded-full bg-gray-400 mr-2"></span>
                                            Belum ada kegiatan
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('dupak.pengajuan.show', $item->pengajuan_id) }}"
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg"
                                                title="Lihat Detail Pengajuan">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('dupak.penunjukan_tpak.destroy', $item->id) }}"
                                                method="POST" onsubmit="return confirm('Batalkan penugasan ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg"
                                                    title="Batalkan">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-folder-open text-gray-300 fa-3x mb-3"></i>
                                            <p class="text-sm text-gray-500 italic">Tidak ada penugasan aktif saat ini.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($penunjukanTpak->hasPages())
                    <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-t border-blue-200 dark:border-blue-800">
                        {{ $penunjukanTpak->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('dupak.penunjukan_tpak.partials.modal_penunjukan_tpak')

@endsection

@section('script')
<script>
    // Data mapping dari server
    const pengajuMap = @json($pengajuMap ?? []);
    const assignedMap = @json($assignedMap ?? []);
    const tpakCounts = @json($tpakCounts ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        const modalSelectPengajuan = document.getElementById('modal_pengajuan_id');
        const modalSelectTpak = document.getElementById('modal_idDosenTpak');
        const modalInfoPengajuan = document.getElementById('modal-pengajuan-info');
        const modalInfoFilter = document.getElementById('modal-tpak-filter-info');

        function filterTpakOptions() {
            const pengajuanId = modalSelectPengajuan.value;
            if (!pengajuanId) {
                Array.from(modalSelectTpak.options).forEach(opt => {
                    if (opt.value !== '') opt.hidden = false;
                });
                modalInfoFilter.classList.add('hidden');
                modalInfoPengajuan.classList.add('hidden');
                return;
            }

            const pengajuId = pengajuMap[pengajuanId] || null;
            const assigned = assignedMap[pengajuanId] || [];
            const count = tpakCounts[pengajuanId] || 0;
            const targetJfa = modalSelectPengajuan.options[modalSelectPengajuan.selectedIndex].dataset.target;

            if (count >= 5) {
                modalInfoPengajuan.textContent = 'Pengajuan ini sudah memiliki 5 TPAK (maksimal). Target JFA Pengaju: ' + targetJfa;
            } else {
                modalInfoPengajuan.textContent = 'Target JFA Pengaju: ' + targetJfa + '. Sudah ' + count + '/5 TPAK ditugaskan.';
            }
            modalInfoPengajuan.classList.remove('hidden');

            let hiddenCount = 0;
            Array.from(modalSelectTpak.options).forEach(opt => {
                if (opt.value === '') return;

                const dosenId = opt.value;
                const isSelf = pengajuId && dosenId === pengajuId;
                const isAssigned = assigned.includes(dosenId);

                if (isSelf || isAssigned) {
                    opt.hidden = true;
                    hiddenCount++;
                } else {
                    opt.hidden = false;
                }
            });

            if (hiddenCount > 0) {
                modalInfoFilter.textContent = hiddenCount + ' dosen disembunyikan (pengaju sendiri / sudah ditunjuk).';
                modalInfoFilter.classList.remove('hidden');
            } else {
                modalInfoFilter.classList.add('hidden');
            }

            if (modalSelectTpak.selectedOptions[0]?.hidden) {
                modalSelectTpak.value = '';
            }
        }

        if (modalSelectPengajuan) {
            modalSelectPengajuan.addEventListener('change', filterTpakOptions);
        }

        // Escape key untuk menutup modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalTpak();
            }
        });
    });

    function openModalTpak() {
        document.getElementById('modalTpak').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalTpak() {
        document.getElementById('modalTpak').classList.add('hidden');
        document.body.style.overflow = '';
        const form = document.querySelector('#modalTpak form');
        if (form) form.reset();
        const infoFilter = document.getElementById('modal-tpak-filter-info');
        const infoPengajuan = document.getElementById('modal-pengajuan-info');
        if (infoFilter) infoFilter.classList.add('hidden');
        if (infoPengajuan) infoPengajuan.classList.add('hidden');
        Array.from(document.getElementById('modal_idDosenTpak').options).forEach(opt => {
            if (opt.value !== '') opt.hidden = false;
        });
    }

    // Quick assign dari tabel antrean
    function quickAssign(pengajuanId, pengajuId) {
        openModalTpak();
        const selectPengajuan = document.getElementById('modal_pengajuan_id');
        if (selectPengajuan) {
            selectPengajuan.value = pengajuanId;
            selectPengajuan.dispatchEvent(new Event('change'));
        }
    }
</script>
@endsection