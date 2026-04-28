@extends('layouts.app')

@section('content')
<div class="pt-16 p-6">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Validasi TPAK</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Kelola dan validasi pengajuan DUPAK yang ditugaskan kepada Anda.</p>
        </div>

        @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                        <i class="fas fa-tasks text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Tugas Aktif</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $detailPengajuanTPAK->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Pending Review</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $detailPengajuanTPAK->where('pengajuan.status', 'pending')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Selesai</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $selesaiCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Rata-rata Score</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $avgScore > 0 ? $avgScore . '%' : '--' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filters -->
        <form method="GET" action="{{ route('dupak.validasi.index') }}">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama dosen atau pengajuan..." class="pl-10 pr-4 py-2 w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select name="status" class="px-4 py-2 rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                        <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
                        Filter
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('dupak.validasi.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition text-sm flex items-center">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                    @endif
                </div>
            </div>
        </div>
        </form>

        <!-- Tasks Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-list mr-2 text-blue-600"></i>
                    Daftar Pengajuan untuk Divalidasi
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                No Pengajuan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nama Dosen
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Komponen / Deskripsi
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Angka Kredit
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Status Pengajuan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Deadline
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Progress
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($detailPengajuanTPAK as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">D{{ $item->id }} (P#{{ $item->pengajuan_id }})</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->pengajuan->nama_dosen ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">Fakultas Teknik</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->komponen->nama ?? $item->idKomponen }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ Str::limit($item->deskripsi_kegiatan, 80) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    {{ $item->angka_kredit_murni ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $item->pengajuan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($item->pengajuan->status ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $item->created_at->addDays(7)->format('d M Y') }}
                            </td>
                            @php
                                $prog = $progressMap[$item->id] ?? ['evaluated' => false, 'percent' => 0];
                            @endphp
                            <td class="px-6 py-4 text-center">
                                <div class="w-16 h-2 bg-gray-200 rounded-full mx-auto">
                                    <div class="h-2 {{ $prog['evaluated'] ? 'bg-green-500' : 'bg-blue-500' }} rounded-full transition-all duration-500" style="width: {{ $prog['percent'] }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    @if($prog['evaluated'])
                                        <span class="text-green-600 font-semibold"><i class="fas fa-check mr-1"></i>Sudah</span>
                                    @else
                                        <span class="text-yellow-600">Belum</span>
                                    @endif
                                </p>
                            </td>
                            <td class="px-6 py-4 text-right space-y-1">
                                <a href="{{ route('dupak.validasi.show', $item->pengajuan_id) }}" class="inline-flex items-center px-3 py-1.5 {{ $prog['evaluated'] ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white text-xs font-bold rounded-lg transition">
                                    <i class="fas {{ $prog['evaluated'] ? 'fa-check' : 'fa-edit' }} mr-1"></i> {{ $prog['evaluated'] ? 'Lihat' : 'Review' }} Pengajuan
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                                    <h3 class="text-lg font-bold mb-2">Belum ada tugas validasi</h3>
                                    <p class="text-sm">Tunggu penugasan dari admin SDM.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                {{ $detailPengajuanTPAK->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
