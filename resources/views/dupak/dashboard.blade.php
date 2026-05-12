@extends('layouts.app')

@section('content')

<x-dupak.popup-tambah-kegiatan :kegiatanUtama="$kegiatanUtama" :pengajuanId="$submissions['latest']->id ?? null" />

<div class="py-6" x-data="{ tab: 'personal' }">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>

        {{-- Statistik Admin (di luar tab) --}}
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
                Dasbor DUPAK ===
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
                    <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs">
                        {{ $penugasanTpak->count() }}
                    </span>
                </button>
                @endif
            </div>
        </div>

        <div class="bg-white shadow rounded-b-lg p-6">

            {{-- TAB PERSONAL --}}
            <div x-show="tab === 'personal'">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                    {{-- Kolom Kiri: Informasi KUM --}}
                    <div class="lg:col-span-2">
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

                        {{-- Validasi Card (Admin Only) --}}
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
                        @elseif (isset($user) && isset($dosen))
                        <div class="p-6 border rounded-lg bg-white shadow-sm">
                            <h3 class="mb-4 text-sm font-semibold text-gray-700">
                                <i class="fas fa-user-circle mr-2 text-blue-900"></i>
                                Anda saat ini masuk sebagai Pengaju DUPAK
                            </h3>
                            <a href="{{ route('dupak.validasi.index') }}" class="px-4 py-2 text-sm text-white bg-blue-900 rounded hover:bg-blue-950 inline-block">
                                Menjadi TPAK
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Daftar Pengajuan --}}
                <div class="mt-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold">Daftar Pengajuan DUPAK</h3>

                        @if (!$user->is_admin)
                        @php
                        $buttonDisabled = $submissions['has_pending'] || $isMaxJfa;
                        @endphp
                        <a href="{{ $buttonDisabled ? '#' : route('dupak.pengajuan.create', ['userId' => $user->id]) }}"
                           class="px-4 py-2 text-xs font-semibold text-white uppercase rounded-md
                           {{ $buttonDisabled ? 'bg-gray-400 cursor-not-allowed opacity-60' : 'bg-blue-900 hover:bg-blue-950' }}">
                            Buat Pengajuan Baru
                        </a>
                        @endif
                    </div>

                    @if(!$user->is_admin && $submissions['has_pending'])
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-700 rounded">
                        Lengkapi detail kegiatan hingga memenuhi syarat pengajuan baru.
                    </div>
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
                    <table class="min-w-full divide-y divide-gray-200">
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
                                       class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-md text-sm transition">
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
            const percent = progressBar.getAttribute('data-percent');
            progressBar.style.width = percent + '%';
        }
    });
</script>
@endsection
