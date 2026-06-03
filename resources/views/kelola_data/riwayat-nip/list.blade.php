@php
    $active_sidebar = 'Daftar NIP';
@endphp

@extends('kelola_data.base')

@section('header-base')
    <script src="https://balkan.app/js/OrgChart.js" defer></script>
    <style>
        .max-w-100 {
            max-width: 100% !important;
        }

        .nav-active {
            background-color: #0070ff;
        }

        .nav-active span {
            color: white;
        }
    </style>
@endsection

@section('page-name')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Riwayat NIP Pegawai</span>
            </div>
            <span class="font-normal text-[10.28px] text-[#1f2028]">Kelola dan pantau riwayat Nomor Induk Pegawai serta
                status kepegawaian.</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <x-print-tb target_id="nipTable"></x-print-tb>
            <x-export-csv-tb target_id="nipTable"></x-export-csv-tb>
            <a href="{{ route('manage.riwayat-nip.new') }}" class="flex rounded-[5.87px]">
                <div
                    class="flex justify-center items-center gap-[5.87px] bg-[#0070ff] px-[11.75px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] text-white">Tambah NIP</span>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    {{-- Modal View (Opsional disesuaikan) --}}
    <x-modal-view :footer="false" :head="false" id="nip-detail" title="Detail NIP">
        <div class="flex flex-col gap-3 px-8 py-8" id="modal-content">
            <span class="font-semibold text-xl text-[#101828]">Informasi NIP</span>
            <div class="flex gap-12 w-full">
                <div class="flex flex-col gap-2 w-1/3 text-sm font-light">
                    <span>NIP</span>
                    <span>Status Pegawai</span>
                    <span>No. SK</span>
                    <span>Tanggal Dibuat</span>
                </div>
                <div class="flex flex-col gap-2 w-2/3 text-sm font-normal">
                    <span id="view-nip">-</span>
                    <span id="view-status">-</span>
                    <span id="view-sk">-</span>
                    <span id="view-created">-</span>
                </div>
            </div>
        </div>
    </x-modal-view>

    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <x-tb id="nipTable">
            <x-slot:table_header>
                <x-tb-td nama="nama" sorting=true>Pemilik</x-tb-td>
                <x-tb-td nama="nip" sorting=true>NIP</x-tb-td>
                <x-tb-td nama="status_pegawai" sorting=true>Status Pegawai</x-tb-td>
                <x-tb-td nama="no_sk" sorting=true>No. SK YPT</x-tb-td>
                <x-tb-td nama="created_at" sorting=true>Tanggal Input</x-tb-td>
                <x-tb-td nama="action">Aksi</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @forelse ($nips as $item)
                {{-- {{ dd($item) }} --}}
                    <x-tb-cl id="{{ $item->id }}">
                        {{-- NIP --}}
                        <x-tb-cl-fill>
                            <div class="flex flex-col">
                                <span class="font-bold text-[#101828] text-sm">{{ $item->pegawai->nama_lengkap ?? 'Belum Terpetakan' }}</span>
                                {{-- <span class="text-[10px] text-gray-400">ID: {{ Str::limit($item->id, 8) }}</span> --}}
                            </div>
                        </x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <div class="flex flex-col">
                                <span class="font-bold text-[#101828] text-sm">{{ $item->nip }}</span>
                                {{-- <span class="text-[10px] text-gray-400">ID: {{ Str::limit($item->id, 8) }}</span> --}}
                            </div>
                        </x-tb-cl-fill>

                        {{-- Status Pegawai (Badge Berwarna) --}}
                        <x-tb-cl-fill>
                            @php
                                $status = $item->statusPegawai->status_pegawai ?? 'TIDAK DIKETAHUI';
                                $badgeColor = match (strtoupper($status)) {
                                    'PEGAWAI TETAP' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'PEGAWAI KONTRAK' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'CALON PEGAWAI' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium border {{ $badgeColor }}">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ str_replace('text', 'fill', explode(' ', $badgeColor)[1]) }}"
                                    fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                {{ $status }}
                            </span>
                        </x-tb-cl-fill>

                        {{-- No SK YPT (Styled File Link) --}}
                        <x-tb-cl-fill>
                            @if ($item->sk_or_amandemen)
                                {{-- {{ dd($item->sk_ypt_or_amandemen) }} --}}
                                <a href="{{ route('manage.sk.view', ['id_sk_or_sk_number' => $item->sk_or_amandemen->id]) }}"
                                    class="group flex items-center gap-3 p-1.5 rounded-lg border border-transparent hover:border-blue-200 hover:bg-blue-50 transition-all duration-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-red-50 rounded text-red-500 group-hover:scale-110 transition-transform">
                                        <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                                    </div>
                                    <div class="flex flex-col items-start">
                                        <span
                                            class="text-[11px] font-semibold text-blue-600 leading-none group-hover:text-blue-800">{{ $item->sk_or_amandemen->no_sk }}</span>
                                        <span
                                            class="text-[9px] text-gray-400 mt-1 uppercase">{{ $item->sk_or_amandemen->tipe_sk ?? 'SK YPT' }}</span>
                                    </div>
                                </a>
                            @else
                                <span class="text-gray-400 italic text-xs">Tidak ada file</span>
                            @endif
                        </x-tb-cl-fill>

                        {{-- Tanggal Input --}}
                        <x-tb-cl-fill>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="bi bi-calendar3 text-[10px]"></i>
                                <span
                                    class="text-xs">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</span>
                            </div>
                        </x-tb-cl-fill>

                        {{-- Action Buttons --}}
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-2">
                                <div class="dropdown">
                                    <button
                                        class="flex items-center justify-center w-7 h-7 bg-gray-50 rounded-md border border-gray-200 hover:bg-gray-100"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical text-xs"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border-0 text-sm">
                                        <li><a href="{{ route('manage.riwayat-nip.update-data',['id_nip'=> $item->id]) }}" class="dropdown-item py-2" href="#">
                                            <i class="bi bi-pencil-square me-2 text-blue-500"></i> Edit Data</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a href="{{ route('manage.riwayat-nip.history',['id_pegawai'=> $item->pegawai->id]) }}" class="dropdown-item py-2" href="#">
                                            <i class="bi bi-pencil-square me-2 text-blue-500"></i>Riwayat NIP Pegawai Ini</a></li>
                                        {{-- <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i> Hapus</a></li> --}}
                                    </ul>
                                </div>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @empty
                    <x-tb-cl id="empty">
                        <x-tb-cl-fill colspan="5" class="text-center py-10">
                            <div class="flex flex-col items-center opacity-50">
                                <i class="bi bi-database-exclamation text-4xl mb-2"></i>
                                <p>Belum ada data riwayat NIP.</p>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforelse
            </x-slot:table_column>
        </x-tb>
    </div>
@endsection
