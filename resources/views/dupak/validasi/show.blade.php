@extends('layouts.app')

@section('content')
<div class="pt-16 p-6">
    <div class="mx-auto max-w-7xl">

        @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            {{ session('error') }}
        </div>
        @endif

        {{-- Header --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('dupak.validasi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Validasi Pengajuan #{{ $pengajuan->id }}</h1>
                <p class="text-gray-600 dark:text-gray-400">Review detail kegiatan dan berikan penilaian TPAK.</p>
            </div>
        </div>

        {{-- Grid: Info kiri, Form kanan --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

            {{-- Info Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informasi Pengaju</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Nama Dosen</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $pengajuan->nama_dosen }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">NIDN / ID Dosen</p>
                            <p class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $pengajuan->idDosen }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">JFA Tujuan</p>
                            <p class="text-base font-semibold text-blue-600 dark:text-blue-400">{{ $pengajuan->jabatanTujuan->nama ?? 'N/A' }}</p>
                        </div>
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Total Diajukan</p>
                            <p class="text-xl font-bold text-orange-600" id="totalDiajukan">0 AK</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Total Disetujui</p>
                            <p class="text-xl font-bold text-green-600" id="totalApproved">0 AK</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('dupak.validasi.update', $pengajuan->id) }}" class="">
                    @csrf
                    @method('PATCH')

                    {{-- Kegiatan Table --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-list mr-2 text-blue-600"></i>
                                Daftar Kegiatan ({{ $pengajuan->details->count() }} Items)
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Kegiatan</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bukti</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Diajukan</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Bobot (%)</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Verifikasi</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan Pemeriksa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($pengajuan->details as $detail)
                                    @php
                                    $eval = $myEvaluations[$detail->id] ?? null;
                                    $others = $otherEvaluations[$detail->id] ?? collect();
                                    $scoreVal = $eval ? round(($eval->nilai_angka_kredit / max(0.01, $detail->angka_kredit_total)) * 100) : 100;
                                    $flagVal = $eval->status_evaluasi ?? 'OK';
                                    $noteVal = $eval->catatan ?? '';
                                    $did = $detail->id;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $eval ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $detail->komponen->nama ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $detail->deskripsi_kegiatan ?? '' }}</div>
                                            @if($eval)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 mt-1">
                                                <i class="fas fa-check mr-1"></i> Sudah dinilai
                                            </span>
                                            @endif
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
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white ak-diajukan" data-ak="{{ $detail->angka_kredit_total ?? 0 }}">
                                            {{ number_format($detail->angka_kredit_total, 2) }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="relative">
                                                <input type="number" min="0" max="100" step="1" value="{{ $scoreVal }}" 
                                                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 pr-8" 
                                                    data-did="{{ $did }}" id="score_{{ $did }}" name="scores[{{ $did }}]">
                                                <span class="absolute right-3 top-2 text-gray-400 text-xs font-bold">%</span>
                                            </div>
                                            <div class="mt-1 text-[10px] text-gray-500">Hasil: <span id="score_calc_{{ $did }}" class="font-bold">0.00</span> AK</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <select name="flags[{{ $did }}]" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white text-xs p-2 focus:ring-blue-500">
                                                <option value="OK" {{ $flagVal === 'OK' ? 'selected' : '' }}>VALID (OK)</option>
                                                <option value="Doubt" {{ $flagVal === 'Doubt' ? 'selected' : '' }}>DIRAGUKAN</option>
                                                <option value="Fake" {{ $flagVal === 'Fake' ? 'selected' : '' }}>PALSU / DITOLAK</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <textarea name="notes[{{ $did }}]" rows="2" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 resize-vertical" placeholder="Catatan penilaian TPAK...">{{ $noteVal }}</textarea>

                                            @if($others->isNotEmpty())
                                            <div class="mt-2 pt-2 border-t border-gray-200">
                                                <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Penilaian TPAK lain:</p>
                                                @foreach($others as $o)
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $o->status_evaluasi === 'OK' ? 'bg-green-100 text-green-700' : ($o->status_evaluasi === 'Fake' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                        {{ $o->status_evaluasi }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-500">{{ round(($o->nilai_angka_kredit / max(0.01, $detail->angka_kredit_total)) * 100) }}%</span>
                                                    @if($o->catatan)
                                                    <span class="text-[10px] text-gray-400 italic">"{{ Str::limit($o->catatan, 30) }}"</span>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
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

                    {{-- Overall Decision --}}
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

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateScore(did) {
        const input = document.getElementById('score_' + did);
        const calc = document.getElementById('score_calc_' + did);
        const row = input.closest('tr');
        const akDiajukan = parseFloat(row.querySelector('.ak-diajukan').dataset.ak) || 0;
        
        if (!input || !calc) return;
        
        const score = parseFloat(input.value) || 0;
        const result = akDiajukan * (score / 100);
        
        calc.textContent = result.toFixed(2);
        updateTotals();
    }
    
    function updateTotals() {
        let totalDiajukan = 0;
        let totalDisetujui = 0;
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            const akEl = row.querySelector('.ak-diajukan');
            const input = row.querySelector('input[type="number"]');
            if (!akEl || !input) return;
            
            const ak = parseFloat(akEl.dataset.ak) || 0;
            const score = parseFloat(input.value) || 0;
            totalDiajukan += ak;
            totalDisetujui += ak * (score / 100);
        });
        document.getElementById('totalDiajukan').textContent = totalDiajukan.toFixed(2) + ' AK';
        document.getElementById('totalApproved').textContent = totalDisetujui.toFixed(2) + ' AK';
    }
    
    document.querySelectorAll('input[type="number"]').forEach(function(input) {
        input.addEventListener('input', function() {
            updateScore(this.dataset.did);
        });
        // Inisialisasi kalkulasi awal
        updateScore(input.dataset.did);
    });
    updateTotals();
});
</script>
@endsection
@endsection
