@extends('layouts.app')

@section('content')
<div class="mt-16 md:ml-64 sm:ml-12 lg:ml-64">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <a href="{{ route('dupak.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>

                <h1 class="mb-3 text-2xl font-semibold">Formulir Detil Pengajuan DUPAK</h1>
                <h2 class="text-xl">Daftar Usulan Penetapan Angka Kredit</h2>

                <form method="POST" action="{{ route('dupak.dupak.detil_pengajuan.store', [$category, $pengajuan->id]) }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="id_komponen" value="{{ $komponen->id }}">

                    <!-- Unsur Utama -->
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h2 class="mb-2 text-2xl font-medium text-gray-900">{{ $komponen->nama }}</h2>
                        <p class="mb-2 text-gray-600">Sub-kategori dari: {{ ucfirst(str_replace('-', ' ', $category)) }}</p>
                        <p class="mb-6 text-xs text-indigo-600 font-medium">Satuan Hasil: {{ $komponen->satuanHasil ?: 'Butir Kegiatan' }}</p>


                        <div class="space-y-4 bg-white p-4 border rounded-lg shadow-sm">
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Detail Butir Kegiatan</label>
                                <select name="id_jenis_input" id="id_jenis_input" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Salah Satu --</option>
                                    @foreach($jenisInputs as $input)
                                    <option value="{{ $input->id }}" data-nilai="{{ $input->nilai_baku }}">
                                        {{ $input->nama }} (AK: {{ $input->nilai_baku }})
                                    </option>tung
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih butir kegiatan untuk melihat Angka Kredit (AK) baku.</p>
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-bold text-gray-700">Periode Pengajuan</label>
                                <input id="periode_pengajuan" name="periode_pengajuan" type="text" name="periode" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: Semester Ganjil 2025/2026">
                            </div>

                            @if($isPerkuliahan)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 p-3 bg-blue-50 rounded-md border border-blue-100">

                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-700">Jumlah SKS</label>
                                    <input type="number" step="0.1" name="sks" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-700">Jumlah Kelas</label>
                                    <input type="number" name="jumlah_kelas" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                            </div>
                            @endif

                            @if($isBimbinganTA)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 p-3 bg-indigo-50 rounded-md border border-indigo-100">
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-700">Periode</label>
                                    <input type="text" name="tahun_ajaran" placeholder="Contoh: 2023/2024" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-700">Peran Pembimbing</label>
                                    <select name="peran" id="peran_bimbingan" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="Pembimbing Utama">Pembimbing Utama</option>
                                        <option value="Pembimbing Pendamping">Pembimbing Pendamping</option>
                                    </select>
                                </div>
                            </div>
                            <div class="p-2 bg-yellow-50 border-l-4 border-yellow-400 text-xs text-yellow-700 mt-2">
                                <strong>Info Capping:</strong> Maksimal AK Bimbingan TA adalah 32 per periode pengajuan.
                                Kuota mahasiswa per kategori: Disertasi (4), Tesis (6), Skripsi (8), Laporan Akhir (10).
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-700">Volume / Jumlah</label>
                                    <input type="number" name="volume" id="volume" value="1" min="0.1" step="0.1" required
                                        {{ $isPerkuliahan ? 'readonly' : '' }}
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm {{ $isPerkuliahan ? 'bg-gray-100 cursor-not-allowed' : '' }}">

                                    <!-- Tempat breakdown kalkulasi SKS muncul -->
                                    <div id="perkuliahan-calc-preview" class="mt-1 min-h-[1rem]"></div>

                                    <p class="text-xs text-gray-500 mt-1">Jumlah satuan hasil (default: 1 {{ $komponen->satuanHasil ?: 'butir' }}).</p>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-700">Angka Kredit Total (Preview)</label>
                                    <input type="text" id="ak_preview" readonly
                                        class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm sm:text-sm text-gray-700 font-bold"
                                        value="0.000">
                                    <p class="text-xs text-gray-500 mt-1">Dihitung otomatis dari AK Baku × Volume[cite: 1].</p>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Uraian / Deskripsi Kegiatan</label>
                                <input type="text" name="deskripsi_kegiatan" required placeholder="Contoh: Nama Perguruan Tinggi, Judul Penelitian, atau Nama Mata Kuliah"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Link Bukti Pendukung</label>
                                <input type="url" name="link_bukti_pendukung" required placeholder="https://drive.google.com/..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition">
                            Simpan Data {{ ucfirst(str_replace('-', ' ', $category)) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Fungsi Utama: Menghitung volume berdasarkan SKS dan Kelas 
     * Khusus Komponen Perkuliahan sesuai scoring_mechanism_dupak.txt[cite: 1]
     */
    function calculatePerkuliahanVolume() {
        const sksInput = document.querySelector('input[name="sks"]');
        const kelasInput = document.querySelector('input[name="jumlah_kelas"]');
        const volumeInput = document.getElementById('volume');
        const previewElement = document.getElementById('perkuliahan-calc-preview');

        if (!sksInput || !kelasInput || !volumeInput) return;

        const sks = parseFloat(sksInput.value) || 0;
        const kelas = parseInt(kelasInput.value) || 0;
        const totalSks = sks * kelas;

        let finalVolume = 0;
        let calculationLabel = "";

        // Logika Batas 10 SKS (SKS > 10 menggunakan faktor 0.5 untuk overload)[cite: 1]
        if (totalSks > 10) {
            const normalPortion = 10;
            const overloadPortion = totalSks - 10;

            // 10 SKS pertama faktor 1.0, sisanya faktor 0.5[cite: 1]
            finalVolume = (normalPortion * 1.0) + (overloadPortion * 0.5);
            calculationLabel = `(10 × 1.0) + (${overloadPortion.toFixed(1)} × 0.5)`;
        } else {
            // Jika total <= 10, faktor pengali full 1.0[cite: 1]
            finalVolume = totalSks * 1.0;
            calculationLabel = `${totalSks.toFixed(1)} × 1.0`;
        }

        // Set nilai ke input volume
        volumeInput.value = finalVolume;

        // Tampilkan breakdown teks agar user paham asalnya
        if (previewElement) {
            previewElement.innerHTML = `<span class="text-indigo-600 font-medium text-xs italic">Kalkulasi: ${calculationLabel} = ${finalVolume}</span>`;
        }

        // Trigger event input agar AK Total Preview ikut terupdate
        volumeInput.dispatchEvent(new Event('input'));
    }

    /**
     * Fungsi Sinkronisasi: Update Preview Angka Kredit Total 
     * Rumus: AK Baku × Volume[cite: 1]
     */
    function updateAkPreview() {
        const jenisSelect = document.getElementById('id_jenis_input');
        const volumeInput = document.getElementById('volume');
        const akPreview = document.getElementById('ak_preview');
        const peranSelect = document.getElementById('peran_bimbingan');

        if (!jenisSelect || !volumeInput || !akPreview) return;

        const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];

        // Logika khusus Bimbingan TA (Component 6)
        if (peranSelect) {
            const jenisNama = selectedOption.text.toLowerCase();
            const peran = peranSelect.value;

            if (jenisNama.includes('disertasi')) {
                nilaiBaku = (peran === 'Pembimbing Utama') ? 8.0 : 6.0;
            } else if (jenisNama.includes('tesis')) {
                nilaiBaku = (peran === 'Pembimbing Utama') ? 3.0 : 2.0;
            } else if (jenisNama.includes('skripsi') || jenisNama.includes('laporan akhir')) {
                nilaiBaku = (peran === 'Pembimbing Utama') ? 1.0 : 0.5;
            }
        }

        const volume = parseFloat(volumeInput.value) || 0;

        const total = nilaiBaku * volume;
        akPreview.value = total.toFixed(3);
    }

    // Inisialisasi Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('id_jenis_input');
        const volumeInput = document.getElementById('volume');
        const sksInput = document.querySelector('input[name="sks"]');
        const kelasInput = document.querySelector('input[name="jumlah_kelas"]');
        const peranSelect = document.getElementById('peran_bimbingan');

        // Listeners untuk SKS & Kelas (Jika ada di view)
        if (sksInput && kelasInput) {
            [sksInput, kelasInput].forEach(el => {
                el.addEventListener('input', calculatePerkuliahanVolume);
            });
        }

        // Listeners untuk perubahan dropdown kegiatan atau volume
        if (jenisSelect) {
            jenisSelect.addEventListener('change', updateAkPreview);
        }

        if (peranSelect) {
            peranSelect.addEventListener('change', updateAkPreview);
        }

        if (volumeInput) {
            volumeInput.addEventListener('input', updateAkPreview);
        }

        // Jalankan kalkulasi awal (jika ada data lama/edit mode)
        if (sksInput && kelasInput && (sksInput.value || kelasInput.value)) {
            calculatePerkuliahanVolume();
        } else {
            updateAkPreview();
        }
    });
</script>
@endsection