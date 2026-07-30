<!-- Modal Penunjukan TPAK -->
<div id="modalTpak" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModalTpak()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-blue-900 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center"
                        id="modal-title">
                        <i class="fas fa-user-plus mr-2"></i> Penunjukan Penilai Baru
                    </h3>
                    <button type="button" onclick="closeModalTpak()"
                        class="text-blue-200 hover:text-white focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('dupak.penunjukan_tpak.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih
                            Pengajuan (Antrean)</label>
                        <select id="modal_pengajuan_id" name="pengajuan_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                            <option value="">-- Cari Nama Pengaju --</option>
                            @foreach($pengajuan as $p)
                            @php
                            $count = $tpakCounts[$p->id] ?? 0;
                            $targetJfa = $jfaGlobalNames[$p->jfaTujuan] ?? 'N/A';
                            @endphp
                            <option value="{{ $p->id }}" data-pengaju="{{ $p->idDosen }}" data-count="{{ $count }}" data-target="{{ $targetJfa }}">
                                #{{ $p->id }} - {{ $p->nama_dosen }} (Target: {{ $targetJfa }})
                            </option>
                            @endforeach
                        </select>
                        <p id="modal-pengajuan-info" class="text-[10px] text-gray-500 mt-1 italic hidden"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih
                            Dosen TPAK (Penilai)</label>
                        <select id="modal_idDosenTpak" name="idDosenTpak"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                            <option value="">-- Pilih Penilai --</option>
                            @foreach($dosens as $d)
                            @php $workload = $dosenWorkload[$d->id] ?? 0; @endphp
                            @php $jfaNama = $tpakJfaNama[$d->id] ?? null; @endphp
                            <option value="{{ $d->id }}" data-workload="{{ $workload }}" data-jfa="{{ $jfaNama }}">
                                {{ $d->nama_lengkap }} (Beban: {{ $workload }} penugasan) -
                                {{ $jfaNama ? $jfaNama : 'JFA tidak aktif' }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1 italic">* Pastikan JFA Penilai ≥ JFA Pengaju</p>
                        <p id="modal-tpak-filter-info" class="text-[10px] text-yellow-600 mt-1 italic hidden"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Bukti Penunjukan</label>
                        <textarea name="bukti_penunjukan" rows="3"
                            class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:text-white"
                            placeholder="Isi tautan hanya /<nama_link> contoh: tautan_bukti_penunjukan tanpa https://www.drive.google.com"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Catatan
                            Instruksi</label>
                        <textarea name="catatan" rows="3"
                            class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:text-white"
                            placeholder="Instruksi khusus untuk penilai..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModalTpak()"
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white bg-blue-900 rounded-lg hover:bg-blue-800 flex items-center">
                            Tugaskan Sekarang <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>