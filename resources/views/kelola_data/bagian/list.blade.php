@php
    $active_sidebar = 'Daftar Bagian Kerja';
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
        .btn-kebab {
            background: none;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .btn-kebab:hover {
            background-color: #f3f4f6;
        }
        .dropdown-toggle::after {
            display: none !important;
        }
    </style>
@endsection

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Daftar Bagian atau Divisi</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">
                Anda dapat melihat semua bagian yang terdaftar di sistem disini
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <x-print-tb target_id="PemetaanTable"></x-print-tb>
            <x-export-csv-tb target_id="PemetaanTable"></x-export-csv-tb>

            <a href="{{ route('manage.bagian.new') }}" class="flex rounded-[5.87px]">
                <div class="flex justify-center items-center gap-[5.87px] bg-[#0070ff] px-[11.75px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition">
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
                <x-tb-td nama="nama" sorting=true>Nama Bagian</x-tb-td>
                <x-tb-td nama="tipe_bagian" type="select" sorting=true>Tipe Bagian</x-tb-td>
                <x-tb-td nama="type_pekerja" type="select" sorting=true>Tipe Pekerja</x-tb-td>
                {{-- Header Singkatan Bagian juga diketengahkan --}}
                <x-tb-td nama="singkatan" sorting=true type="select" class="text-center">Singkatan Bagian</x-tb-td>
                <x-tb-td nama="action" sorting=false type="hide" addClass="hide-select-header">Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @foreach ($work_positions as $wp)
                    <x-tb-cl cls="">
                        <x-tb-cl-fill>{{ $wp->position_name }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $wp->type_work_position }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $wp->type_pekerja }}</x-tb-cl-fill>

                        {{-- Kolom Singkatan diketengahkan --}}
                        <x-tb-cl-fill cls="text-center">
                            <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded text-xs font-semibold border border-blue-100">
                                {{ $wp->kode }}
                            </span>
                        </x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center">
                                <div class="dropdown">
                                    <button class="btn-kebab dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical text-gray-600"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                        <li>
                                            <a href="{{ route('manage.bagian.edit', ['id_wp' => $wp->id]) }}"
                                                class="dropdown-item py-2 flex items-center gap-2">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                                <span>Ubah Data</span>
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
            <input type="hidden" name="id" class="id_pengawakan">
        </form>
    </div>

    @include('kelola_data.pegawai.js.alert-success-from-controller')

    <script>
        function End_validation(idPemetaan) {
            Swal.fire({
                title: "Konfirmasi Selesai",
                text: "Apakah Anda yakin ingin menyelesaikan masa jabatan untuk bagian ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#0070ff",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Selesaikan",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.querySelector('.end-pemetaan');
                    form.querySelector('.id_pengawakan').value = idPemetaan;
                    form.submit();
                }
            });
        }
    </script>
@endsection
