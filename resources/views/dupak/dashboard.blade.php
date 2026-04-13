@extends('layouts.app')

@section('content')

<x-dupak.popup-tambah-kegiatan />
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>

        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-semibold mb-6">Selamat Datang Di Dasbor DUPAK</h1>

            <!-- Data Statistika -->
            <!-- Hanya Admin saja yang dapat melihat komponen ini -->
            @if ($user->is_admin)
            <div class="md:col-span-2 p-6 my-6 border rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">Statistika Pengajuan</h3>
                <p class="text-sm text-gray-600">Ringkasan jumlah pengajuan</p>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Pengajuan Baru</span>
                        <span class="font-medium">{{ $statistik['baru'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Pengajuan Diproses</span>
                        <span class="font-medium">{{ $statistik['diproses'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Pengajuan Selesai</span>
                        <span class="font-medium">{{ $statistik['selesai'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- CARD: Informasi KUM --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Informasi Kum Container --}}

                <!-- Jika User belum membuat pengajuan maka ganti dengan "Anda Belum Mengajukan pengajuan -->
                <!-- jika user adalah admin, tapi bukan dosen -->
                @if (!$userIsAdminButNotDosen)
                <div class="md:col-span-2 p-6 border rounded-lg">
                    <div class="flex justify-between items-start">

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informasi KUM</h3>
                            <p class="text-sm text-gray-600">Ringkasan KUM, jabatan, dan progress</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500">Jabatan</span>
                            <div class="text-sm font-semibold">{{ $jfa['current'] ?? 'Belum diisi' }}</div>
                        </div>
                    </div>

                    {{-- KUM Numbers --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <span class="text-xs text-gray-500">KUM Saat Ini</span>
                            <div class="text-2xl font-bold text-blue-900">
                                {{ $kum['current'] }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500">Target KUM ({{ $jfa['next'] ?? 'Belum diisi' }})</span>
                            <div class="text-lg font-semibold">
                                {{ $kum['target'] }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500">Tersisa</span>
                            <div class="text-lg font-semibold">
                                {{ $kum['remaining'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Progress menuju target</span>
                            <span class="font-medium">{{ number_format($kum['percent'], 0) }}%</span>
                        </div>

                        <div class="w-full h-4 bg-gray-200 rounded-full mt-2 overflow-hidden">
                            <div id="progress-bar" class="h-full {{ $kum['statusColor'] }}" data-percent="{{ $kum['percent'] }}"></div>
                        </div>

                        <div class="text-xs text-gray-500 mt-2">
                            Terakhir diperbarui: {{ $kum['updatedAtFormatted'] ?? 'Tidak tersedia' }}
                        </div>
                    </div>

                    {{-- Action Buttons - Flex (Horizontal) --}}
                    <div class="flex gap-2 mt-4">
                        @if($submissions['latest'])
                        <a href="{{ route('dupak.pengajuan.show', $submissions['latest']->id) }}"
                            class="px-4 py-2 text-sm text-white bg-blue-900 rounded hover:bg-blue-950">Detail Kegiatan</a>
                        @else
                        <button disabled title="Anda belum memiliki pengajuan"
                            class="px-4 py-2 text-sm text-white bg-gray-400 rounded cursor-not-allowed">Detail Kegiatan</button>
                        @endif

                        <!-- Tambahkan Kegiatan : Jika belum memiliki pengajuan button dan modal di disable -->
                        <a onclick="openModal()" class="px-4 py-2 text-sm text-blue-900 border border-blue-900 rounded hover:bg-indigo-50">Tambahkan Kegiatan</a>
                    </div>
                </div>
                @endif


                <!-- Informasi Spesifik KUM Berdasarkan Detil Kegiatan -->
                <div class="border rounded-lg p-4 bg-gray-50 text-sm text-gray-700">
                    <div class="text-lg font-medium text-gray-900">
                        Angka Kredit Spesifik
                    </div>
                    <p class="text-sm text-gray-600">
                        Rincian angka kredit per komponen kegiatan
                    </p>


                    <!-- grid 2x2 untuk menampung informasi grid nilai spesifik pengaju -->
                    <div class="grid grid-cols-2 gap-2 mt-6 justify-items-start">
                        <div>
                            <span class="text-xs text-gray-500">Pendidikan</span>
                            <div class="text-lg font-semibold">
                                {{ $kum['pendidikan'] ?? '0' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500">Penelitian</span>
                            <div class="text-lg font-semibold">
                                {{ $kum['penelitian'] ?? '0' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500">Pengabdian</span>
                            <div class="text-lg font-semibold">
                                {{ $kum['pengabdian'] ?? '0' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500">Penunjang</span>
                            <div class="text-lg font-semibold">
                                {{ $kum['penunjang'] ?? '0' }}
                            </div>
                        </div>
                    </div>

                    <!-- border with size around 2 px -->
                    <div class="border-t border-4 rounded mt-6"></div>

                    <!-- button untuk melihat breakdown informasi AK secara spesifik -->
                    <div class="gap-4 mt-8">
                        @if($user->is_admin)
                        <a href="{{ route('dupak.pengajuan.show', $submissions['latest']->id) }}"
                            class="px-4 py-2 text-sm text-white bg-blue-900 rounded hover:bg-blue-950 mt-4 inline-block">Lihat Rincian Kegiatan</a>
                        @else
                        <button disabled title="Anda belum memiliki pengajuan"
                            class="px-4 py-2 text-sm text-white bg-gray-400 rounded cursor-not-allowed mt-4 inline-block">Lihat Rincian Kegiatan</button>
                        @endif
                    </div>

                </div>

                <!-- Identitas Dosen dan/atau TPA yang memiliki status kepegawaian dosen -->
                @if (isset($user) && isset($dosen))
                <div class="p-6 border rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Identitas Pengaju</h3>
                    <div class="space-y-2 text-sm text-gray-700">
                        <div><span class="font-semibold">Nama:</span> {{ $user->nama_lengkap ?? 'N/A' }}</div>
                        <div><span class="font-semibold">NIDN:</span> {{ $dosen->nidn ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Jabatan Saat Ini:</span> {{ $jfa['current'] ?? 'Belum diisi' }}</div>
                        <div><span class="font-semibold">NIK:</span> {{ $user->nik ?? 'N/A' }}</div>
                    </div>
                </div>
                @endif

                {{-- Validasi Card (Admin Only) --}}
                @if (auth()->user()->is_admin)
                <div class="p-6 border rounded-lg">
                    <h3 class="text-lg font-medium">Validasi DUPAK</h3>
                    <p class="text-gray-600 mb-4">Validasi pengajuan dari pegawai.</p>
                    <a href="{{ route('dupak.validasi.index') }}" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950">
                        Validasi Pengajuan
                    </a>
                </div>
                @endif

                @if (auth()->user()->is_admin)
                <div class="p-6 border rounded-lg">
                    <h3 class="text-lg font-medium">Pengelolaan TPAK</h3>
                    <p class="text-gray-600 mb-4">Kelola penunjukan TPAK</p>
                    <a href="{{ route('dupak.penunjukan-tpak.index') }}" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950">
                        Kelola TPAK
                    </a>
                </div>
                @endif
            </div>


        </div>

        {{-- DAFTAR PENGAJUAN --}}
        <div class="bg-white shadow rounded-lg p-6 mt-10">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">
                    Daftar Pengajuan DUPAK
                    @if ($user->is_admin)
                    (Admin)
                    @endif
                </h1>

                <!-- Jika user bukan admin, maka tombol pengajuan akan muncul -->
                @if (!$user->is_dosen)
                @php
                $buttonDisabled = $submissions['has_pending'];
                @endphp

                <a href="{{ $buttonDisabled ? '#' : route('dupak.pengajuan.create', ['userId' => $user->id]) }}"
                    class="px-4 py-2 text-xs font-semibold text-white uppercase rounded-md
                               {{ $buttonDisabled
                                    ? 'bg-gray-400 cursor-not-allowed opacity-60'
                                    : 'bg-blue-900 hover:bg-blue-950' }}">
                    Buat Pengajuan Baru
                </a>
                @endif
            </div>

            {{-- Jika memiliki pengajuan dengan status pending, maka user akan diberikan informasi  --}}
            @if(!$user->is_admin && $submissions['has_pending'])
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-700 rounded">
                Lengkapi detail kegiatan hingga memenuhi syarat pengajuan baru.
            </div>
            @endif

            @php
            $thValue = ["ID", "Nama Dosen", "Tanggal", "Periode", "Status", "Aksi"]
            @endphp

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-900 text-white text-xs uppercase">
                        <tr>
                            @foreach ($thValue as $thDataValue)
                            <th class="px-6 py-3 text-left">{{ $thDataValue }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($submissions['list'] as $item)
                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm font-medium">
                                {{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-6 py-4 text-sm">
                                {{ $item->dosen->pegawai->nama_lengkap ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $item->created_at->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $item->start }} -
                                {{ $item->end }}
                            </td>

                            <td class="px-6 py-4">
                                @php
                                $badgeColor = [
                                'Draft' => 'bg-gray-100 text-gray-800',
                                'Diajukan' => 'bg-yellow-100 text-yellow-800',
                                'Menunggu' => 'bg-indigo-100 text-indigo-800',
                                'Ditolak' => 'bg-red-100 text-red-800',
                                'Diterima' => 'bg-green-100 text-green-800',
                                'Revisi' => 'bg-yellow-100 text-yellow-800',
                                ][$item->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp

                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                    {{ $item->status }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-sm font-medium space-x-2">
                                <a href="{{ route('dupak.pengajuan.show', $item->id) }}" class="text-blue-600">Lihat</a>

                                @if (!$user->is_admin && in_array($item->status, ['Draft','Revisi']))
                                <a href="{{ route('dupak.pengajuan.edit', $item->id) }}" class="text-indigo-600">Edit</a>

                                <form action="{{ route('dupak.pengajuan.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600"
                                        onclick="return confirm('Hapus pengajuan ini?')">Hapus</button>
                                </form>
                                @endif

                                @if ($user->is_admin && $item->status === 'Diajukan')
                                <a href="{{ route('dupak.validasi.show', $item->id) }}" class="text-green-600">Validasi</a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                Belum ada data pengajuan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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
<!-- modal untuk tambah kegiatan -->