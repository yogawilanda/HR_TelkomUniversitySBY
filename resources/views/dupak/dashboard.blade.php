@extends('layouts.app')

@section('content')

<x-dupak.popup-tambah-kegiatan :kegiatanUtama="$kegiatanUtama" :pengajuanId="$submissions['latest']->id ?? null" />

<div class="py-6" x-data="{ tab: 'personal' }"></div>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>

        {{-- Statistik Admin --}}
        @if ($user->is_admin)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="p-4 border rounded-lg border-blue-100 bg-white shadow-sm">
                <span class="text-xs font-semibold text-blue-600 uppercase">Total Pengajuan</span>
                <div class="text-2xl font-bold text-blue-900">{{ $totalSeluruhPengajuan ?? 0 }}</div>
            </div>
            <div class="p-4 border rounded-lg border-green-100 bg-white shadow-sm">
                <span class="text-xs font-semibold text-green-600 uppercase">Pengajuan Selesai</span>
                <div class="text-2xl font-bold text-green-900">{{ $statistik['selesai'] ?? 0 }}</div>
            </div>
            <div class="p-4 border rounded-lg border-yellow-100 bg-white shadow-sm">
                <span class="text-xs font-semibold text-yellow-600 uppercase">Perlu Validasi</span>
                <div class="text-2xl font-bold text-yellow-900">{{ $statistik['pending'] ?? 0 }}</div>
            </div>
        </div>
        @endif

        <div class="bg-white shadow rounded-t-lg p-6 pb-0">
            <h1 class="text-2xl font-semibold mb-6">
                Dasbor DUPAK
                @if ($user->is_admin)
                <span class="text-sm text-gray-500 font-normal">(Admin)</span>
                @endif
            </h1>

            <div class="flex space-x-4">
                <button @click="tab = 'personal'"
                    :class="tab === 'personal' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500'"
                    class="px-4 py-2 font-semibold border-b-2 transition-colors">
                    DUPAK Pribadi
                </button>

                @if($isTpak)
                <button @click="tab = 'tpak'"
                    :class="tab === 'tpak' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500'"
                    class="px-4 py-2 font-semibold border-b-2 transition-colors flex items-center">
                    Penugasan TPAK
                    <span class="ml-2 px-2 py-0.5 bg-blue-900 text-white rounded-full text-xs">
                        {{ $penugasanTpak->count() }}
                    </span>
                </button>
                @endif
            </div>
        </div>

        <div class="bg-white shadow rounded-b-lg p-6">

            {{-- TAB PERSONAL --}}
            <div x-show="tab === 'personal'">
                {{-- Grid utama 3 kolom --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    {{-- Kolom Kiri: Informasi KUM / Status Profil --}}
                    <div class="lg:col-span-2">
                        @if (($user->is_admin || $isTpak) && !$dosen)
                            <div class="p-6 border rounded-lg bg-yellow-50 border-yellow-200 text-yellow-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Anda terdaftar sebagai Admin/TPAK namun bukan Dosen. Pengajuan DUPAK hanya dapat dilakukan oleh Dosen. Hubungi Admin SDM untuk proses pengubahan jabatan apabila terdapat kesalahan data.
                            </div>
                        @elseif (!$user->is_admin && !$isTpak && !$dosen)
                            <div class="p-6 border rounded-lg bg-red-50 border-red-200 text-red-800 text-sm text-center">
                                <i class="fas fa-ban mr-2"></i> Anda tidak memiliki izin untuk mengakses halaman ini.
                            </div>
                        @else
                            {{-- ALTERNATE FLOW: Tampilkan Peringatan Profil Belum Lengkap dengan dimensi py-8 & min-h agar proporsional dengan kolom identitas --}}
                            @if($isProfileIncomplete)
                                <div class="p-8 border rounded-lg bg-yellow-50 border-yellow-200 text-yellow-800 text-sm min-h-[250px] flex items-center">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle mt-1 mr-4 text-2xl text-yellow-600 animate-pulse"></i>
                                        <div>
                                            <h4 class="font-semibold text-yellow-900 mb-2 text-base">Profil Belum Lengkap</h4>
                                            <p class="text-yellow-700 leading-relaxed">
                                                Data profil Anda belum lengkap di sistem. Untuk dapat mengajukan DUPAK baru, Anda wajib memiliki data di bawah ini:
                                            </p>
                                            <ul class="list-disc list-inside mt-3 text-yellow-700 space-y-1.5 font-medium">
                                                <li>NIK / NIP pada akun Anda.</li>
                                                <li>NIDN atau NIDK pada data Dosen.</li>
                                                <li>Data Riwayat JFA yang aktif.</li>
                                            </ul>
                                            <p class="mt-4 text-yellow-700 text-xs italic">
                                                *Silakan hubungi Admin SDM / Kepegawaian untuk melengkapi data tersebut.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- MAIN FLOW: Dosen dengan profil lengkap --}}
                                @if($submissions['latest'])
                                    @include('partials.dupak.info-kum')
                                @else
                                    <div class="p-10 border-2 border-dashed border-gray-300 text-center rounded-lg">
                                        <p class="text-gray-500">Belum ada pengajuan aktif.</p>
                                        @if($isMaxJfa)
                                            <div class="mt-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded-md text-sm">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Anda telah mencapai jabatan tertinggi (Guru Besar). Tidak perlu pengajuan kenaikan jabatan lagi.
                                            </div>
                                        @else
                                            <a href="{{ route('dupak.pengajuan.create', ['userId' => $user->id]) }}" class="mt-4 inline-block bg-blue-900 text-white px-4 py-2 rounded hover:bg-blue-950">Buat Pengajuan Baru</a>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>

                    {{-- Kolom Kanan: Identitas & Aksi --}}
                    <div id="containerRightSide" class="space-y-6">
                        @if (isset($user) && isset($dosen))
                        <div class="p-6 border rounded-lg bg-white shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-blue-900"></i> Identitas Pengaju
                            </h3>
                            <div class="space-y-2 text-sm text-gray-700">
                                <div class="flex justify-between border-b border-gray-50 pb-1"><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $user->nama_lengkap ?? 'N/A' }}</span></div>
                                <div class="flex justify-between border-b border-gray-50 pb-1"><span class="text-gray-500">NIDN:</span> <span class="font-medium">{{ $dosen->nidn ?? 'N/A' }}</span></div>
                                <div class="flex justify-between border-b border-gray-50 pb-1"><span class="text-gray-500">Jabatan:</span> <span class="font-medium">{{ $jfa['current'] ?? 'Belum diisi' }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">NIK:</span> <span class="font-medium">{{ $user->nik ?? 'N/A' }}</span></div>
                            </div>
                        </div>
                        @endif

                        @if (auth()->user()->is_admin)
                        <div class="p-6 border rounded-lg bg-white shadow-sm border-l-4 border-l-blue-900">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Validasi DUPAK</h3>
                            <p class="text-sm text-gray-600 mb-4">Menu untuk melakukan verifikasi dan validasi butir kegiatan yang diajukan dosen.</p>
                            <a href="{{ route('dupak.validasi.index') }}" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950 text-sm inline-block">
                                Validasi Pengajuan
                            </a>
                        </div>

                        <div class="p-6 border rounded-lg bg-white shadow-sm border-l-4 border-l-blue-900">
                            <h3 class="text-lg font-medium">Pengelolaan TPAK</h3>
                            <p class="text-gray-600 mb-4 text-sm">Kelola penunjukan TPAK</p>
                            <a href="{{ route('dupak.penunjukan_tpak.index') }}" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950 text-sm inline-block">
                                Kelola TPAK
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Daftar Pengajuan --}}
                <div class="mt-10">
                    <div class="flex justify-between items-center mb-6 ">
                        <h3 class="text-xl font-semibold">Daftar Pengajuan DUPAK</h3>
                    </div>

                    @if(!$user->is_admin && $submissions['has_pending'])
                        @php
                            $latestSub = $submissions['latest'];
                            $isEditable = $latestSub && in_array($latestSub->status, ['Draft', 'Pending', 'Revisi']);
                        @endphp
                        @if($isEditable)
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-700 rounded flex items-center gap-2">
                            <i class="fas fa-edit text-yellow-600"></i>
                            <span>Lengkapi detail kegiatan pada pengajuan aktif Anda (Draft), lalu kirimkan untuk dinilai TPAK.</span>
                        </div>
                        @else
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <span>Pengajuan DUPAK Anda saat ini sedang dinilai oleh TPAK. Pembuatan pengajuan baru akan terbuka setelah penilaian selesai.</span>
                        </div>
                        @endif
                    @endif

                    @if(!$user->is_admin && $isMaxJfa)
                    <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded">
                        <i class="fas fa-check-circle mr-1"></i>
                        Anda sudah mencapai jabatan fungsional tertinggi (Guru Besar). Pengajuan kenaikan jabatan tidak tersedia.
                    </div>
                    @endif

                    @include('partials.dupak.table-pribadi')
                </div>
            </div>

            {{-- TAB TPAK --}}
            @if($isTpak)
            <div x-show="tab === 'tpak'" x-cloak>
                <div class="p-4 border-l-4 border-blue-900 bg-blue-50 mb-6">
                    <p class="text-sm text-blue-900 font-medium">
                        Anda ditugaskan sebagai Tim Penilai (TPAK) untuk pengajuan berikut:
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 rounded-b-lg">
                        <thead class="bg-blue-900 text-white text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Dosen Pengaju</th>
                                <th class="px-6 py-3 text-left">Periode</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($penugasanTpak as $tugas)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ $tugas->pengajuan->dosen->pegawai->nama_lengkap ?? 'Nama tidak ditemukan' }}
                                    </div>
                                    <div class="text-xs text-gray-500">NIDN: {{ $tugas->pengajuan->dosen->nidn ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $tugas->pengajuan->start }} - {{ $tugas->pengajuan->end }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('dupak.validasi.show', $tugas->pengajuan->id) }}"
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-sm transition">
                                         Mulai Penilaian
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            // Menggunakan requestAnimationFrame untuk mencegah layout thrashing saat mengisi lebar bar
            requestAnimationFrame(() => {
                const percent = progressBar.getAttribute('data-percent');
                progressBar.style.width = percent + '%';
            });
        }
    });
</script>
@endsection