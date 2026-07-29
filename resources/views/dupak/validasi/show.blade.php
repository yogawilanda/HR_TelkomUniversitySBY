@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] font-sans antialiased pb-12">

    {{-- Breadcrumb & Title Section --}}
    <div class="bg-white border-b border-gray-200 mb-8 shadow-sm">
        <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <a href="{{ route('dupak.validasi.index') }}" class="group inline-flex items-center text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors mb-3 uppercase tracking-widest">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                        Kembali ke Dashboard TPAK
                    </a>
                    <h1 class="text-3xl font-black tracking-tighter text-gray-900 lg:text-4xl uppercase">
                        Validasi <span class="text-blue-600">#{{ $pengajuan->id }}</span>
                    </h1>
                    <p class="mt-1 text-gray-500 font-medium">Review Angka Kredit: <span class="text-gray-900 font-black uppercase">{{ $pengajuan->nama_dosen }}</span></p>
                </div>

                {{-- Floating Totals: Realtime Feedback --}}
                <div class="flex gap-4">
                    <div class="bg-white border-2 border-slate-100 rounded-2xl p-4 min-w-[130px] text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Total Diajukan</p>
                        <p class="text-2xl font-black text-slate-800" id="totalDiajukan">0.00</p>
                    </div>
                    <div class="bg-blue-600 rounded-2xl p-4 shadow-lg shadow-blue-200 min-w-[130px] text-center">
                        <p class="text-[10px] font-black text-blue-100 uppercase tracking-tighter">Total Disetujui</p>
                        <p class="text-2xl font-black text-white" id="totalApproved">0.00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 lg:px-8">
        <form action="{{ route('dupak.validasi.update', $pengajuan->id) }}" method="POST">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- Sidebar: Info Dosen (Collapsible) --}}
                <aside class="lg:col-span-3 lg:sticky lg:top-8">
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden" id="profileCard">
                        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center cursor-pointer select-none" onclick="toggleProfile()">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Profil Pengaju</span>
                            <i class="fas fa-chevron-up text-gray-400 transition-transform duration-300" id="profileChevron"></i>
                        </div>

                        <div id="profileContent" class="block">
                            <div class="p-6">
                                <div class="flex flex-col items-center text-center space-y-4">
                                    <div class="w-20 h-20 rounded-3xl bg-blue-900 flex items-center justify-center text-white text-3xl font-black shadow-inner">
                                        {{ substr($pengajuan->nama_dosen, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-gray-900 text-lg leading-tight uppercase">{{ $pengajuan->nama_dosen }}</p>
                                        <p class="text-sm font-bold text-gray-400 mt-1">{{ $pengajuan->dosen?->nidn }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100/50">
                                        <p class="text-[10px] font-black text-blue-400 uppercase mb-1 tracking-widest">NIDN</p>
                                        <p class="text-sm font-black text-blue-900">{{ $pengajuan->dosen?->nidn ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest uppercase">Auto-Save Active</p>
                                </div>

                                <button type="submit" class="mt-6 w-full py-4 bg-blue-900 hover:bg-blue-800 text-white font-black rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2 text-[11px] uppercase tracking-widest">
                                    <i class="fas fa-save"></i>
                                    Simpan Validasi Final
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Table Content --}}
                <div class="lg:col-span-9">
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="text-sm font-black uppercase tracking-widest text-gray-400">Rincian Komponen</h2>
                            <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-full uppercase tracking-widest">
                                {{ $pengajuan->details->count() }} Data Points
                            </span>
                        </div>

                        <div class="overflow-x-auto text-[13px]">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">#</th>
                                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Kegiatan & Deskripsi</th>
                                        <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">AK Awal</th>
                                        <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Bobot (%)</th>
                                        <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi Validasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($pengajuan->details as $index => $detail)
                                    @php
                                        $eval = $myEvaluations[$detail->id] ?? null;
                                        $scoreVal = $eval ? round(($eval->nilai_angka_kredit / max(0.01, $detail->angka_kredit_total)) * 100) : 100;
                                        $did = $detail->id;
                                        $currentFlag = $eval->status_evaluasi ?? 'Pending';

                                        // Dynamic Classes Logic (Struktur asli Anda dipertahankan)
                                        $flagClass = [
                                            'OK' => 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100',
                                            'Doubt' => 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100',
                                            'Rejected' => 'bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100',
                                            'Pending' => 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100',
                                            'Verified' => '',
                                        ][$currentFlag] ?? 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100'; 
                                        // Fallback jika value di luar list
                                    @endphp
                                    <tr class="hover:bg-blue-50/30 transition-colors group">
                                        <td class="px-4 py-6 text-center text-gray-400 font-bold">{{ $index + 1 }}</td>
                                        <td class="px-6 py-6">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-black text-gray-900 uppercase leading-tight">{{ $detail->komponen->nama ?? 'N/A' }}</span>
                                                    @if($detail->link_bukti_pendukung)
                                                    <a href="{{ $detail->link_bukti_pendukung }}" target="_blank" class="text-blue-500 hover:text-blue-700 p-1 bg-blue-50 rounded-md transition-all">
                                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                <p class="text-gray-500 italic leading-relaxed">{{ $detail->deskripsi_kegiatan }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-800 font-black rounded-lg ak-diajukan" data-ak="{{ $detail->angka_kredit_total }}">
                                                {{ number_format($detail->angka_kredit_total, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-6 text-center">
                                            <input type="number" name="scores[{{ $did }}]" id="score_{{ $did }}" data-did="{{ $did }}" value="{{ $scoreVal }}"
                                                class="w-16 p-2 border-2 border-gray-100 rounded-xl text-center font-black focus:border-blue-500 focus:ring-0 transition-all outline-none">
                                            <div class="text-[10px] font-black text-emerald-600 mt-2">
                                                <span id="score_calc_{{ $did }}">0.00</span> AK
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            <button type="button" onclick="showStatusModal('{{ $did }}')" class="w-full py-3 px-4 rounded-xl border-2 font-black text-[11px] uppercase tracking-widest transition-all {{ $flagClass }} flex items-center justify-between">
                                                <span id="current_status_text_{{ $did }}">{{ $currentFlag }}</span>
                                                <i class="fas fa-chevron-right opacity-30"></i>
                                            </button>
                                            <div class="mt-2 text-[9px] font-bold text-gray-400 uppercase tracking-tight">
                                                Nilai: <span id="status_score_{{ $did }}">{{ $scoreVal }}%</span>
                                            </div>

                                            {{-- Inputs Hidden --}}
                                            <input type="hidden" name="flags[{{ $did }}]" id="flag_{{ $did }}" value="{{ $currentFlag }}">
                                            <input type="hidden" id="notes_{{ $did }}" name="notes[{{ $did }}]" value="{{ $eval->catatan ?? '' }}">
                                        </td>
                                    </tr>
                                    @empty
                                    {{-- ... empty state ... --}}
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Overall Feedback --}}
                        <div class="mt-8 p-8 bg-white border border-gray-200 rounded-3xl shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                                <h3 class="text-xs font-black uppercase tracking-widest text-gray-900">Catatan Kesimpulan TPAK</h3>
                            </div>
                            <textarea name="overall_notes" rows="4" class="w-full p-5 border-2 border-gray-50 rounded-2xl bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all outline-none text-sm placeholder-gray-400 font-medium" placeholder="Tuliskan catatan akhir verifikasi untuk arsip internal TPAK...">{{ $overallNotes }}</textarea>
                        </div>
                    </div>
                </div>
        </form>

        {{-- Status Modal --}}
        <div id="statusModal" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full overflow-hidden border border-white/20">
                <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Update Status</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest" id="modalDid">Detail ID: ...</p>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">Status Evaluasi</label>
                        <select id="modalStatus" class="w-full p-4 border-2 border-gray-100 rounded-2xl bg-white text-sm font-black focus:border-blue-500 focus:ring-0 outline-none appearance-none transition-all">
                            <option value="OK">✅ OK (100%)</option>
                            <option value="Doubt">⚠️ REVISI (50%)</option>
                            <option value="Rejected">❌ TOLAK (0%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest">Catatan Evaluasi</label>
                        <textarea id="modalNotes" rows="3" class="w-full p-4 border-2 border-gray-100 rounded-2xl bg-white text-sm focus:border-blue-500 focus:ring-0 outline-none resize-none placeholder-gray-300 font-medium transition-all" placeholder="Jelaskan alasan revisi atau penolakan..."></textarea>
                    </div>
                </div>
                <div class="p-8 pt-0 flex gap-3">
                    <button onclick="hideStatusModal()" class="flex-1 py-4 px-4 border-2 border-gray-100 text-[11px] font-black text-gray-400 rounded-2xl hover:bg-gray-50 transition-all uppercase tracking-widest">Batal</button>
                    <button onclick="applyStatusModal()" class="flex-1 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl shadow-lg shadow-blue-100 transition-all text-[11px] uppercase tracking-widest">Simpan</button>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi hitung per baris
        function calculateItem(did) {
            const input = document.getElementById('score_' + did);
            const calcDisplay = document.getElementById('score_calc_' + did);
            if (!input) return {
                akAwal: 0,
                result: 0
            };

            const tr = input.closest('tr');
            const akAwal = parseFloat(tr.querySelector('.ak-diajukan')?.dataset.ak) || 0;
            let percent = parseFloat(input.value) || 0;

            if (percent > 100) percent = 100;
            if (percent < 0) percent = 0;

            const result = akAwal * (percent / 100);
            if (calcDisplay) calcDisplay.textContent = result.toFixed(2);

            return {
                akAwal,
                result
            };
        }

        // Refresh total AK di header
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

        // Event Listeners untuk input skor
        document.querySelectorAll('input[name^="scores"]').forEach(input => {
            input.addEventListener('input', () => {
                calculateItem(input.dataset.did);
                refreshTotals();
            });
            calculateItem(input.dataset.did); // Initial calculation
        });

        refreshTotals();

        // Modal Logic
        let currentDetailId = null;

        window.showStatusModal = function(did) {
            currentDetailId = did;
            const modal = document.getElementById('statusModal');
            const flagEl = document.getElementById('flag_' + did);
            const notesEl = document.getElementById('notes_' + did);

            document.getElementById('modalDid').textContent = 'Detail ID: ' + did;
            document.getElementById('modalStatus').value = flagEl.value;
            document.getElementById('modalNotes').value = notesEl.value;

            modal.classList.remove('hidden');
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
            }, 200);
        };

        window.applyStatusModal = async function() {
            const did = currentDetailId;
            const status = document.getElementById('modalStatus').value;
            const notes = document.getElementById('modalNotes').value;

            const scoreMap = {
                'OK': 100,
                'Doubt': 50,
                'Rejected': 0,
                'Pending': 100
            };
            const score = scoreMap[status] ?? 100;
            const pengajuanId = "{{ $pengajuan->id }}";
            
            const url = "{{ route('dupak.validasi.detail.save', ['pengajuan' => ':pengajuanId', 'detail' => ':did']) }}"
                .replace(':pengajuanId', pengajuanId)
                .replace(':did', did);
            
            // Ambil tombol simpan modal untuk disable sementara
            const saveBtn = document.querySelector('#statusModal button:last-child');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Menyimpan...';
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        score,
                        flag: status,
                        note: notes
                    })
                });

                const data = await response.json();
                if (data.success) {
                    updateRowUI(did, score, status, notes);
                    if (typeof showToast === 'function') {
                        showToast('success', data.message);
                    }
                } else {
                    alert('Gagal menyimpan: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Save error:', error);
                alert('Terjadi kesalahan jaringan.');
            } finally {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Simpan';
                }
                hideStatusModal();
            }
        };

        function updateRowUI(did, score, flag, notes) {
            const scoreInput = document.getElementById('score_' + did);
            const flagInput = document.getElementById('flag_' + did);
            const notesInput = document.getElementById('notes_' + did);
            const statusTextSpan = document.getElementById('current_status_text_' + did);
            const statusButton = statusTextSpan?.closest('button');
            const statusScoreSpan = document.getElementById('status_score_' + did);

            if (scoreInput) scoreInput.value = score;
            if (flagInput) flagInput.value = flag;
            if (notesInput) notesInput.value = notes;
            if (statusScoreSpan) statusScoreSpan.textContent = score + '%';
            if (statusTextSpan) statusTextSpan.textContent = flag;

            if (statusButton) {
                const flagClasses = {
                    'OK': 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100',
                    'Doubt': 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100',
                    'Rejected': 'bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100',
                    'Pending': 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100',
                };

                // Reset classes but keep base classes
                statusButton.className = 'w-full py-3 px-4 rounded-xl border-2 font-black text-[11px] uppercase tracking-widest transition-all flex items-center justify-between ' + flagClasses[flag];
            }

            calculateItem(did);
            refreshTotals();
        }
    });

    function toggleProfile() {
        const content = document.getElementById('profileContent');
        const chevron = document.getElementById('profileChevron');

        // Toggle class hidden milik Tailwind
        content.classList.toggle('hidden');
        
        // Putar ikon panah
        if (content.classList.contains('hidden')) {
            chevron.style.transform = 'rotate(180deg)';
        } else {
            chevron.style.transform = 'rotate(0deg)';
        }
    }
</script>
@endsection