@php
    $active_sidebar = 'Daftar Pemetaan';
@endphp
@extends('kelola_data.base')
@section('header-base')
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- OrgChart.js -->
    <script src="https://balkan.app/js/OrgChart.js"></script>
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
                    class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Pemetaan</span>
            </div><span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat melihat semua formasi yang terdaftar di sistem disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">


            <x-print-tb target_id="PemetaanTable"></x-print-tb>
            <x-export-csv-tb target_id="PemetaanTable"></x-export-csv-tb>

            <a href="{{ route('manage.pengawakan.new') }}" class="flex rounded-[5.874740123748779px]">
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
        <x-tb id="PemetaanTable">
            <x-slot:table_header>
                <x-tb-td nama="nama" sorting=true>Nama Pegawai</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>Formasi</x-tb-td>
                <x-tb-td nama="tmt_mulai" sorting=true>TMT Mulai</x-tb-td>
                {{-- <x-tb-td nama="tmt_selesai" sorting=true>TMT Selesai</x-tb-td> --}}
                <x-tb-td nama="sk" sorting=true>Nomor SK</x-tb-td>
                {{-- <x-tb-td type="select" nama="aktif" sorting=true>Nonaktifkan</x-tb-td> --}}
                <x-tb-td nama="email_pribadi" sorting=true>Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @foreach ($pemetaans as $pemetaan)
                    <x-tb-cl :cls="$pemetaan->tmt_selesai !== null ? 'opacity-45' : ''">

                        <x-tb-cl @if ($pemetaan->tmt_selesai !== null) cls="opacity-45" @endif>
                            <x-tb-cl-fill>{{ htmlspecialchars($pemetaan->users->nama_lengkap) }}</x-tb-cl-fill>
                            <x-tb-cl-fill>{{ htmlspecialchars($pemetaan->formasi->nama_formasi) }}</x-tb-cl-fill>
                            <x-tb-cl-fill>{{ date('d/m/Y', strtotime($pemetaan->tmt_mulai)) }}</x-tb-cl-fill>
                            {{-- <x-tb-cl-fill :cls="$pemetaan->tmt_selesai === null ? 'text-white' : ''">{{ $pemetaan->tmt_selesai }}</x-tb-cl-fill> --}}
                            <x-tb-cl-fill><a href="" class="text-wrap">

                                    <a href="{{ route('manage.sk.view', ['id_sk_or_sk_number' => str_replace("/", "|", $pemetaan->sk_ypt->no_sk)]) }}"
                                        class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full flex items-center gap-2
                                            border border-transparent 
                                            transition-all duration-200 ease-in-out
                                            hover:bg-white hover:text-blue-700 hover:border-blue-500 hover:scale-105
                                            active:bg-blue-100 active:text-blue-700 active:scale-100">
                                        <i class="fa-solid fa-file"></i>
                                        {{ htmlspecialchars($pemetaan->sk_ypt->no_sk) }}
                                    </a>
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                <div class="flex items-center justify-center gap-3">
                                    <div class="dropdown shadow-xl">
                                        <button class="" data-bs-toggle="dropdown">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('manage.pengawakan.update', ['idPemetaan' => $pemetaan->id]) }}"
                                                    class="dropdown-item hover:bg-blue-500 hover:text-white" href="#">
                                                    Ubah Data
                                                </a>
                                            </li>
                                            <li>
                                                <button onclick="End_validation('{{ $pemetaan->id }}')"
                                                    class="end-jabatan dropdown-item hover:bg-blue-500 hover:text-white">
                                                    Selesaikan Masa Jabatan
                                                </button>
                                            </li>
                                            <li>
                                                <a href="{{ route('profile.history.pemetaan', ['id_user' => $pemetaan->users_id]) }}"
                                                    class="dropdown-item hover:bg-blue-500 hover:text-white" href="#">
                                                    History Pemetaan Karyawan
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

        <form action="{{ route('manage.pengawakan.selesaikan-jabatan') }}" method="POST" class="end-pemetaan hidden">
            @csrf
            <input type="text" name="id" id="" class="id_pengawakan" valuee="">
        </form>





    </div>






    @include('kelola_data.pegawai.js.alert-success-from-controller')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            [...popoverTriggerList].map(el => new bootstrap.Popover(el));
        });
    </script>

    <script>
        function End_validation(idPemetaan) {
            console.log('masuk :>> ');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.querySelector('.end-pemetaan');
                    form.querySelector('.id_pengawakan').value = idPemetaan;
                    form.submit();
                }
            });
            //     Swal.fire({
            //     title: "Deleted!",
            //     text: "Your file has been deleted.",
            //     icon: "success"
            // });
        }
    </script>
@endsection
