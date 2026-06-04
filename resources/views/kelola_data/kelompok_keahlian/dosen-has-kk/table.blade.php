@php
    $active_sidebar = 'Daftar Pegawai';
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
    <div class="flex flex-col md:flex-row items-center gap-[11.74px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Daftar Dosen & Keahlian</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">
                Kelola informasi keahlian, fakultas, dan status aktif dosen di sini.
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.74px]">
            <x-print-tb target_id="dosenTable"></x-print-tb>
            <x-export-csv-tb target_id="dosenTable"></x-export-csv-tb>

            <a href="{{ route('manage.kelompok-keahlian.dosen-with-kk.new') }}" class="flex rounded-[5.87px]">
                <div
                    class="flex route_pop_up justify-center items-center gap-[5.87px] bg-[#0070ff] px-[11.74px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah Dosen</span>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    {{-- Bagian Filter Status --}}
    <div class="flex items-center gap-2 border-slate-200 pb-2">
        <a href="{{ route(Request::route()->getName(), ['status' => 'Active']) }}"
            class="route_pop_up {{ request('btn') == 'ba' ? 'bg-blue-600 text-white shadow-sm' : '' }} active:scale-95 transition-all border-blue-600 border hover:bg-blue-950 hover:text-white duration-300 flex justify-center items-center gap-[6.2px] py-2 px-5 rounded-lg">
            <span class="font-semibold text-xs">Data Aktif Saat Ini</span>
        </a>
        <a href="{{ route(Request::route()->getName(), ['sort' => 'kk']) }}"
            class="route_pop_up {{ request('btn') == 'bb' ? 'bg-blue-600 text-white shadow-sm' : '' }} active:scale-95 transition-all hover:bg-blue-950 hover:text-white duration-300 flex justify-center items-center gap-[6.2px] py-2 px-5 rounded-lg">
            <span class="font-semibold text-xs">Berdasarkan Kelompok Keahlian</span>
        </a>
    </div>

    <div class="flex flex-grow-0 flex-col gap-2 max-w-100 bg-white p-4 rounded-xl shadow-sm border border-slate-200 mt-2">
        <x-tb id="dosenTable" search_status=true>
            <x-slot:table_header>
                <x-tb-td nama="nama" sorting=true>Nama Dosen</x-tb-td>
                <x-tb-td type="select" nama="fakultas" sorting=true>Fakultas</x-tb-td>
                <x-tb-td type="select" nama="kk" sorting=true>Kelompok Keahlian</x-tb-td>
                <x-tb-td type="select" nama="sub_kk" sorting=true>Sub Kelompok Keahlian</x-tb-td>
                <x-tb-td type="select" nama="status" sorting=true>Status</x-tb-td>
                <x-tb-td type="select" nama="date" sorting=true>Dipetakan pada</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @foreach ($data as $item)
                    <x-tb-cl >
                        <x-tb-cl-fill>
                            <span class="font-semibold text-slate-800">{{ $item->dosen->pegawai->nama_lengkap }}</span>
                        </x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <span class="px-2 py-1 bg-slate-100 rounded text-xs font-bold">
                                {{ $item->subKK->KK->fakultas->position_name }}
                            </span>
                        </x-tb-cl-fill>

                        <x-tb-cl-fill>{{ $item->subKK->KK->nama }}</x-tb-cl-fill>

                        <x-tb-cl-fill><span class="italic text-slate-500">{{ $item->subKK->nama }}</span></x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $item->is_active ? 'Active' : 'Nonactive' }}
                            </span>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->created_at }}</x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-lg border border-slate-200 px-2"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-list"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border-0 rounded-xl overflow-hidden">
                                        <li>
                                            <a class="dropdown-item py-2 hover:bg-blue-500 hover:text-white" href="#">
                                                Edit Data
                                            </a>
                                        </li>

                                        @if($item->is_active==1)
                                        <li>
                                            {{-- {{ dd($item->id) }} --}}
                                            {{-- Perubahan di sini: href diubah ke javascript:void(0) --}}
                                            <a href="javascript:void(0)"
                                               onclick="ConfirmAction('{{ route('manage.kelompok-keahlian.dosen-with-kk.lepas-dosen',['DosenHasKK_id' => $item->id]) }}')"
                                               class="dropdown-item py-2 hover:bg-rose-500 hover:text-white">
                                                Lepas dari Sub Kelompok
                                            </a>
                                        </li>
                                        @else
                                        <li>
                                            <a href="javascript:void(0)"
                                            onclick="ConfirmAction('{{ route('manage.kelompok-keahlian.dosen-with-kk.pasang-kembali-dosen',['DosenHasKK_id' => $item->id]) }}')"
                                            class="dropdown-item py-2 hover:bg-green-500 hover:text-white transition-colors duration-150">
                                                Aktifkan Kembali Pemetaan
                                            </a>
                                        </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('manage.kelompok-keahlian.dosen-with-kk.riwayat',['id_user' => $item->dosen->pegawai->id]) }}"
                                               class="dropdown-item py-2 hover:bg-blue-500 hover:text-white">
                                                History Pemetaan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforeach
            </x-slot:table_column>
        </x-tb>
    </div>
@endsection

@push('script-under-base')
    @include('components.js.route-pop-up-button')
    @include('kelola_data.pegawai.js.alert-success-from-controller')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            [...popoverTriggerList].map(el => new bootstrap.Popover(el));
        });

        /**
         * Fungsi Konfirmasi
         * @param {string} url - URL tujuan redirect jika dikonfirmasi
         */
        function ConfirmAction(url) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Dosen akan dilepas dari sub kelompok keahlian ini.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Lepaskan!",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading sebentar sebelum pindah halaman (opsional tapi bagus untuk UX)
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect ke route Laravel
                    window.location.href = url;
                }
            });
        }
    </script>
@endpush
