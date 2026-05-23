@php
    $active_sidebar = 'Daftar Formasi';
@endphp
@extends('kelola_data.base')
@section('header-base')
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- OrgChart.js -->
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
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch"><span
                    class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Pangkat Golongan
                    Dosen</span>
            </div><span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat melihat semua JFA yang terdaftar di sistem disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">


            <x-print-tb target_id="formasiTable"></x-print-tb>
            <x-export-csv-tb target_id="formasiTable"></x-export-csv-tb>

            <a href="{{ route('manage.pangkat-golongan.new') }}" class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </a>
        </div>

    </div>
@endsection
@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">


        <x-tb id="formasiTable">
            <x-slot:table_header>
                <x-tb-td type="select" nama="level" sorting=true>Nama Dosen</x-tb-td>
                <x-tb-td nama="pangkat" sorting=true>Pangkat</x-tb-td>
                <x-tb-td nama="golongan" sorting=true>Golongan</x-tb-td>
                <x-tb-td nama="tipe_bagian" sorting=true>SK LLKDIKTI atau AMANDEMEN</x-tb-td>
                <x-tb-td type="select" nama="atasan" sorting=true>TMT Mulai</x-tb-td>
                {{-- <x-tb-td nama="tmt_selesai" sorting=true>TMT Selesai</x-tb-td> --}}
                <x-tb-td nama="kuota" sorting=true>Action</x-tb-td>
                {{-- <x-tb-td nama="email_pribadi"></x-tb-td> --}}
            </x-slot:table_header>

            <x-slot:table_column>
                @forelse ($jpgs as $jpg)
                    @if ($jpg->tmt_selesai == null)
                        {{-- {{ dd($formation) }} --}}
                        <x-tb-cl id="">
                            <x-tb-cl-fill>
                                {{ $jpg->dosen->pegawai->nama_lengkap }}
                            </x-tb-cl-fill>
                            <x-tb-cl-fill>
                                {{ $jpg->refPangkatGolongan->pangkat }}
                            </x-tb-cl-fill>
                            <x-tb-cl-fill>
                                {{ $jpg->refPangkatGolongan->golongan }}
                            </x-tb-cl-fill>
                            <x-tb-cl-fill>
                                @if (isset($jpg->skLlDikti) && $jpg->skLlDikti->no_sk)
                                    <a href="{{ route('manage.sk.view',['id_sk_or_sk_number' => $jpg->skLlDikti->id])}}" target="_blank"
                                        class="inline-flex items-center px-3 py-2 border border-blue-600 text-blue-600 font-medium text-xs rounded-lg hover:bg-blue-50 transition shadow-sm
                                        max-w-[200px] w-fit overflow-hidden">

                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>

                                        <span class="break-words whitespace-normal leading-relaxed">
                                            {{ $jpg->skLlDikti->no_sk }}
                                        </span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs px-2">-</span>
                                @endif
                            </x-tb-cl-fill>
                            <x-tb-cl-fill>
                                {{-- {{ dd($jpg) }} --}}
                                {{ $jpg->tmt_mulai }}
                            </x-tb-cl-fill>
                            <x-tb-cl-fill>
                                <div class="flex items-center justify-center gap-3">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('manage.pangkat-golongan.update', ['id_pg' => $jpg->id]) }}"
                                                    class="dropdown-item hover:bg-blue-500 hover:text-white" href="#">
                                                    Ubah Data
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                href="{{ route('manage.pangkat-golongan.history',['id_user' => $jpg->dosen->pegawai->id]) }}"
                                                class="dropdown-item hover:bg-blue-500 hover:text-white" href="#">
                                                    Riwayat Pangkat Golongan Dosen Ini
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </x-tb-cl-fill>
                        </x-tb-cl>
                    @endif
                @empty
                    <p>No Data</p>
                @endforelse
            </x-slot:table_column>
        </x-tb>





    </div>


@endsection
