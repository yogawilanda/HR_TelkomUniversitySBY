@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp

<x-dupak.sidebar />

<div class="mt-16 md:ml-64 p-6">

    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Penunjukan Tim Penilai Angka Kredit (TPAK)</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola penugasan dosen penilai untuk setiap pengajuan DUPAK.</p>
        </div>

        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm" role="alert">
            <p class="font-bold">Berhasil</p>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-sm" role="alert">
            <p class="font-bold">Gagal</p>
            <p>{{ session('error') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Form Penunjukan -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 bg-blue-900">
                        <h2 class="text-lg font-semibold text-white">Form Penunjukan TPAK</h2>
                    </div>
                    <form action="{{ route('dupak.penunjukan_tpak.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        
                        <div>
                            <label for="pengajuan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Pengajuan DUPAK</label>
                            <select name="pengajuan_id" id="pengajuan_id" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">-- Pilih Pengajuan --</option>
                                @foreach($pengajuan as $p)
                                    <option value="{{ $p->id }}">
                                        #{{ $p->id }} - {{ $p->nama_dosen }} ({{ $p->status }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pengajuan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="idDosenTpak" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Dosen TPAK</label>
                            <select name="idDosenTpak" id="idDosenTpak" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">-- Pilih Dosen Penilai --</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            @error('idDosenTpak') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="catatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Penugasan</label>
                            <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Mohon review dokumen penelitian..."></textarea>
                            @error('catatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded transition duration-200">
                            <i class="fas fa-user-plus mr-2"></i> Tunjuk TPAK
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat Penunjukan -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Penugasan Aktif</h2>
                        <form action="{{ route('dupak.penunjukan_tpak.index') }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dosen..." class="pl-8 pr-4 py-1 text-sm rounded-lg border-gray-300 dark:bg-gray-600 dark:text-white">
                            <i class="fas fa-search absolute left-2.5 top-2 text-gray-400"></i>
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pengaju</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">TPAK (Penilai)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tgl Penunjukan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($penunjukanTpak as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
{{ $item->pengajuan->nama_dosen ?? $item->pengaju_nama ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">ID Pengajuan: #{{ $item->pengajuan_id }}</div>

                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">
{{ $item->tpak_nama_lengkap ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500 italic">"{{ Str::limit($item->catatan, 30) }}"</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('dupak.penunjukan_tpak.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Batalkan penugasan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                        Belum ada riwayat penunjukan TPAK.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($penunjukanTpak->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        {{ $penunjukanTpak->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/penunjukan-tpak.js') }}"></script>
<!-- Select2 etc -->
@endpush
@endsection
