@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard DUPAK
        </a>

        <div class="bg-white shadow rounded-t-lg p-6 pb-0">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-6">
                <div>
                    <h1 class="text-2xl font-semibold mb-2">Dashboard TPAK</h1>
                    <p class="text-sm text-gray-500 font-medium">Kelola validasi angka kredit dosen yang ditugaskan kepada Anda.</p>
                </div>
                
                <div class="flex gap-4">
                    <div class="p-4 border rounded-lg border-blue-100 bg-blue-50 shadow-sm text-center min-w-[120px]">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-widest">Tugas Aktif</p>
                        <p class="text-xl font-bold text-blue-900">{{ $detailPengajuanTPAK->total() }}</p>
                    </div>
                    <div class="p-4 border rounded-lg border-emerald-100 bg-emerald-50 shadow-sm text-center min-w-[120px]">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Review Selesai</p>
                        <p class="text-xl font-bold text-emerald-900">{{ $selesaiCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-sm flex items-center animate-fade-in">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
        @endif

            <div class="border-b border-gray-200 pb-6">
                <form method="GET" action="{{ route('dupak.validasi.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[280px] relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama dosen atau pengajuan..."
                            class="w-full pl-11 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                    </div>
                    <select name="status" class="min-w-[140px] p-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm appearance-none">
                        <option value="">Semua Status</option>
                        @foreach(['Pending', 'Diajukan', 'Revisi', 'Diterima', 'Ditolak'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-6 py-2 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 shadow-sm transition-all whitespace-nowrap">
                        Filter
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('dupak.validasi.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all whitespace-nowrap">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Info Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dosen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Komponen</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">AK</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($detailPengajuanTPAK as $item)
                        @php
                        $prog = $progressMap[$item->id] ?? ['evaluated' => false, 'percent' => 0];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">#{{ $item->pengajuan_id }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $item->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item->pengajuan->nama_dosen ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">NIDN {{ $item->pengajuan->idDosen ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium truncate" title="{{ $item->komponen->nama ?? 'Komponen #' . $item->idKomponen }}">
                                    {{ Str::limit($item->komponen->nama ?? 'Komponen #' . $item->idKomponen, 40) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($item->deskripsi_kegiatan, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    {{ $item->angka_kredit_murni ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="w-16 mx-auto">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $prog['evaluated'] ? 'emerald' : 'amber' }}-500 h-2 rounded-full transition-all" style="width: {{ $prog['percent'] }}%"></div>
                                    </div>
                                    <div class="text-xs font-medium {{ $prog['evaluated'] ? 'text-emerald-700' : 'text-amber-700' }} mt-1">
                                        {{ $prog['evaluated'] ? 'Selesai' : $prog['percent'] . '%' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('dupak.validasi.show', $item->pengajuan_id) }}"
                                   class="inline-flex items-center px-3 py-1.5 border text-sm font-medium {{ $prog['evaluated'] ? 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100' }} rounded-lg transition duration-150 ease-in-out">
                                    {{ $prog['evaluated'] ? 'Selesai' : 'Review' }}
                                    <i class="{{ $prog['evaluated'] ? 'fas fa-check ml-1' : 'fas fa-arrow-right ml-1' }}"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">Tidak ada tugas validasi</p>
                                <p class="text-sm text-gray-400 mt-1">Tugas akan muncul saat ada penugasan TPAK baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($detailPengajuanTPAK->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $detailPengajuanTPAK->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

            <div class="bg-white shadow rounded-b-lg p-6 pt-0">
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection