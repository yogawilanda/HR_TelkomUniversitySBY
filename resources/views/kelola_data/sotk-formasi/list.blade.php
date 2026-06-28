@php
    $active_sidebar = 'Formasi Struktural';
@endphp
@extends('kelola_data.base')
@section('header-base')
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
                    class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Formasi</span>
            </div><span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat melihat semua formasi yang terdaftar di sistem disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">


            <x-print-tb target_id="formasiTable"></x-print-tb>
            <x-export-csv-tb target_id="formasiTable"></x-export-csv-tb>

            <a href="{{ route('manage.formasi.new') }}" class="flex route_pop_up rounded-[5.874740123748779px]">
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
    <x-modal-view :footer="false" :head="false" id="formasi-update" title="Formasi Details">
        <div class="flex flex-col gap-3 px-8 py-8">
            <!-- Header -->
            <div class="flex items-center gap-5">
                <span class="font-semibold text-xl text-[#101828]">Data Formasi</span>
                <button onclick="window.location=''" id="ubah-data-button"
                    class="flex items-center justify-center gap-1 bg-[#0070ff] text-white font-medium text-xs px-3 py-1 rounded border border-[#0070ff] hover:bg-[#005bd4] transition-all">
                    Ubah Data
                </button>
            </div>

            <!-- Data Grid -->
            <div class="flex gap-12 w-full">
                <div class="flex flex-col gap-2 w-1/2">
                    <span class="font-light text-sm text-black">Level</span>
                    <span class="font-light text-sm text-black">Nama Formasi</span>
                    <span class="font-light text-sm text-black">Bagian</span>
                    <span class="font-light text-sm text-black">Atasan</span>
                    <span class="font-light text-sm text-black">Kuota</span>
                </div>
                <div class="flex flex-col gap-2 w-1/2">
                    <span class="font-normal text-sm text-black" id="level">Directur</span>
                    <span class="font-normal text-sm text-black" id="nama_formasi">Directur</span>
                    <span class="font-normal text-sm text-black" id="bagian">Directur</span>
                    <span class="font-normal text-sm text-black" id="atasan">Directur</span>
                    <span class="font-normal text-sm text-black" id="kuota">1</span>
                </div>
            </div>
        </div>


    </x-modal-view>
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">


        <x-tb id="formasiTable">
            <x-slot:table_header>
                <x-tb-td type="select" nama="level" sorting=true>Level</x-tb-td>
                <x-tb-td nama="nama_formasi" sorting=true>Nama Formasi</x-tb-td>
                <x-tb-td type="select" nama="tipe_bagian" sorting=true>Tipe Bagian</x-tb-td>
                <x-tb-td type="select" nama="bagian" sorting=true>Bagian</x-tb-td>
                <x-tb-td type="select" nama="atasan" sorting=true>Atasan</x-tb-td>
                <x-tb-td nama="kuota" sorting=true>Kuota</x-tb-td>
                <x-tb-td nama="email_pribadi">Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @forelse ($formations as $formation)
                    {{-- {{ dd($formation) }} --}}
                    <x-tb-cl id="{{ $formation->id }}">
                        <x-tb-cl-fill clsText="text-center">
                            <p id="level">{{ $formation->level_data->singkatan_level }}</p>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <p class="text-wrap" id="nama_formasi">{{ $formation->nama_formasi }}</p>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill clsText="text-center">
                            <p id="tipe_bagian"> {{ $formation->bagian->type_work_position }}</p>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill clsText="text-center">

                            <p id="kode" >{{ $formation->bagian->kode }}</p>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <p class="text-wrap" id="atasan">
                                {{ $formation->atasan_formation ? $formation->atasan_formation->nama_formasi : '-' }}</p>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill clsText="text-center">
                            <p id="kuota" class="text-center">{{ $formation->kuota }}</p>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('manage.formasi.update', ['idFormasi' => $formation->id]) }}"
                                    class="px-3 py-1.5 cursor-pointer border open-modal border-[#0070ff] text-[#0070ff] rounded-md text-xs font-medium hover:bg-[#0070ff] hover:text-white transition">
                                    Edit Data
                                </a>
                                <button data-bs-target="#formasi-update" data-bs-toggle="modal" onclick="open_modal(this)"
                                    class="px-3 py-1.5 border open-modal border-[#0070ff] text-[#0070ff] rounded-md text-xs font-medium hover:bg-[#0070ff] hover:text-white transition">
                                    View Data
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                                        ⋮
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('manage.formasi.update', ['idFormasi' => $formation->id]) }}"
                                                class="dropdown-item route_pop_up hover:bg-blue-500 hover:text-white"
                                                href="#">
                                                Ubah Data
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item route_pop_up hover:bg-blue-500 hover:text-white"
                                                href="{{ route('manage.pengawakan.list',['is_active'=>'Aktif', 'bagian' => $formation->bagian->position_name, 'sort'=>'formasi','order'=>'desc']) }}">
                                                Karyawan Aktif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item route_pop_up hover:bg-blue-500 hover:text-white"
                                                href="{{ route('manage.pengawakan.list',['formasi' => $formation->nama_formasi, 'sort'=>'tmt_selesai','order'=>'desc']) }}">
                                                History Karyawan
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

@push('script-under-base')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.show-modal');

        function open_modal(elemen) {

            console.log('tes :>> ', elemen);
            const modal = document.querySelector('.modal#formasi-update');
            if (!modal) return;


            const form = elemen.closest('.x-tb-cl');
            // console.log('form :>> ', form);
            // Ambil data dari baris yang diklik
            const namaFormasi = form.querySelector('#nama_formasi')
                ?.textContent || '';
            const Level = form.querySelector('#level')
                ?.textContent || '';
            const bagian = form.querySelector('#bagian')
                ?.textContent || '';
            const tipeBagian = form.querySelector('#tipe_bagian')
                ?.textContent || '';
            const atasan = form.querySelector('#atasan')
                ?.textContent || '';
            const kuota = form.querySelector('#kuota')
                ?.textContent || '';
            const icode = form.getAttribute('id') || '';
            // console.log('isi :>> ', namaFormasi, Level, bagian, atasan, kuota, icode);
            // console.log('icode :>> ', icode);
            // console.log('namaLevel,singkatan,atasan :>> ', namaLevel, singkatan, atasan, icode);
            // Masukkan data ke dalam modal
            // modal.querySelector('#ubah-data-button').setAttribute('onclick',
            //     `window.location.href='/manage/formasi/update/${icode}'`);

            modal.querySelector('#level').textContent = Level;
            modal.querySelector('#nama_formasi').textContent = namaFormasi;
            modal.querySelector('#bagian').textContent = tipeBagian + " - " + bagian;
            modal.querySelector('#kuota').textContent = kuota;
            modal.querySelector('#atasan').textContent = atasan;
        }
        // });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            [...popoverTriggerList].map(el => new bootstrap.Popover(el));
        });
    </script>
@endpush
