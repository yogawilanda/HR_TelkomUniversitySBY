@php
    $active_sidebar = 'KM & Sasaran Mutu';
@endphp

@extends('kinerja_pegawai.base')

@section('page-name')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Laporan KM & Sasaran Mutu</h2>
        <p class="text-sm text-gray-500">Monitoring capaian indikator dan log pengerjaan harian seluruh unit.</p>
    </div>
@endsection

@section('content-base')
<div class="w-full max-w-7xl mx-auto space-y-10 pb-10">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    
    {{-- Tabel 1: Daftar Indikator KM & Sasaran Mutu --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">Capaian Indikator KM & SM</h3>
                <p class="text-xs text-gray-400 italic">Progress realisasi terhadap target triwulan.</p>
            </div>
            {{-- Global Filter --}}
            <form method="GET" class="flex items-center gap-2">
                <select name="user_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 w-48">
                    <option value="">Semua Pegawai</option>
                    @foreach ($allUsers as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->nama_lengkap }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-150 inline-flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Indikator</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Unit</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">TW I</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">TW II</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">TW III</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">TW IV</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Progress</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($targetKinerjaList as $target)
                        @php
                            $totalTarget = $target->tw1_target + $target->tw2_target + $target->tw3_target + $target->tw4_target;
                            $realisasi = 0;
                            foreach($target->targetHarian as $harian) {
                                $realisasi += $harian->pelaporan()->where('status', 'approved')->sum('approved_jumlah');
                            }
                            $progress = $totalTarget > 0 ? min(round(($realisasi / $totalTarget) * 100, 1), 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-gray-800 leading-tight">{{ $target->nama_kpi }}</p>
                                    <span class="text-[9px] font-black px-2 py-0.5 rounded bg-blue-50 text-blue-600 uppercase border border-blue-100">{{ $target->jenis ?? 'KM' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $target->unit->nama_unit ?? '-' }}</td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">{{ number_format($target->tw1_target) }}</td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">{{ number_format($target->tw2_target) }}</td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">{{ number_format($target->tw3_target) }}</td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">{{ number_format($target->tw4_target) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="text-[10px] font-black text-blue-600">{{ $progress }}%</span>
                                    <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden border border-gray-50 shadow-inner">
                                        <div class="bg-blue-600 h-full transition-all duration-700" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel 2: Log Kinerja Harian Terkini --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">Log Kinerja Harian Terkini</h3>
            <p class="text-xs text-gray-400 italic">Daftar laporan pekerjaan yang masuk dari seluruh pegawai.</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Pekerjaan / Pelapor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Realisasi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Output</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Waktu (Min)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pelaporanItems as $it)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ $it->targetHarian->pekerjaan ?? '-' }}</p>
                                <p class="text-[10px] text-blue-600 font-bold uppercase mt-1">Oleh: {{ $it->pembuat_laporan->nama_lengkap ?? 'Unknown' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span title="{{ $it->realisasi }}">{{ Str::limit($it->realisasi, 50) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-black text-gray-700">
                                {{ $it->approved_jumlah ?? $it->realisasi_jumlah }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">
                                {{ $it->approved_waktu_minutes ?? $it->realisasi_waktu_minutes }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight 
                                    {{ $it->status == 'approved' ? 'bg-green-100 text-green-700 border border-green-200' : ($it->status == 'rejected' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-amber-100 text-amber-700 border border-amber-200') }}">
                                    {{ $it->status ?? 'pending' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                                Belum ada log kinerja harian yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-100 bg-gray-50">
            {{ $pelaporanItems->links() }}
        </div>
    </div>
</div>
@endsection
