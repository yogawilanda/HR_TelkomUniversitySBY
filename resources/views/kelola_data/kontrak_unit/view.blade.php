@php
    $active_sidebar = 'Kontrak Unit';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Detail Kontrak Unit</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Informasi kontrak unit dan pelaporan kinerja</span>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white rounded-lg shadow p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Informasi Kontrak Unit</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Kontrak Manajemen</label>
                    <p class="text-gray-900">{{ $kontrakUnit->kontrakManajemen->nama ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Nama Unit</label>
                    <p class="text-gray-900">{{ $kontrakUnit->nama_unit }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600">Pekerjaan</label>
                    <p class="text-gray-900">{{ $kontrakUnit->pekerjaan }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tipe Kontrak</label>
                    <p class="text-gray-900">{{ $kontrakUnit->kontrak_type ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Target</label>
                    <p class="text-gray-900">{{ $kontrakUnit->jumlah ?? 0 }} {{ $kontrakUnit->result ?? '' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Waktu (Menit)</label>
                    <p class="text-gray-900">{{ $kontrakUnit->waktu_minutes ?? 0 }} menit</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Bobot</label>
                    <p class="text-gray-900">{{ $kontrakUnit->bobot ?? 0 }}</p>
                </div>
            </div>
        </div>

        @if($kontrakUnit->kinerjaUnit)
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Status Kinerja Unit</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <p>
                        <span class="px-3 py-1 rounded text-sm 
                            @if($kontrakUnit->kinerjaUnit->status === 'completed') bg-green-100 text-green-800 
                            @elseif($kontrakUnit->kinerjaUnit->status === 'in_progress') bg-blue-100 text-blue-800 
                            @else bg-gray-100 text-gray-800 
                            @endif">
                            {{ ucfirst($kontrakUnit->kinerjaUnit->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Realisasi (%)</label>
                    <p class="text-gray-900">{{ $kontrakUnit->kinerjaUnit->realisasi_percent ?? 0 }}%</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Total Realisasi Jumlah</label>
                    <p class="text-gray-900">{{ $kontrakUnit->kinerjaUnit->total_realisasi_jumlah ?? 0 }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Total Realisasi Waktu</label>
                    <p class="text-gray-900">{{ $kontrakUnit->kinerjaUnit->total_realisasi_waktu_minutes ?? 0 }} menit</p>
                </div>
                @if($kontrakUnit->kinerjaUnit->catatan)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600">Catatan</label>
                    <p class="text-gray-900">{{ $kontrakUnit->kinerjaUnit->catatan }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Pelaporan Pekerjaan</h2>
                <a href="{{ route('manage.kontrak-unit.isi-pelaporan', $kontrakUnit->kinerjaUnit->id) }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                    <i class="bi bi-plus"></i> Tambah Pelaporan
                </a>
            </div>
            
            @if($kontrakUnit->kinerjaUnit->pelaporanPekerjaan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Realisasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">TPA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($kontrakUnit->kinerjaUnit->pelaporanPekerjaan as $pelaporan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pelaporan->created_at->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4">{{ Str::limit($pelaporan->realisasi, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pelaporan->realisasi_jumlah ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pelaporan->realisasi_waktu_minutes ?? 0 }} menit</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pelaporan->tpa->pegawai->nama_lengkap ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded text-xs 
                                            @if($pelaporan->status === 'approved') bg-green-100 text-green-800 
                                            @elseif($pelaporan->status === 'rejected') bg-red-100 text-red-800 
                                            @else bg-yellow-100 text-yellow-800 
                                            @endif">
                                            {{ ucfirst($pelaporan->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada pelaporan pekerjaan</p>
            @endif
        </div>
        @endif

        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Pegawai yang Ditugaskan</h2>
                <a href="{{ route('manage.kontrak-unit.assign', $kontrakUnit->id) }}" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                    <i class="bi bi-person-plus"></i> Assign Pegawai
                </a>
            </div>
            
            @if($kontrakUnit->pegawai->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($kontrakUnit->pegawai as $pegawai)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pegawai->nama_lengkap }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                            {{ ucfirst($pegawai->pivot->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pegawai->pivot->tanggal_mulai ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $pegawai->pivot->catatan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada pegawai yang ditugaskan</p>
            @endif
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('manage.kontrak-unit.list') }}" 
                class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                Kembali
            </a>
            <a href="{{ route('manage.kontrak-unit.edit', $kontrakUnit->id) }}" 
                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition">
                Edit
            </a>
        </div>
    </div>
@endsection
