
@extends('layouts.app')

@section('content')
@php 
use Illuminate\Support\Str; 
$stats = [
    'belum_ditunjuk' => $pengajuan->count(),
    'sedang_review' => $penunjukanTpak->total(),
    'total_dosen' => $dosens->count(),
    'selesai' => 0
];
@endphp

<div class="pt-16 p-6">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white text-left">Command Center DUPAK</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 text-left">Monitoring pengajuan, penugasan TPAK, dan beban kerja penilai.</p>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-calendar-alt mr-2"></i> Periode {{ date('Y') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Belum Ditunjuk</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $stats['belum_ditunjuk'] }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-lg text-orange-600"><i class="fas fa-exclamation-circle fa-lg"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Sedang Review</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['sedang_review'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg text-blue-600"><i class="fas fa-sync-alt fa-lg"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Dosen</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['total_dosen'] }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-lg text-purple-600"><i class="fas fa-users fa-lg"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Selesai Dinilai</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['selesai'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg text-green-600"><i class="fas fa-check-double fa-lg"></i></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <!-- Form remains the same -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 bg-blue-900 border-b border-blue-800">
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Penunjukan Penilai Baru
                        </h2>
                    </div>
                    <form action="{{ route('dupak.penunjukan_tpak.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih Pengajuan (Antrean)</label>
                            <select name="pengajuan_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">-- Cari Nama Pengaju --</option>
                                @foreach($pengajuan as $p)
                                <option value="{{ $p->id }}">#{{ $p->id }} - {{ $p->nama_dosen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih Dosen TPAK (Penilai)</label>
                            <select name="idDosenTpak" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">-- Pilih Penilai --</option>
                                @foreach($dosens as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->nama_lengkap }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-500 mt-1 italic">* Pastikan JFA Penilai ≥ JFA Pengaju</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Catatan Instruksi</label>
                            <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:text-white" placeholder="Instruksi khusus untuk penilai..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 rounded-lg transition-all shadow-md flex items-center justify-center">
                            Tugaskan Sekarang <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>

                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 text-left">Tips Admin</h3>
                            <p class="text-xs text-blue-700 dark:text-blue-400 mt-1 leading-relaxed text-left">
                                Periksa beban kerja penilai pada kolom kanan sebelum memberikan penugasan baru untuk menjaga kualitas review.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-orange-500 rounded-lg mr-3"><i class="fas fa-clock text-white"></i></div>
                        <h2 class="text-xl font-bold dark:text-white">Antrean Pengajuan (Belum Ada Penilai)</h2>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-2 border-orange-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-orange-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-orange-600">ID</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-orange-600">Dosen Pengaju</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-orange-600 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($pengajuan as $p)
                                <tr class="hover:bg-orange-50/50">
                                    <td class="px-6 py-4 text-sm dark:text-white">#{{ $p->id }}</td>
                                    <td class="px-6 py-4 text-sm font-bold dark:text-white">{{ $p->nama_dosen }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <button onclick="document.getElementById('pengajuan_id').value = '{{ $p->id }}'" class="bg-orange-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-orange-600">
                                            <i class="fas fa-user-plus mr-1"></i> Tunjuk Penilai
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">Semua pengajuan sudah memiliki penilai.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                        <h2 class="font-bold text-gray-800 dark:text-white">Daftar Penugasan Aktif</h2>
                        <div class="relative">
                            <input type="text" placeholder="Cari pengajuan..." class="pl-9 pr-4 py-1.5 text-xs rounded-full border-gray-300 dark:bg-gray-600 dark:text-white">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Dosen Pengaju</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Dosen Penilai</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($penunjukanTpak as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->pengaju_nama ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-blue-600 font-medium uppercase tracking-tighter">ID: #{{ $item->pengajuan_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <i class="fas fa-user-shield text-gray-500"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $item->tpak_nama_lengkap ?? 'N/A' }}</div>
                                                <div class="text-[10px] text-gray-500 italic">{{ Str::limit($item->catatan ?? '', 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <span class="h-2 w-2 rounded-full bg-yellow-400 mr-2 animate-pulse"></span>
                                            Reviewing
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="#" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('dupak.penunjukan_tpak.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Batalkan penugasan ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg" title="Batalkan">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-folder-open text-gray-300 fa-3x mb-3"></i>
                                            <p class="text-sm text-gray-500 italic">Tidak ada penugasan aktif saat ini.</p>
                                        </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick assign buttons
    document.querySelectorAll('.quick-assign').forEach(btn => {
        btn.addEventListener('click', function() {
            const pengajuanId = this.dataset.id;
            document.getElementById('pengajuan_id').value = pengajuanId;
        });
    });
});
</script>
@endpush

@endsection