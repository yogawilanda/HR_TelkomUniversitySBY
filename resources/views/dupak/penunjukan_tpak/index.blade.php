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
            <a href="{{ route('dupak.dashboard') }}"
                class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white text-left">Penunjukan TPAK - DUPAK</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-left">Monitoring pengajuan, penugasan TPAK, dan
                        beban kerja penilai.</p>
                </div>
                <div class="flex gap-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-calendar-alt mr-2"></i> Periode {{ date('Y') }}
                    </span>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Ditunjuk</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['belum_ditunjuk'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-600"><i
                                class="fas fa-exclamation-circle fa-lg"></i></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Sedang Review</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $stats['sedang_review'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-700"><i class="fas fa-sync-alt fa-lg"></i></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Dosen</p>
                            <p class="text-2xl font-bold text-blue-800">{{ $stats['total_dosen'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-800"><i class="fas fa-users fa-lg"></i></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Selesai Dinilai</p>
                            <p class="text-2xl font-bold text-blue-500">{{ $stats['selesai'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-500"><i class="fas fa-check-double fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

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
                                $sortedDosens = $dosens->sortByDesc(fn($d) => $dosenWorkload[$d->id] ?? 0)->take(8);
                            @endphp
                            @forelse($sortedDosens as $d)
                                @php $wl = $dosenWorkload[$d->id] ?? 0; @endphp
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs text-gray-700 dark:text-gray-300 truncate w-32">{{ $d->nama_lengkap }}</span>
                                    <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full"
                                            style="width: {{ min(100, ($wl / 5) * 100) }}%"></div>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-blue-700 dark:text-blue-300 w-6 text-right">{{ $wl }}</span>
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
                            <!-- <form action="{{ route('dupak.penunjukan_tpak.index') }}" method="GET" class="relative">
                                <input type="text" name="antrean_search" value="{{ request('antrean_search') }}"
                                    placeholder="Cari pengaju..."
                                    class="pl-8 pr-3 py-1.5 text-xs rounded-full border-gray-300 dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <i class="fas fa-search absolute left-2.5 top-2 text-gray-400 text-[10px]"></i>
                            </form> -->
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
                                                    {{ $item->pengaju_nama ?? 'N/A' }}</div>
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
                                                            {{ $item->tpak_nama_lengkap ?? 'N/A' }}</div>
                                                        <div class="text-[10px] text-gray-500 italic">
                                                            {{ Str::limit($item->catatan ?? '', 25) }}</div>
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

    <!-- Modal Penunjukan TPAK -->
    <div id="modalTpak" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModalTpak()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-blue-900 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center"
                            id="modal-title">
                            <i class="fas fa-user-plus mr-2"></i> Penunjukan Penilai Baru
                        </h3>
                        <button type="button" onclick="closeModalTpak()"
                            class="text-blue-200 hover:text-white focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('dupak.penunjukan_tpak.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih
                                Pengajuan (Antrean)</label>
                            <select id="modal_pengajuan_id" name="pengajuan_id"
                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                required>
                                <option value="">-- Cari Nama Pengaju --</option>
                                @foreach($pengajuan as $p)
                                    @php
                                        $count = $tpakCounts[$p->id] ?? 0;
                                        $targetJfa = $jfaGlobalNames[$p->jfaTujuan] ?? 'N/A';
                                    @endphp
                                    <option value="{{ $p->id }}" data-pengaju="{{ $p->idDosen }}" data-count="{{ $count }}" data-target="{{ $targetJfa }}">
                                        #{{ $p->id }} - {{ $p->nama_dosen }} (Target: {{ $targetJfa }})
                                    </option>
                                @endforeach
                            </select>
                            <p id="modal-pengajuan-info" class="text-[10px] text-gray-500 mt-1 italic hidden"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih
                                Dosen TPAK (Penilai)</label>
                            <select id="modal_idDosenTpak" name="idDosenTpak"
                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                required>
                                <option value="">-- Pilih Penilai --</option>
                                @foreach($dosens as $d)
                                    @php $workload = $dosenWorkload[$d->id] ?? 0; @endphp
                                    @php $jfaNama = $tpakJfaNama[$d->id] ?? null; @endphp
                                    <option value="{{ $d->id }}" data-workload="{{ $workload }}" data-jfa="{{ $jfaNama }}">
                                        {{ $d->nama_lengkap }} (Beban: {{ $workload }} penugasan) -
                                        {{ $jfaNama ? $jfaNama : 'JFA tidak aktif' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-500 mt-1 italic">* Pastikan JFA Penilai ≥ JFA Pengaju</p>
                            <p id="modal-tpak-filter-info" class="text-[10px] text-yellow-600 mt-1 italic hidden"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Catatan
                                Instruksi</label>
                            <textarea name="catatan" rows="3"
                                class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="Instruksi khusus untuk penilai..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="closeModalTpak()"
                                class="px-4 py-2 text-xs font-bold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-bold text-white bg-blue-900 rounded-lg hover:bg-blue-800 flex items-center">
                                Tugaskan Sekarang <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        // Data mapping dari server
        const pengajuMap = @json($pengajuMap ?? []);
        const assignedMap = @json($assignedMap ?? []);
        const tpakCounts = @json($tpakCounts ?? []);

        document.addEventListener('DOMContentLoaded', function () {
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
            document.addEventListener('keydown', function (e) {
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