@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard DUPAK
        </a>

        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard TPAK</h1>
                        <p class="text-sm text-gray-500 mt-1">Kelola validasi angka kredit dosen yang ditugaskan</p>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="px-5 py-3 bg-blue-50 rounded-xl border border-blue-100 text-center min-w-[100px]">
                            <p class="text-xs font-semibold text-blue-600 uppercase">Tugas</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $pengajuanList->total() }}</p>
                        </div>
                        <div class="px-5 py-3 bg-emerald-50 rounded-xl border border-emerald-100 text-center min-w-[100px]">
                            <p class="text-xs font-semibold text-emerald-600 uppercase">Selesai</p>
                            <p class="text-2xl font-bold text-emerald-900">{{ $selesaiCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <div class="bg-white shadow rounded-lg">
            <div class="p-6 border-b border-gray-100">
                <form method="GET" action="{{ route('dupak.validasi.index') }}" class="flex flex-wrap gap-3 items-center">
                    <div class="flex-1 min-w-[250px] relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama dosen..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                        <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-blue-900 text-white rounded-lg font-medium hover:bg-blue-800">
                        Filter
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('dupak.validasi.index') }}" class="px-4 py-2.5 text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8"></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pengajuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dosen</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total AK</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Progress</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pengajuanList as $pengajuan)
                        @php
                        $details = isset($allDetailsMap[$pengajuan->id]) ? collect($allDetailsMap[$pengajuan->id]) : collect([]);
                        $totalAk = $details->sum('angka_kredit_total');
                        $komponenCount = $details->count();
                        $prog = $progressMap[$pengajuan->id] ?? ['evaluated' => false, 'percent' => 0, 'totalDetail' => 0, 'evaluatedCount' => 0];
                        
                        $statusBadge = [
                            'Pending' => 'bg-gray-100 text-gray-600',
                            'Diajukan' => 'bg-blue-100 text-blue-700',
                            'Revisi' => 'bg-amber-100 text-amber-700',
                            'Diterima' => 'bg-emerald-100 text-emerald-700',
                            'Ditolak' => 'bg-rose-100 text-rose-700',
                        ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <button type="button" onclick="toggleDetail({{ $pengajuan->id }})" class="w-7 h-7 flex items-center justify-center rounded hover:bg-blue-50 text-gray-400 hover:text-blue-600">
                                    <i class="fas fa-chevron-right text-xs transition-transform" id="icon-{{ $pengajuan->id }}"></i>
                                </button>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">#{{ $pengajuan->id }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5">{{ $pengajuan->created_at->format('d M Y') }}</span>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium {{ $statusBadge[$pengajuan->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $pengajuan->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900">{{ $pengajuan->nama_dosen }}</div>
                                <!-- update : nidn mengambil dari dosen model -->
                                <div class="text-sm text-gray-500">NIDN {{ $pengajuan->dosen?->nidn }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-bold bg-blue-50 text-blue-700">
                                    {{ number_format($totalAk, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $komponenCount }} item</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="flex-1 max-w-[80px] h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $prog['evaluated'] ? 'bg-emerald-500' : 'bg-amber-500' }} rounded-full" style="width: {{ $prog['percent'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium {{ $prog['evaluated'] ? 'text-emerald-600' : 'text-amber-600' }} min-w-[45px]">
                                        {{ $prog['evaluated'] ? '100%' : $prog['percent'] . '%' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('dupak.validasi.show', $pengajuan->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium {{ $prog['evaluated'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }} hover:opacity-80">
                                    {{ $prog['evaluated'] ? 'Selesai' : 'Review' }}
                                </a>
                            </td>
                        </tr>
                        {{-- Expandable Detail --}}
                        <tr class="hidden" id="details-{{ $pengajuan->id }}">
                            <td colspan="7" class="px-4 py-0 bg-gray-50">
                                <div class="ml-10 my-2">
                                    <div class="bg-white rounded-lg border border-gray-100 overflow-hidden">
                                        <table class="min-w-full">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Komponen</th>
                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Deskripsi</th>
                                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">AK</th>
                                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                @forelse($details as $detail)
                                                @php
                                                $evalStatus = $detail->evaluation['status_evaluasi'] ?? null;
                                                $displayStatus = $evalStatus ?? $detail->status;
                                                
                                                $statusColors = [
                                                    'OK' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                    'Doubt' => 'bg-amber-50 text-amber-600 border-amber-200',
                                                    'Fake' => 'bg-rose-50 text-rose-600 border-rose-200',
                                                    'pending' => 'bg-gray-50 text-gray-500 border-gray-200',
                                                    'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                    'revision' => 'bg-amber-50 text-amber-600 border-amber-200',
                                                    'rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
                                                ];
                                                $label = $evalStatus ? 'TPAK: ' . $evalStatus : ucfirst($detail->status);
                                                @endphp
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3">
                                                        <span class="text-sm font-medium text-gray-800">{{ Str::limit($detail->komponen->nama ?? 'Komponen #' . $detail->idKomponen, 35) }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-sm text-gray-500">{{ Str::limit($detail->deskripsi_kegiatan, 60) }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="text-sm font-semibold text-gray-700">{{ number_format($detail->angka_kredit_total, 2) }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-medium border {{ $statusColors[$displayStatus] ?? 'bg-gray-50 text-gray-500 border-gray-200' }}">
                                                            {{ $label }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                                        Tidak ada detail
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400 mb-2">
                                    <i class="fas fa-inbox text-4xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Tidak ada tugas validasi</p>
                                <p class="text-sm text-gray-400 mt-1">Tugas akan muncul saat ada penugasan TPAK baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengajuanList->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $pengajuanList->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleDetail(id) {
    var row = document.getElementById('details-' + id);
    var icon = document.getElementById('icon-' + id);
    if (row.classList.contains('hidden')) {
        row.classList.remove('hidden');
        icon.style.transform = 'rotate(90deg)';
    } else {
        row.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection
