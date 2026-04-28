@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pb-12">
    {{-- Notifikasi / Alerts --}}
    @if (session('success') || session('error'))
    <div class="fixed top-20 right-6 z-50 min-w-[300px] animate-fade-in-down">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 shadow-lg rounded-r-xl flex items-center">
                <i class="fas fa-check-circle mr-3 text-emerald-500"></i>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @else
            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 shadow-lg rounded-r-xl flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-rose-500"></i>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
        @endif
    </div>
    @endif

    {{-- Header Navigation --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-[1600px] mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dupak.validasi.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-900 hover:text-white transition-all">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-lg font-black text-gray-900 leading-tight">Validasi Pengajuan #{{ $pengajuan->id }}</h1>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Review Angka Kredit TPAK</p>
                </div>
            </div>

            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Total Diajukan</p>
                    <p class="text-lg font-black text-gray-900" id="totalDiajukan">0.00 AK</p>
                </div>
                <div class="text-right border-l border-gray-200 pl-8">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-tighter">Total Disetujui</p>
                    <p class="text-lg font-black text-emerald-600" id="totalApproved">0.00 AK</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-6 mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            {{-- Kiri: Informasi Dosen --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-28">
                    <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Pengaju</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-black text-gray-900">{{ $pengajuan->nama_dosen }}</p>
                            <p class="text-xs text-gray-500 font-medium">NIDN: {{ $pengajuan->idDosen }}</p>
                        </div>
                        <div class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold">
                            {{ $pengajuan->jabatanTujuan->nama ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Tabel Kegiatan --}}
            <div class="lg:col-span-3">
                <form id="finalForm" action="{{ route('dupak.validasi.update', $pengajuan->id) }}" method="POST">
                    @csrf @method('PATCH')

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-4 text-center w-12">#</th>
                                    <th class="px-4 py-4">Detail Kegiatan</th>
                                    <th class="px-4 py-4 text-center w-20">Bukti</th>
                                    <th class="px-4 py-4 text-center w-28">AK Awal</th>
                                    <th class="px-4 py-4 w-32">Bobot (%)</th>
                                    <th class="px-4 py-4 w-40">Verifikasi</th>
                                    <th class="px-6 py-4">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($pengajuan->details as $index => $detail)
                                    @php
                                        $eval = $myEvaluations[$detail->id] ?? null;
                                        $others = $otherEvaluations[$detail->id] ?? collect();
                                        $scoreVal = $eval ? round(($eval->nilai_angka_kredit / max(0.01, $detail->angka_kredit_total)) * 100) : 100;
                                        $did = $detail->id;
                                    @endphp
                                    <tr class="hover:bg-blue-50/30 transition-all group">
                                        <td class="px-6 py-4 text-center text-xs font-bold text-gray-300">{{ $index + 1 }}</td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $detail->komponen->nama ?? 'N/A' }}</div>
                                            <div class="text-[11px] text-gray-500 italic line-clamp-1 group-hover:line-clamp-none">"{{ $detail->deskripsi_kegiatan }}"</div>
                                            
                                            {{-- Penilaian TPAK Lain (Tetap Ada) --}}
                                            @if($others->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @foreach($others as $o)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-[9px] font-bold text-gray-500 border border-gray-200" title="{{ $o->catatan }}">
                                                    TPAK: {{ round(($o->nilai_angka_kredit / max(0.01, $detail->angka_kredit_total)) * 100) }}%
                                                </span>
                                                @endforeach
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if($detail->link_bukti_pendukung)
                                                <a href="{{ $detail->link_bukti_pendukung }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-sm">
                                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                                </a>
                                            @else
                                                <span class="text-gray-200">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm font-black text-gray-700 ak-diajukan" data-ak="{{ $detail->angka_kredit_total }}">
                                                {{ number_format($detail->angka_kredit_total, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="relative">
                                                <input type="number" name="scores[{{ $did }}]" id="score_{{ $did }}" data-did="{{ $did }}" value="{{ $scoreVal }}" min="0" max="100"
                                                    class="w-full bg-gray-50 border-gray-200 rounded-lg py-1.5 px-2 text-sm font-black text-center text-blue-600 focus:ring-2 focus:ring-blue-500">
                                                <div class="text-[9px] text-center mt-1 font-bold text-emerald-500"><span id="score_calc_{{ $did }}">0.00</span> AK</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <select name="flags[{{ $did }}]" class="w-full bg-gray-50 border-gray-200 rounded-lg py-1.5 text-[10px] font-black uppercase focus:ring-2 focus:ring-blue-500">
                                                <option value="OK" {{ ($eval->status_evaluasi ?? 'OK') === 'OK' ? 'selected' : '' }}>✓ Valid</option>
                                                <option value="Doubt" {{ ($eval->status_evaluasi ?? '') === 'Doubt' ? 'selected' : '' }}>? Ragu</option>
                                                <option value="Fake" {{ ($eval->status_evaluasi ?? '') === 'Fake' ? 'selected' : '' }}>✕ Tolak</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <textarea name="notes[{{ $did }}]" rows="1" class="w-full bg-transparent border-b border-gray-100 py-1 text-xs focus:border-blue-500 focus:ring-0 placeholder-gray-300 resize-none" placeholder="Catatan...">{{ $eval->catatan ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm font-medium italic">Tidak ada data kegiatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer: Keputusan Akhir --}}
                    <div class="bg-white rounded-3xl p-8 shadow-2xl shadow-blue-900/20 text-white">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="md:col-span-2">
                                <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest block mb-3">Catatan Kesimpulan TPAK</label>
                                <textarea name="overall_notes" rows="4" class="w-full bg-white/5 border-gray-300 rounded-2xl text-sm focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500" placeholder="Ketikkan catatan akhir di sini...">{{ $pengajuan->catatan_tpak }}</textarea>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest block mb-3">Keputusan Status</label>
                                <select name="status" class="w-full bg-white/10 border-gray-200 rounded-xl text-black text-sm font-bold mb-6 focus:ring-blue-500">
                                    <option value="Approved" class="text-gray-900">Approved</option>
                                    <option value="Revision" class="text-gray-900">Revision</option>
                                    <option value="Rejected" class="text-gray-900">Rejected</option>
                                </select>
                                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl transition-all shadow-lg flex items-center justify-center gap-3">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    SIMPAN VALIDASI
                                </button>
                            </div>
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
    function calculateItem(did) {
        const input = document.getElementById('score_' + did);
        const calcDisplay = document.getElementById('score_calc_' + did);
        if(!input) return { akAwal:0, result:0 };

        const tr = input.closest('tr');
        const akAwal = parseFloat(tr.querySelector('.ak-diajukan').dataset.ak) || 0;
        const percent = parseFloat(input.value) || 0;
        const result = akAwal * (percent / 100);
        
        calcDisplay.textContent = result.toFixed(2);
        return { akAwal, result };
    }

    function refreshTotals() {
        let totalDiajukan = 0;
        let totalDisetujui = 0;
        
        document.querySelectorAll('input[name^="scores"]').forEach(input => {
            const data = calculateItem(input.dataset.did);
            totalDiajukan += data.akAwal;
            totalDisetujui += data.result;
        });

        document.getElementById('totalDiajukan').textContent = totalDiajukan.toFixed(2) + ' AK';
        document.getElementById('totalApproved').textContent = totalDisetujui.toFixed(2) + ' AK';
    }

    document.querySelectorAll('input[name^="scores"]').forEach(input => {
        input.addEventListener('input', refreshTotals);
        calculateItem(input.dataset.did); 
    });

    refreshTotals();
});
</script>

<style>
@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down { animation: fade-in-down 0.4s ease-out; }
</style>
@endsection
@endsection