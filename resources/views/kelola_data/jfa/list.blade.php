@php
    $active_sidebar = 'Daftar JFA';
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

            span {
                color: white;
            }
        }
    </style>
@endsection

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Daftar Jabatan Fungsional Akademik
                    (JFA)</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">
                Kelola data jenjang karir akademik dosen di sini.
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <x-print-tb target_id="jfaTable"></x-print-tb>
            <x-export-csv-tb target_id="jfaTable"></x-export-csv-tb>

            <a href="{{ route('manage.jfa.new') }}" class="flex rounded-[5.87px]">
                <div
                    class="flex justify-center items-center gap-[5.87px] bg-[#0070ff] px-[11.75px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    {{-- Modal khusus JFA jika diperlukan --}}
    <x-modal-new-sk-ypt keperluan="JFA" route_khusus='/manage/jfa/fill-sk-ypt'></x-modal-new-sk-ypt>

    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <x-tb id="jfaTable">
            <x-slot:table_header>
                <x-tb-td type="select" nama="nama_dosen" sorting=true>Nama Dosen</x-tb-td>
                <x-tb-td type="select" nama="nama_jabatan" sorting=true>Jabatan</x-tb-td>
                <x-tb-td nama="sk_llkdikti_id" sorting=true>SK LLDikti</x-tb-td>
                <x-tb-td nama="sk_ypt" sorting=true>SK Pengakuan YPT</x-tb-td>
                <x-tb-td type="select" nama="tmt_start" sorting=true>TMT Mulai</x-tb-td>
                <x-tb-td type="select" nama="tmt_end" sorting=true>TMT Selesai</x-tb-td>
                <x-tb-td nama="created_updated" sorting=true>Diperbarui</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @forelse ($jfas as $jfa)
                    <x-tb-cl id="{{ $jfa->id }}">
                        {{-- Nama Dosen --}}
                        <x-tb-cl-fill clsText="text-xs">
                            {{ $jfa->dosen->pegawai->nama_lengkap }}
                        </x-tb-cl-fill>

                        {{-- Nama Jabatan --}}
                        <x-tb-cl-fill>
                            <div class="flex font-semibold justify-center flex-col">
                                <span class="text-md font-bold text-xs">{{ $jfa->jfa->kode }}</span> <span
                                    class="text-sm text-gray-600 text-xs">{{ $jfa->jfa->nama_jabatan }}</span>
                            </div>
                        </x-tb-cl-fill>

                        {{-- SK LLDikti --}}
                        <x-tb-cl-fill>
                            <div class="flex justify-center">
                                @if ($jfa->sk_llkdikti_id == null)
                                    <p class="text-xs text-gray-500 flex flex-col items-center">
                                        <span class="opacity-50">Belum ada SK</span>
                                        <a href="{{ route('manage.jfa.update', ['id_jfa' => $jfa->id]) }}"
                                            class="route_pop_up text-blue-600">Set Sekarang</a>
                                    </p>
                                @else
                                    <a href="{{ route('manage.sk.view', ['id_sk_or_sk_number' => $jfa->sk_dikti->id]) }}"
                                        target="_blank" class="text-blue-600 text-xs hover:underline">
                                        <i class="bi bi-file-earmark-check"></i> {{ $jfa->sk_dikti->no_sk }}
                                    </a>
                                @endif
                            </div>
                        </x-tb-cl-fill>

                        {{-- SK Pengakuan YPT --}}
                        <x-tb-cl-fill>
                            <div class="flex justify-center">
                                @if ($jfa->sk_ypt == null)
                                    <p class="text-xs text-gray-500 flex flex-col items-center">
                                        <span class="opacity-50">Belum ada SK</span>
                                        <a href="{{ route('manage.jfa.update', ['id_jfa' => $jfa->id]) }}"
                                            class="route_pop_up text-blue-600">Set Sekarang</a>
                                    </p>
                                @else
                                    <a href="{{ route('manage.sk.view', ['id_sk_or_sk_number' => $jfa->sk_ypt->id]) }}"
                                        target="_blank" class="text-blue-600 text-xs hover:underline">
                                        <i class="bi bi-file-earmark-check"></i> {{ $jfa->sk_ypt->no_sk }}
                                    </a>
                                @endif
                            </div>
                        </x-tb-cl-fill>

                        {{-- TMT Mulai --}}
                        <x-tb-cl-fill>
                            <div class="flex justify-center italic text-sm">
                                {{ $jfa->tmt_mulai }}
                            </div>
                        </x-tb-cl-fill>

                        {{-- TMT Selesai --}}
                        <x-tb-cl-fill>
                            <div class="flex justify-center">
                                @if ($jfa->tmt_selesai == null)
                                    <span class="text-gray-500 text-sm font-medium">Belum di set</span>
                                @else
                                    {{ $jfa->tmt_selesai }}
                                @endif
                            </div>
                        </x-tb-cl-fill>

                        {{-- Diperbarui --}}
                        <x-tb-cl-fill>
                            <div class="flex justify-center text-[10px]">
                                {{ $jfa->created_at }}
                            </div>
                        </x-tb-cl-fill>

                        {{-- Action --}}
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border shadow-sm" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li>
                                            <a href="{{ route('manage.jfa.update', ['id_jfa' => $jfa->id]) }}"
                                                class="dropdown-item hover:bg-blue-600 hover:text-white route_pop_up">
                                                <i class="bi bi-pencil-square mr-2"></i> Ubah Data
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('manage.jfa.history',['id_user' => $jfa->dosen->pegawai->id]) }}"
                                                class="dropdown-item hover:bg-blue-500 route_pop_up  hover:text-white"
                                                href="#">
                                                Riwayat JFA
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @empty
                    <tr class="text-center">
                        <td colspan="8" class="py-10 text-gray-400 italic">Data JFA tidak ditemukan</td>
                    </tr>
                @endforelse
            </x-slot:table_column>
        </x-tb>
    </div>
@endsection
