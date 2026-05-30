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
                    class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Jenjang Pendidikan
                    Staff</span>
            </div><span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat melihat semua Jenjang Pendidikan Staff yang terdaftar di sistem disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">


            <x-print-tb target_id="formasiTable"></x-print-tb>
            <x-export-csv-tb target_id="formasiTable"></x-export-csv-tb>

            <a href="{{ route('manage.jenjang-pendidikan.new') }}" class="flex rounded-[5.874740123748779px]">
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
                <x-tb-td nama="level" sorting=true>Nama Staff</x-tb-td>
                <x-tb-td type="select" nama="nama_formasi" sorting=true>Jenjang Pendidikan Tertinggi</x-tb-td>
                <x-tb-td type="select" type="select" nama="bagian" sorting=true>Bidang Pendidikan</x-tb-td>
                <x-tb-td nama="tipe_bagian" sorting=true>Tahun Lulus</x-tb-td>
                <x-tb-td nama="kuota" sorting=true>Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @forelse ($results as $result)
                    <x-tb-cl id="">
                        <x-tb-cl-fill>

                            {{ $result->nama_lengkap }}
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            {{ isset($result['pendidikan_data']->refJenjangPendidikan) ? $result['pendidikan_data']->refJenjangPendidikan->jenjang_pendidikan : '-' }}
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            {{-- {{ dd($result['pendidikan_data']->bidang_pendidikan) }} --}}
                            {{ isset($result['pendidikan_data']->bidang_pendidikan) ? $result['pendidikan_data']->bidang_pendidikan : '-' }}
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            {{ isset($result['pendidikan_data']->tahun_lulus) ? $result['pendidikan_data']->tahun_lulus : '-' }}
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a
                                href="{{ route('manage.jenjang-pendidikan.history', ['idUser' => $result->id]) }}"
                                    class="px-3 py-1.5 border open-modal border-[#0070ff] text-[#0070ff] rounded-md text-xs font-medium hover:bg-[#0070ff] hover:text-white transition">
                                    View Data
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                                        ⋮
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if ($result->id_pendidikan_tertinggi != null && isset($result->pendidikan_data->id))
                                            <li>
                                                <a
                                                    href="{{ route('manage.jenjang-pendidikan.update', ['id_jp' => $result->pendidikan_data->id]) }}"
                                                    class="dropdown-item hover:bg-blue-500 hover:text-white" href="#">
                                                        Ubah Data
                                                </a>
                                            </li>

                                        @endif
                                        <li>
                                            <a href="{{ route('manage.jenjang-pendidikan.new', ['id_user' => $result->id]) }}"
                                                class="dropdown-item hover:bg-blue-500 hover:text-white" href="#">
                                                Input Pendidikan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @empty
                    <p>No Data</p>
                @endforelse
            </x-slot:table_column>
        </x-tb>





    </div>
@endsection
