@php
    $active_sidebar = 'Reporting Kinerja';
@endphp

@extends('kinerja_pegawai.base')

@section('page-name', 'Reporting Kinerja Pegawai')

@section('content-base')
<div class="mb-4">
    <p class="text-sm text-gray-500 italic">Rekapitulasi efektivitas harian, bulanan, dan tahunan.</p>
</div>
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 bg-white rounded-lg shadow-sm border border-gray-100">
    {{-- Filter Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-gray-100 gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Filter Laporan</h3>
            <p class="text-xs text-gray-400 italic">Gunakan filter untuk menyaring data spesifik.</p>
        </div>
        <form action="{{ route('manage.laporan.reporting') }}" method="GET" class="flex flex-wrap gap-2">
            @if(auth()->user()->role !== 'pegawai' || auth()->user()->is_admin)
                <select name="nama" class="text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                    <option value="">Semua Pegawai</option>
                    @foreach($users as $user)
                        <option value="{{ $user->nama_lengkap }}" {{ $nama == $user->nama_lengkap ? 'selected' : '' }}>
                            {{ $user->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            @else
                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm font-bold text-gray-700">
                    {{ auth()->user()->nama_lengkap }}
                </div>
            @endif
            <select name="bulan" class="text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ (int)$bulan == (int)$m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate((int)$tahun, (int)$m, 1)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="tahun" class="text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ (int)$tahun == (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-150">
                Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        {{-- TABEL 2: REPORT BULANAN --}}
        <div class="bg-gray-50 rounded-xl border border-gray-200 flex flex-col h-[400px]">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Report Bulanan</h3>
                <span class="text-xs text-gray-400 font-medium">Bulan: {{ \Carbon\Carbon::createFromDate((int)$tahun, (int)$bulan, 1)->translatedFormat('F Y') }}</span>
            </div>
            <div class="overflow-auto flex-grow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Nama</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Efektivitas (%)</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dataBulanan as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->pelapor->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center font-bold">{{ number_format($item->efektivitas * 100, 1) }}%</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $item->color_class }}">
                                    {{ $item->status_teks }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 italic">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 3: REPORT TAHUNAN --}}
        <div class="bg-gray-50 rounded-xl border border-gray-200 flex flex-col h-[400px]">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Report Tahunan</h3>
                <span class="text-xs text-gray-400 font-medium">Tahun: {{ $tahun }}</span>
            </div>
            <div class="overflow-auto flex-grow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Nama</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Efektivitas (%)</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dataTahunan as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->pelapor->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center font-bold">{{ number_format($item->efektivitas * 100, 1) }}%</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $item->color_class }}">
                                    {{ $item->status_teks }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 italic">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 1: REPORT HARIAN --}}
        <div class="xl:col-span-2 bg-gray-50 rounded-xl border border-gray-200 flex flex-col h-[500px]">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Detail Report Harian</h3>
                <span class="text-xs text-gray-400 font-medium">Periode: {{ \Carbon\Carbon::createFromDate((int)$tahun, (int)$bulan, 1)->translatedFormat('F Y') }}</span>
            </div>
            <div class="overflow-auto flex-grow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Nama</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Tanggal</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Input (Min)</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Validasi (Min)</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Efektivitas (%)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dataHarian as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->pelapor->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center">{{ $item->waktu_pengerjaan }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center">{{ $item->waktu_validasi_atasan }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold {{ $item->efektivitas > 1 ? 'text-red-600' : ($item->efektivitas >= 0.75 ? 'text-green-600' : 'text-yellow-600') }}">
                                    {{ number_format($item->efektivitas * 100, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 italic">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
