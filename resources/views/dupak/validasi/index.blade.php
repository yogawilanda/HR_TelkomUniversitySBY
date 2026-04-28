@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pb-12 pt-16">
    <div class="max-w-[1400px] mx-auto px-6">
        <a href="{{ route('dupak.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>

        {{-- Header & Stats --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard TPAK</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Kelola validasi angka kredit dosen yang ditugaskan kepada Anda.</p>
            </div>

            <div class="flex gap-3">
                <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tugas Aktif</p>
                    <p class="text-xl font-black text-blue-600">{{ $detailPengajuanTPAK->total() }}</p>
                </div>
                <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Review Selesai</p>
                    <p class="text-xl font-black text-emerald-500">{{ $selesaiCount }}</p>
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

        {{-- Filter & Search --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('dupak.validasi.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[300px] relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama dosen atau nomor pengajuan..."
                        class="w-full pl-9 pr-4 py-1.5 bg-gray-50 border border-gray-100 rounded-lg text-[12px] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400">
                </div>

                <div class="relative">
                    <select name="status" class="bg-gray-50 border border-gray-100 rounded-lg text-[12px] font-bold py-1.5 pl-3 pr-8 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer text-gray-600 tracking-tight leading-tight">
                        <option value="">Status: Semua</option>
                        @foreach(['Pending', 'Diajukan', 'Revisi', 'Diterima', 'Ditolak'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
                </div>

                <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white rounded-lg text-[12px] font-bold hover:bg-blue-600 transition-all shadow-sm active:scale-95">
                    Filter
                </button>

                @if(request('search') || request('status'))
                <a href="{{ route('dupak.validasi.index') }}" class="text-sm font-bold text-gray-400 hover:text-rose-500 transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Info Pengajuan</th>
                        <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Dosen Pengaju</th>
                        <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Komponen Utama</th>
                        <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">AK</th>
                        <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Progress</th>
                        <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($detailPengajuanTPAK as $item)
                    @php
                    $prog = $progressMap[$item->id] ?? ['evaluated' => false, 'percent' => 0];
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="text-xs font-black text-blue-600 mb-1">#{{ $item->pengajuan_id }}</div>
                            <div class="text-[10px] font-bold text-gray-400 flex items-center">
                                <i class="far fa-calendar-alt mr-1.5"></i>
                                {{ $item->created_at->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-gray-900 group-hover:text-blue-700">{{ $item->pengajuan->nama_dosen ?? 'N/A' }}</div>
                            <div class="text-[11px] text-gray-500 font-medium">NIDN: {{ $item->pengajuan->idDosen ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-xs font-bold text-gray-700 truncate max-w-[250px]">
                                {{ $item->komponen->nama ?? 'Komponen #' . $item->idKomponen }}
                            </div>
                            <div class="text-[10px] text-gray-400 italic truncate max-w-[200px]" title="{{ $item->deskripsi_kegiatan }}">
                                {{ $item->deskripsi_kegiatan }}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-[11px] font-black rounded-lg">
                                {{ $item->angka_kredit_murni ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $prog['evaluated'] ? 'bg-emerald-500' : 'bg-amber-400' }} transition-all" style="width: {{ $prog['percent'] }}%"></div>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-tighter mt-1.5 {{ $prog['evaluated'] ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $prog['evaluated'] ? 'Selesai' : 'Pending' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <a href="{{ route('dupak.validasi.show', $item->pengajuan_id) }}"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl {{ $prog['evaluated'] ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white' : 'bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white' }} transition-all shadow-sm">
                                <i class="fas {{ $prog['evaluated'] ? 'fa-check-double' : 'fa-chevron-right' }} text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="opacity-20 mb-4">
                                <i class="fas fa-folder-open text-5xl"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tidak ada tugas ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($detailPengajuanTPAK->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
                {{ $detailPengajuanTPAK->links() }}
            </div>
            @endif
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