@php
    $active_sidebar = 'Target Kinerja';
    $isAdmin = auth()->user()->is_admin;
    $role = auth()->user()->role;
@endphp

@extends('kinerja_pegawai.base')

@section('page-name', (isset($role) && $role === 'pegawai' && !$isAdmin) ? 'Tugas Kinerja Saya' : 'Daftar Target Kinerja (Individu)')

@section('content-base')
<div class="mb-4">
    <p class="text-sm text-gray-500 italic">
        @if(isset($role) && $role === 'pegawai' && !$isAdmin)
            Daftar pekerjaan yang harus Anda selesaikan hari ini.
        @else
            Kelola set target kinerja individu dan progres capaian pegawai.
        @endif
    </p>
</div>
<div class="w-full max-w-7xl mx-auto space-y-8">
    
    @if(!(isset($role) && $role === 'pegawai' && !$isAdmin))
    {{-- Leaderboard Section --}}
    <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-trophy text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-700 uppercase tracking-tight">Top 5 Kinerja Bulan Ini</h3>
                <p class="text-xs text-gray-400 italic">Kontribusi waktu terbaik periode {{ now()->translatedFormat('F Y') }}.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @foreach($leaderboard as $index => $row)
                <div class="relative flex flex-col items-center p-6 rounded-2xl bg-gray-50 border border-gray-100 transition-all hover:bg-white hover:shadow-md group">
                    <div class="absolute top-3 left-4">
                        @if($index == 0)
                            <i class="fa-solid fa-crown text-yellow-400 text-xl"></i>
                        @else
                            <span class="text-sm font-black text-gray-200">#{{ $index + 1 }}</span>
                        @endif
                    </div>
                    
                    <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors border-4 border-white shadow-sm">
                        {{ strtoupper(substr($row->pelapor?->nama_lengkap ?? '?', 0, 1)) }}
                    </div>
                    
                    <p class="text-xs font-bold text-gray-800 text-center line-clamp-1 mb-2">{{ $row->pelapor?->nama_lengkap ?? '-' }}</p>
                    <div class="flex flex-col items-center bg-white px-3 py-1 rounded-full border border-gray-100">
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                            {{ round($row->total_minutes / 60, 1) }} Jam
                        </p>
                    </div>
                </div>
            @endforeach
            
            @if($leaderboard->isEmpty())
                <div class="col-span-full py-10 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-400 italic font-medium">Belum ada data kinerja untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-gray-100 gap-4">
            <h3 class="text-lg font-semibold text-gray-700">Daftar Pekerjaan</h3>
            <div class="flex flex-wrap gap-2">
                @if(!(isset($role) && $role === 'pegawai' && !$isAdmin))
                <a href="{{ route('manage.laporan.print') }}" target="_blank" 
                    class="bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium py-2 px-4 rounded-md transition duration-150 inline-flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                </a>
                <a href="{{ route('manage.laporan.export') }}" 
                    class="bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 text-sm font-medium py-2 px-4 rounded-md transition duration-150 inline-flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </a>
                @endif
                <a href="{{ route('manage.target-kinerja.harian.input') }}" 
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-150 inline-flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Target
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        
        @if(isset($role) && $role === 'pegawai' && !$isAdmin && $items->isEmpty())
            <div class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <i class="fa-solid fa-clipboard-list text-5xl text-gray-200 mb-4 block"></i>
                <p class="text-gray-400 font-medium italic">Belum ada tugas yang di-assign untuk Anda hari ini.</p>
            </div>
        @else
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Pekerjaan</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Master KPI</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Output</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Waktu</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $i => $it)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $it->pekerjaan }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ $it->start ? \Carbon\Carbon::parse($it->start)->format('d M Y') : '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($it->targetKinerja)
                                        <a href="{{ route('manage.target-kinerja.detail', $it->targetKinerja->id) }}" class="text-xs font-bold text-blue-600 hover:underline">
                                            {{ $it->targetKinerja->nama_kpi }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-300 italic">No KPI Linked</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-gray-800">{{ $it->jumlah ?? '0' }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase ml-0.5">Item</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-gray-800">{{ $it->waktu_minutes ?? '0' }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase ml-0.5">Min</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $status = 'pending';
                                        if (isset($role) && $role === 'pegawai' && !$isAdmin) {
                                            $pivot = $it->pegawai->where('id', auth()->id())->first();
                                            if ($pivot) { $status = $pivot->pivot->status; }
                                        }
                                    @endphp
                                    @if($status === 'approved' || $status === 'completed')
                                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-green-100 text-green-700 border border-green-200">Completed</span>
                                    @elseif($status === 'rejected' || $status === 'cancelled')
                                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-red-100 text-red-700 border border-red-200">Cancelled</span>
                                    @else
                                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('manage.target-kinerja.harian.view', $it->id) }}" 
                                            class="w-8 h-8 rounded-md border border-gray-200 bg-white flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-all" title="View Detail">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        @if(!(isset($role) && $role === 'pegawai' && !$isAdmin))
                                        <a href="{{ route('manage.target-kinerja.harian.edit', $it->id) }}" 
                                            class="w-8 h-8 rounded-md border border-gray-200 bg-white flex items-center justify-center text-amber-600 hover:bg-amber-50 transition-all" title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('manage.target-kinerja.harian.isi', $it->id) }}" 
                                            class="w-8 h-8 rounded-md border border-gray-200 bg-white flex items-center justify-center text-green-600 hover:bg-green-50 transition-all" title="Isi Laporan Kinerja">
                                            <i class="fa-solid fa-file-pen text-xs"></i>
                                        </a>
                                        @if(!(isset($role) && $role === 'pegawai' && !$isAdmin))
                                        <a href="{{ route('manage.target-kinerja.harian.assign', $it->id) }}" 
                                            class="w-8 h-8 rounded-md border border-gray-200 bg-white flex items-center justify-center text-indigo-600 hover:bg-indigo-50 transition-all" title="Assign Pegawai">
                                            <i class="fa-solid fa-user-plus text-xs"></i>
                                        </a>
                                        <form action="{{ route('manage.target-kinerja.harian.destroy', $it->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-md border border-gray-200 bg-white flex items-center justify-center text-red-600 hover:bg-red-50 transition-all" 
                                                onclick="return confirm('Hapus target pekerjaan ini?')" title="Hapus">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-8">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
