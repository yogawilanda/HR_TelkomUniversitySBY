@extends('layouts.app')

@section('content')
<div class="mt-16 md:ml-64 p-6">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('dupak.validasi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Validasi Pengajuan #{{ $pengajuan->id }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">Review detail kegiatan dan berikan penilaian TPAK.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informasi Pengaju</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Nama Dosen</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pengajuan->nama_dosen }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">NIP</p>
                            <p class="text-lg font-medium">{{ $pengajuan->idDosen }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">JFA Tujuan</p>
                            <p class="text-lg font-medium text-blue-600">{{ $pengajuan->jabatanTujuan->nama ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Total Diajukan</p>
                            <p class="text-xl font-bold text-orange-600">250 AK</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Total Disetujui</p>
                            <p class="text-xl font-bold text-green-600" id="totalApproved">0 AK <span class="text-sm">(0%)</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('dupak.validasi.update', $pengajuan->id) }}" class="space-y-6">
                    @csrf @method('PATCH')

                    <!-- Kegiatan Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-list mr-2 text-blue-600"></i>Daftar Kegiatan ({{ $pengajuan->details->count() }} Items)
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Kegiatan</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bukti</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Diajukan</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Score %</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Flag</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-64">Catatan TPAK</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($pengajuan->details as $detail)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $detail->kegiatan->nama ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $detail->deskripsi ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($detail->link_bukti_pendukung)
                                            <a href="{{ $detail->link_bukti_pendukung }}" target="_blank" class="text-blue-600 hover:underline text-sm">
                                                <i class="fas fa-file mr-1"></i>Lihat
                                            </a>
                                            @else
                                            <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $detail->angka_kredit }}</td>
                                        <td class="px-6 py-4">

                                            <input type="range" min="0" max="100" step="5" value="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider" 
                                                   id="score_{{ $detail->id }}" name="scores[{{ $detail->id }}]" onchange="updateScore({{ $detail->id }})">
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: 100%" id="score_fill_{{ $detail->id }}"></div>
                                            </div>
                                            <span class="text-sm font-bold text-blue-600" id="score_val_{{ $detail->id }}">100%</span>

                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <label class="flex items-center">
                                                    <input type="radio" name="flags[{{ $detail->id }}]" value="OK" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                    <span class="ml-1 text-xs text-green-700 font-medium">OK</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" name="flags[{{ $detail->id }}]" value="Doubt" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="ml-1 text-xs text-yellow-700 font-medium">Doubt</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" name="flags[{{ $detail->id }}]" value="Fake" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="ml-1 text-xs text-red-700 font-medium">Fake</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <textarea name="notes[{{ $detail->id }}]" rows="2" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 resize-vertical" placeholder="Catatan penilaian TPAK..."></textarea>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada kegiatan untuk divalidasi.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Overall Decision -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-6 border-2 border-dashed border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Keputusan Akhir TPAK</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Pengajuan</label>
                                <select name="status" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500">
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                    <option>Revision</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan Umum TPAK</label>
                                <textarea name="overall_notes" rows="3" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500" placeholder="Catatan keseluruhan untuk pengajuan ini..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="window.history.back()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                <i class="fas fa-save mr-2"></i>Simpan Validasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

let totalApproved = 0;
let detailCount = {{ $pengajuan->details->count() || 0 }};

function updateScore(id) {
    const slider = document.getElementById('score_' + id);
    const fill = document.getElementById('score_fill_' + id);
    const val = document.getElementById('score_val_' + id);
    const score = slider.value;
    fill.style.width = score + '%';
    val.textContent = score + '%';
    updateTotal();
}

function updateTotal() {
    totalApproved = 0;
    for(let i = 0; i < detailCount; i++) {
        const slider = document.getElementById('score_slider_' + i);
        if (slider) totalApproved += parseFloat(slider.value);
    }
    const percentage = detailCount > 0 ? Math.round((totalApproved / detailCount) * 100) : 0;
    document.getElementById('totalApproved').innerHTML = Math.round(totalApproved) + ' AK <span class="text-sm">(' + percentage + '%)</span>';
}

// Init all sliders
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});

</script>
@endpush

@endsection

