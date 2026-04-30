@extends('layouts.app')

@section('content')
<div class="py-6 font-sans antialiased">

    {{-- Breadcrumb & Title --}}
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <a href="{{ route('dupak.validasi.index') }}" class="group inline-flex items-center text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors mb-4 uppercase tracking-widest">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke Dashboard TPAK
                </a>
                <h1 class="text-3xl font-black tracking-tighter text-gray-900 lg:text-4xl uppercase">
                    Validasi Pengajuan <span class="text-blue-600">#{{ $pengajuan->id }}</span>
                </h1>
                <p class="mt-1 text-gray-500 font-medium">Review Angka Kredit: <span class="text-gray-900 font-black">{{ $pengajuan->nama_dosen }}</span></p>
            </div>
            
            {{-- Floating Totals --}}
            <div class="flex gap-3">
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm min-w-[140px] text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Diajukan</p>
                    <p class="text-xl font-black" id="totalDiajukan">0.00</p>
                </div>
                <div class="bg-blue-600 rounded-2xl p-4 shadow-lg shadow-blue-200 min-w-[140px] text-center">
                    <p class="text-[10px] font-black text-blue-100 uppercase">Disetujui</p>
                    <p class="text-xl font-black text-white" id="totalApproved">0.00</p>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 lg:px-8">
        <form id="finalForm" action="{{ route('dupak.validasi.update', $pengajuan->id) }}" method="POST">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left Sidebar: Info & Action --}}
                <aside class="lg:col-span-3 space-y-6 lg:sticky lg:top-8 self-start">
                    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-2xl bg-blue-900 flex items-center justify-center text-white text-xl font-bold shadow-md">
                                {{ substr($pengajuan->nama_dosen, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-xl text-gray-900">{{ $pengajuan->nama_dosen }}</p>
                                <p class="text-sm font-medium text-gray-500">{{ $pengajuan->idDosen }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Target Jabatan</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $pengajuan->jabatanTujuan->nama ?? 'N/A' }}</p>
                            </div>
                            
                            <hr class="border-gray-200 my-4">



                            <button type="submit" class="w-full py-3 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-lg shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 text-sm">
                                <i class="fas fa-save"></i>
                                Simpan Validasi Final
                            </button>
                        </div>
                    </div>
                </aside>

                {{-- Right Content: Main Table --}}
                <div class="lg:col-span-9 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-b-2xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500">Rincian Komponen Kegiatan</h2>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                                    {{ $pengajuan->details->count() }} Item
                                </span>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kegiatan & Bukti</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">AK Awal</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Bobot (%)</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($pengajuan->details as $index => $detail)
                                    @php
                                        $eval = $myEvaluations[$detail->id] ?? null;
                                        $scoreVal = $eval ? round(($eval->nilai_angka_kredit / max(0.01, $detail->angka_kredit_total)) * 100) : 100;
                                        $did = $detail->id;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-center">
                                            <span class="w-6 h-6 rounded-full bg-gray-100 text-xs font-bold flex items-center justify-center text-gray-600">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <div class="flex items-start gap-3">
                                                    <p class="text-sm font-semibold text-gray-900 min-w-0 flex-1">{{ $detail->komponen->nama ?? 'N/A' }}</p>
                                                    @if($detail->link_bukti_pendukung)
                                                    <a href="{{ $detail->link_bukti_pendukung }}" target="_blank" title="Bukti Pendukung">
                                                        <i class="fas fa-external-link-alt text-blue-500 hover:text-blue-700 text-sm p-1.5 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 line-clamp-2" title="{{ $detail->deskripsi_kegiatan }}">{{ $detail->deskripsi_kegiatan }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-px bg-gray-100 text-gray-900 text-sm font-bold rounded">
                                                <span class="ak-diajukan" data-ak="{{ $detail->angka_kredit_total }}">{{ number_format($detail->angka_kredit_total, 2) }}</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col items-center gap-1">
                                                <input type="number" name="scores[{{ $did }}]" id="score_{{ $did }}" data-did="{{ $did }}" value="{{ $scoreVal }}" min="0" max="100" step="1"
                                                    class="w-20 p-2 border border-gray-200 rounded-lg text-center font-bold bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-300 transition-all [-webkit-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                <div class="text-xs font-bold px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span id="score_calc_{{ $did }}">0.00</span> AK
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="space-y-2">
                                                @php
                                                    $currentFlag = $eval->status_evaluasi ?? 'Pending';
                                                    $flagClass = [
                                                        'OK' => 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100',
                                                        'Doubt' => 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100',
                                                        'Fake' => 'bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100',
                                                        'Pending' => 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100',
                                                    ][$currentFlag];
                                                    $flagIcon = [
                                                        'OK' => 'fa-check-circle',
                                                        'Doubt' => 'fa-exclamation-triangle',
                                                        'Fake' => 'fa-times-circle',
                                                        'Pending' => 'fa-question-circle',
                                                    ][$currentFlag];
                                                @endphp
                                                <button type="button" onclick="showStatusModal('{{ $did }}')" class="w-full py-2.5 px-4 {{ $flagClass }} font-semibold rounded-lg shadow-sm hover:shadow-md transition-all text-sm flex items-center justify-center gap-2">
                                                    <i class="fas {{ $flagIcon }}"></i>
                                                    <span id="current_status_text_{{ $did }}">{{ $currentFlag === 'Pending' ? 'Set Status' : $currentFlag }}</span>
                                                </button>
                                                <div class="text-xs text-gray-500 text-center -mt-1">Nilai: <span id="status_score_{{ $did }}">{{ $scoreVal }}%</span></div>
                                                <input type="hidden" name="flags[{{ $did }}]" id="flag_{{ $did }}" value="{{ $eval->status_evaluasi ?? 'OK' }}">
                                                <input type="hidden" id="notes_{{ $did }}" name="notes[{{ $did }}]" value="{{ $eval->catatan ?? '' }}">
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center opacity-50 space-y-3">
                                                <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                                <p class="text-lg font-semibold text-gray-500">Belum ada data kegiatan</p>
                                                <p class="text-sm text-gray-400">Komponen kegiatan akan muncul setelah pengajuan dilengkapi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Status Modal --}}
                            <div id="statusModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full max-h-[85vh] overflow-hidden">
                                    <div class="p-5 border-b border-gray-200 bg-gray-50">
                                        <h3 class="text-base font-bold text-gray-900">Update Status</h3>
                                        <p class="text-sm text-gray-500 mt-1" id="modalDid">Detail ID: ...</p>
                                    </div>
                                    <div class="p-5 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Status Evaluasi</label>
                                            <select id="modalStatus" class="w-full p-3 border border-gray-200 rounded-lg bg-white text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                                <option value="OK">✅ OK - Full Score (100%)</option>
                                                <option value="Doubt">⚠️ REVISI - Half Score (50%)</option>
                                                <option value="Fake">❌ TOLAK - No Score (0%)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Catatan Evaluasi</label>
                                            <textarea id="modalNotes" rows="3" class="w-full p-3 border border-gray-200 rounded-lg bg-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical placeholder-gray-400 shadow-sm transition-all" placeholder="Jelaskan alasan revisi atau penolakan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="p-5 pt-0 flex gap-3 border-t bg-gray-50">
                                        <button onclick="hideStatusModal()" class="flex-1 py-2.5 px-4 border border-gray-200 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-all shadow-sm">Batal</button>
                                        <button onclick="applyStatusModal()" class="flex-1 py-2.5 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-lg shadow-sm hover:shadow-md transition-all text-sm">Terapkan Perubahan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Overall Feedback --}}
                    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-2 h-6 bg-gray-300 rounded-full flex-shrink-0 mt-0.5"></div>
                            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-900 font-semibold">Catatan Kesimpulan TPAK</h3>
                        </div>
                        <textarea name="overall_notes" rows="4" class="w-full p-4 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical placeholder-gray-500 shadow-sm transition-all text-sm" placeholder="Tuliskan catatan akhir verifikasi untuk arsip internal TPAK...">{{ $overallNotes }}</textarea>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function calculateItem(did) {
            const input = document.getElementById('score_' + did);
            const calcDisplay = document.getElementById('score_calc_' + did);
            if (!input) return { akAwal: 0, result: 0 };

            const tr = input.closest('tr');
            const akAwal = parseFloat(tr.querySelector('.ak-diajukan')?.dataset.ak) || 0;
            let percent = parseFloat(input.value) || 0;

            if (percent > 100) percent = 100;
            if (percent < 0) percent = 0;

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

            document.getElementById('totalDiajukan').textContent = totalDiajukan.toFixed(2);
            document.getElementById('totalApproved').textContent = totalDisetujui.toFixed(2);
        }

        document.querySelectorAll('input[name^="scores"]').forEach(input => {
            input.addEventListener('input', refreshTotals);
            calculateItem(input.dataset.did);
        });

        refreshTotals();

        // Modal Functions
        let currentDetailId = null; // To store the ID of the detail being edited
        window.showStatusModal = function(did) {
            currentDetailId = did;
            document.getElementById('modalDid').textContent = 'Detail ID: ' + did;
            const flagEl = document.getElementById('flag_' + did);
            const notesEl = document.getElementById('notes_' + did);
            
            document.getElementById('modalStatus').value = flagEl.value;
            document.getElementById('modalNotes').value = notesEl.value;
            
            const modal = document.getElementById('statusModal');
            modal.classList.remove('hidden');
            // Animate
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.8)';
            setTimeout(() => {
                modal.style.transition = 'all 0.2s ease-out';
                modal.style.opacity = '1';
                modal.style.transform = 'scale(1)';
            }, 10);
        };

        window.hideStatusModal = function() {
            const modal = document.getElementById('statusModal');
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.transition = '';
            }, 200);
        };


        window.applyStatusModal = async function() {
            const did = currentDetailId; // Use the stored ID
            const status = document.getElementById('modalStatus').value;
            const notes = document.getElementById('modalNotes').value;
            
            const scoreMap = {'OK': 100, 'Doubt': 50, 'Fake': 0};
            const score = scoreMap[status];
            
            const pengajuanId = {{ $pengajuan->id }};
            
            // Save via AJAX
            try {
                const response = await fetch(`/dupak/validasi/${pengajuanId}/detail/${did}/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        score: score,
                        flag: status, // This is the status_evaluasi
                        note: notes
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update UI after successful save
                    updateRowUI(did, data.data.score, data.data.flag, notes);
                    // Show success toast
                    showToast('success', data.message);
                } else {
                    showToast('error', 'Gagal menyimpan: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Save error:', error);
                showToast('error', 'Terjadi kesalahan jaringan.');
            }
            
            hideStatusModal();
        };
        
        // Removed the redundant saveDetailEvaluation function as applyStatusModal handles it.
        // window.saveDetailEvaluation = async function(did) { ... };

        function updateRowUI(did, score, flag, notes) {
            const scoreInput = document.getElementById('score_' + did);
            const flagInput = document.getElementById('flag_' + did);
            const notesInput = document.getElementById('notes_' + did);
            const statusTextSpan = document.getElementById('current_status_text_' + did);
            const statusButton = statusTextSpan.closest('button');
            const statusScoreSpan = document.getElementById('status_score_' + did);
            
            scoreInput.value = score;
            flagInput.value = flag;
            notesInput.value = notes;
            statusScoreSpan.textContent = score + '%';
            statusTextSpan.textContent = flag;

            // Update button classes for visual feedback
            const flagClasses = {
                'OK': 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100',
                'Doubt': 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100',
                'Fake': 'bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100',
                'Pending': 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100',
            };
            const flagIcons = {
                'OK': 'fa-check-circle',
                'Doubt': 'fa-exclamation-triangle',
                'Fake': 'fa-times-circle',
                'Pending': 'fa-question-circle',
            };

            // Remove all existing flag classes and icons
            statusButton.className = statusButton.className.split(' ').filter(c => !c.startsWith('bg-') && !c.startsWith('border-') && !c.startsWith('text-') && !c.startsWith('hover:') && !c.startsWith('fa-')).join(' ');
            statusButton.classList.add(...flagClasses[flag].split(' '));
            statusButton.querySelector('i').className = `fas ${flagIcons[flag]}`;

            calculateItem(did);
            refreshTotals();
        }
    });
</script>
@endsection