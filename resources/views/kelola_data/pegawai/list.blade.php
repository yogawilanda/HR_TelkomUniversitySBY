@php
    $active_sidebar = 'Daftar Pegawai';
@endphp
@extends('kelola_data.base')

@section('header-base')
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
    <div class="flex flex-col md:flex-row items-center gap-[11.7px] self-stretch px-1 pt-[14.6px] pb-[13.9px]">
        <div class="flex w-full flex-col gap-[2.9px] grow">
            <div class="flex items-center gap-[5.8px] self-stretch">
                <span class="font-medium font-semibold text-2xl leading-[20.5px] text-[#101828]">
                    Daftar Pegawai {{ $send[0] == 'Semua' ? '' : $send[0] }}
                </span>
            </div>
            <span class="font-normal text-[10.2px] text-[#1f2028]">
                Anda dapat melihat semua pegawai yang terdaftar di sistem disini
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.7px]">
            <x-export-csv-tb target_id="pegawaiTable"></x-export-csv-tb>
            <a href="{{ route('manage.pegawai.new') }}"
                class="bg-[#0070ff] px-[11.7px] active:scale-95 route_pop_up py-[7.3px] rounded-[5.8px] border border-[#0070ff] hover:bg-[#005fe0] transition flex items-center gap-1">
                {{-- class="bg-[#0070ff] px-[11.7px] py-[7.3px] rounded-[5.8px] border border-[#0070ff] hover:bg-[#005fe0] transition flex items-center gap-1"> --}}
                <i class="bi bi-plus text-sm text-white"></i>
                <span class="font-medium text-[10.2px] text-white">Tambah</span>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    {{-- Navigasi Halaman --}}
    <div class="mt-4 px-2">
        {{ $users->links() }}
    </div>
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <div class="flex items-center gap-[3.7px] border-b border-b-gray-300">
            <a href="{{ route('manage.pegawai.list', ['destination' => 'Active']) }}"
                class="h-[17.5px] route_pop_up {{ $send[0] == 'Active' ? 'bg-[#0070ff] text-white' : '' }} text-[#1c2762] hover:bg-[#1A7BFF] hover:text-gray-100 active:bg-[#0050AA] active:scale-95 active:text-white active:drop-shadow-sm flex justify-center items-center gap-[6.2px] p-[10px] rounded-t-[4px]">
                <span class="font-semibold text-xs ">Active</span>
            </a>
            <a href="{{ route('manage.pegawai.list', ['destination' => 'Semua']) }}"
                class="h-[17.5px] route_pop_up {{ $send[0] == 'Semua' ? 'bg-[#0070ff] text-white' : '' }} text-[#1c2762] hover:bg-[#1A7BFF] hover:text-gray-100 active:bg-[#0050AA] active:scale-95 active:text-white active:drop-shadow-sm flex justify-center items-center gap-[6.2px] p-[10px] rounded-t-[4px]">
                <span class="font-semibold text-xs ">Semua</span>
            </a>
        </div>

        <x-tb id="pegawaiTable">
            <x-slot:table_header>
                <x-tb-td nama="nama" sorting=true>Nama Lengkap</x-tb-td>
                <x-tb-td nama="nip" sorting=true>NIP</x-tb-td>
                <x-tb-td nama="nik" sorting=true>NIK</x-tb-td>
                <x-tb-td nama="hp" sorting=true>No. HP</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>Gender</x-tb-td>
                <x-tb-td type="select" nama="tipe" sorting=true>Tipe Pegawai</x-tb-td>
                <x-tb-td type="select" nama="bagian" sorting=true>Prodi/Bagian</x-tb-td>
                @if ($send[0] == 'Semua')
                    <x-tb-td type="select" nama="aktif" sorting=true>Is Active</x-tb-td>
                @endif
                <x-tb-td nama="action" sorting=false>Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @php $authId = session('account')['id']; @endphp
                @foreach ($users as $user)
                    @if ($user->id != $authId)
                        {{-- Tag TR murni dengan class yang sama --}}
                        <tr class="x-tb-cl border-b border-gray-100 hover:bg-gray-50 transition">
                            {{-- Nomor urut diatur oleh JS indexFormatter di x-tb --}}
                            <td class="x-tb-cl-fill fill-table-row px-4 py-3 numbering"></td>

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 text-start py-3 whitespace-nowrap align-middle break-words text-wrap">
                                {{ $user->nama_lengkap }}
                            </td>

                            <td
                                class="x-tb-cl-fill text-start fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                {{ $user->nip_aktif ?? '-' }}
                            </td>
                            <td
                                class="x-tb-cl-fill text-start fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                {{ $user->nik }}
                            </td>
                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                <div class="flex items-center gap-3">
                                    <a href="https://wa.me/62{{ $user->telepon }}" target="_blank"
                                        class="flex items-center active:scale-95 justify-center shrink-0 w-8 h-8 rounded-lg border border-gray-200 bg-white text-[#25D366] shadow-sm hover:bg-green-50 hover:border-green-200 transition-all duration-200"
                                        data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-content="Hubungi WhatsApp 📱">
                                        <i class="bi bi-whatsapp text-lg"></i>
                                    </a>

                                    <span class="hidden md:inline-block font-medium text-gray-700 tracking-wide">
                                        {{ $user->telepon }}
                                    </span>
                                </div>
                            </td>
                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->jenis_kelamin === 'Perempuan')
                                    <span class="inline-flex items-center gap-2 text-pink-500">
                                        <!-- icon female -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 0C8.134 0 5 3.134 5 7c0 2.68 1.675 4.967 4 6.152V16H8v2h3v4h2v-4h3v-2h-1v-2.848C17.325 11.967 19 9.68 19 7c0-3.866-3.134-7-7-7z" />
                                        </svg>
                                        <span class="hidden md:inline">{{ $user->jenis_kelamin }}</span>
                                    </span>
                                @elseif($user->jenis_kelamin === 'Laki-laki')
                                    <span class="inline-flex items-center gap-2 text-blue-500">
                                        <!-- icon male -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 0c-3.866 0-7 3.134-7 7 0 2.68 1.675 4.967 4 6.152V16H8v2h3v4h2v-4h3v-2h-1v-2.848C17.325 11.967 19 9.68 19 7c0-3.866-3.134-7-7-7zm0 2c2.761 0 5 2.239 5 5s-2.239 5-5 5-5-2.239-5-5 2.239-5 5-5z" />
                                        </svg>
                                        <span class="hidden md:inline">{{ $user->jenis_kelamin }}</span>
                                    </span>
                                @endif
                            </td>
                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->tipe_pegawai === 'TPA')
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-pink-500 text-white font-semibold text-sm shadow-md">
                                        {{ $user->tipe_pegawai }}
                                    </span>
                                @elseif($user->tipe_pegawai === 'Dosen')
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-blue-500 text-white font-semibold text-sm shadow-md">
                                        {{ $user->tipe_pegawai }}
                                    </span>
                                @else
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-gray-300 text-gray-800 font-semibold text-sm shadow-sm">
                                        {{ $user->tipe_pegawai }}
                                    </span>
                                @endif
                            </td>
                            
                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->bagian_kode)
                                    <p class="cursor-pointer hover:font-bold"
                                        title="{{ $user->bagian_tipe . ' - ' . $user->bagian_nama }}">
                                        {{ $user->bagian_kode }}
                                    </p>
                                @else
                                    <p class="text-gray-400 italic">Belum dipetakan</p>
                                    <a href="{{ route('manage.pengawakan.new', ['users_id' => $user->id]) }}"
                                        class="text-xs text-blue-500 route_pop_up hover:text-blue-700 hover:underline">
                                        klik untuk set
                                    </a>
                                @endif
                            </td>

                            @if ($send[0] == 'Semua')
                                <td
                                    class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Nonactive' }}
                                    </span>
                                </td>
                            @endif

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap">
                                <div class="flex items-center justify-center gap-3">


                                    <a href="{{ route('manage.pegawai.view.personal-info', ['idUser' => $user->id]) }}"
                                        class="px-3 route_pop_up active:scale-95 py-1.5 border border-[#0070ff] text-[#0070ff] rounded-md text-xs font-medium hover:bg-[#0070ff] hover:text-white transition">
                                        View Details
                                    </a>

                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm active:scale-95" data-bs-toggle="dropdown">⋮</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item route_pop_up" href="#">Tambah Pendidikan</a>
                                            </li>
                                            <li><a class="dropdown-item route_pop_up" href="#">Ubah Struktural</a>
                                            </li>
                                            <li><a class="dropdown-item route_pop_up" href="#">Ubah Fungsional</a>
                                            </li>
                                            <li><a class="dropdown-item route_pop_up"
                                                    href="{{ route('manage.pengawakan.new', ['users_id' => $user->id]) }}">Tambah
                                                    Pemetaan Baru</a></li>

                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </x-slot:table_column>
        </x-tb>

        {{-- Navigasi Halaman --}}
        <div class="mt-4 px-2">
            {{ $users->links() }}
        </div>
    </div>
    @include('kelola_data.pegawai.js.active-and-nonactive-pegawai')
    @include('kelola_data.pegawai.js.alert-success-from-controller')
@endsection

@section('script-base')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            [...popoverTriggerList].map(el => new bootstrap.Popover(el));
        });
    </script>
@endsection
