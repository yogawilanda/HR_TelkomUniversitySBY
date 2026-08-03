@extends('layouts.app')

@section('content')
<div class="py-6" x-data="{ search: '', statusFilter: 'all' }">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- Navigasi Kembali --}}
        <a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-4 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dasbor
        </a>

        {{-- Card Utama --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Daftar Eligibilitas JFA Dosen</h1>
                    <p class="text-sm text-gray-500 mt-1">Monitoring kelayakan masa jabatan dosen untuk pengajuan DUPAK (Minimal 2 Tahun TMT).</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-3 py-1.5 bg-blue-50 text-blue-800 rounded-lg border border-blue-200">
                        Total Dosen: {{ $jfas->total() }}
                    </span>
                </div>
            </div>

            {{-- Filter & Search Bar --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" x-model="search" placeholder="Cari nama dosen di halaman ini..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-900 focus:border-blue-900">
                </div>

                <div class="w-full sm:w-48">
                    <select x-model="statusFilter" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-900 focus:border-blue-900">
                        <option value="all">Semua Status</option>
                        <option value="eligible">Eligible</option>
                        <option value="belum">Belum Eligible</option>
                        <option value="gb">Guru Besar (Max)</option>
                    </select>
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-900 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                            <th scope="col" class="px-6 py-3 text-left">Nama Dosen & NIDN</th>
                            <th scope="col" class="px-6 py-3 text-left">Jabatan Saat Ini</th>
                            <th scope="col" class="px-6 py-3 text-center">TMT Mulai</th>
                            <th scope="col" class="px-6 py-3 text-center">Masa Kerja / Kekurangan Waktu</th>
                            <th scope="col" class="px-6 py-3 text-center">Status Eligibilitas</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse ($jfas as $item)
                            @php
                                $namaJfa = strtolower($item->jfa?->nama_jabatan ?? '');
                                $kodeJfa = strtoupper($item->jfa?->kode ?? '');
                                $isMaxJfa = str_contains($namaJfa, 'guru besar') || str_contains($namaJfa, 'profesor') || $kodeJfa === 'GB';
                                $isExpired = !is_null($item->tmt_selesai) && \Carbon\Carbon::parse($item->tmt_selesai)->isPast();
                                
                                $tmtMulai = $item->tmt_mulai ? \Carbon\Carbon::parse($item->tmt_mulai) : null;
                                $targetEligible = $tmtMulai ? $tmtMulai->copy()->addYears(2) : null;
                                
                                // Cek Eligibilitas
                                $isEligible = $tmtMulai && !$isExpired && !$isMaxJfa && now()->gte($targetEligible);
                                
                                // Masa kerja riil
                                $masaKerja = $tmtMulai ? $tmtMulai->diff(now()) : null;
                                
                                // Kekurangan waktu jika belum 2 tahun
                                $sisaWaktu = ($targetEligible && now()->lt($targetEligible)) ? now()->diff($targetEligible) : null;

                                // Kategori untuk Filter Alpine.js
                                $statusCategory = $isMaxJfa ? 'gb' : ($isEligible ? 'eligible' : 'belum');
                                $namaDosen = $item->dosen?->pegawai?->nama_lengkap ?? '-';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-show="(statusFilter === 'all' || statusFilter === '{{ $statusCategory }}') && ('{{ strtolower($namaDosen) }}'.includes(search.toLowerCase()))">
                                
                                <td class="px-4 py-4 text-center text-gray-500 font-medium">
                                    {{ $loop->iteration + ($jfas->currentPage() - 1) * $jfas->perPage() }}
                                </td>
                                
                                {{-- Nama Dosen & Identitas --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">{{ $namaDosen }}</div>
                                    <div class="text-xs text-gray-500">NIDN: {{ $item->dosen?->nidn ?? '-' }}</div>
                                </td>

                                {{-- JFA --}}
                                <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">
                                    {{ $item->jfa?->nama_jabatan ?? '-' }}
                                </td>

                                {{-- TMT Mulai --}}
                                <td class="px-6 py-4 text-center text-gray-600 whitespace-nowrap">
                                    {{ $tmtMulai ? $tmtMulai->format('d-m-Y') : '-' }}
                                </td>

                                {{-- Masa Kerja / Kekurangan Waktu --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap text-xs">
                                    @if($isMaxJfa)
                                        <span class="text-gray-400 italic">Jenjang Maksimal</span>
                                    @elseif(!$tmtMulai)
                                        <span class="text-gray-400">-</span>
                                    @elseif($isEligible)
                                        <div class="font-semibold text-green-700">
                                            <i class="fas fa-check mr-1"></i> Terpenuhi
                                        </div>
                                        <div class="text-[11px] text-gray-500">
                                            ({{ $masaKerja->y > 0 ? $masaKerja->y . ' thn ' : '' }}{{ $masaKerja->m }} bln {{ $masaKerja->d }} hr)
                                        </div>
                                    @elseif($sisaWaktu)
                                        <div class="text-red-600 font-medium">
                                            Kurang {{ $sisaWaktu->y > 0 ? $sisaWaktu->y . ' thn ' : '' }}{{ $sisaWaktu->m }} bln {{ $sisaWaktu->d }} hr
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            Target: {{ $targetEligible->format('d-m-Y') }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Status Badges --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($isMaxJfa)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-300">
                                            <i class="fas fa-award mr-1.5 text-gray-500"></i> Maksimal (GB)
                                        </span>
                                    @elseif($isExpired)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                            <i class="fas fa-exclamation-circle mr-1.5"></i> Masa Jabatan Selesai
                                        </span>
                                    @elseif($isEligible)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
                                            <i class="fas fa-check-circle mr-1.5"></i> Eligible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                            <i class="fas fa-clock mr-1.5"></i> Belum Eligible
                                        </span>
                                    @endif
                                </td>

                                {{-- Quick Action --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap text-xs">
                                    @if($isEligible)
                                        <a href="{{ route('dupak.pengajuan.create', ['userId' => $item->dosen?->pegawai?->user_id]) }}" 
                                           class="inline-flex items-center px-2.5 py-1.5 bg-blue-900 text-white font-medium rounded hover:bg-blue-950 transition-colors">
                                            <i class="fas fa-paper-plane mr-1"></i> Ajukan DUPAK
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <i class="fas fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                    Data eligibilitas dosen belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Link Pagination --}}
            <div class="mt-4">
                {{ $jfas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection