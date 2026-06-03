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
            background-color: #2563eb;
            /* blue-600 tailwind */
        }

        .nav-active span {
            color: white;
        }
    </style>
@endsection

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.7px] self-stretch px-1 pt-[14.6px] pb-[13.9px]">
        <div class="flex w-full flex-col gap-1 grow">
            <div class="flex items-center gap-2 self-stretch">
                <span class="font-bold text-[26px] tracking-tight text-[#1d1d1f]">
                    Daftar Pegawai {{ $send[0] == 'Semua' ? '' : $send[0] }}
                </span>
            </div>
            <span class="font-medium text-[13px] text-[#86868b] tracking-normal">
                Kelola informasi dan pemetaan pegawai dalam sistem.
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.7px]">
            <x-export-csv-tb target_id="pegawaiTable"></x-export-csv-tb>

            <a href="{{ route('manage.pegawai.new') }}"
                class="bg-blue-600 px-[11.7px] py-[7.3px] rounded-xl border border-blue-600 shadow-sm hover:bg-blue-700 hover:shadow-md hover:-translate-y-0.5 active:scale-95 transition-all duration-300 flex items-center gap-1 route_pop_up">
                <i class="bi bi-plus text-sm text-white"></i>
                <span class="font-medium text-[10.2px] text-white">Tambah</span>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    {{-- Navigasi Halaman (Pagination Atas) --}}
    <div class="mt-4 px-2">
        {{ $users->links() }}
    </div>

    <div class="flex flex-col gap-2 max-w-max min-h-[30vh] h-fit max-h-[80vh] p-4 pb-6 mb-2">

        {{-- Tabs --}}
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <a href="{{ route('manage.pegawai.list', ['destination' => 'Active']) }}"
                class="route_pop_up {{ $send[0] == 'Active' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200' }}
                active:scale-95 transition-all duration-300 flex justify-center items-center gap-[6.2px] py-2 px-5 rounded-lg">
                <span class="font-semibold text-xs">Active</span>
            </a>
            <a href="{{ route('manage.pegawai.list', ['destination' => 'Semua']) }}"
                class="route_pop_up {{ $send[0] == 'Semua' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200' }}
                active:scale-95 transition-all duration-300 flex justify-center items-center gap-[6.2px] py-2 px-5 rounded-lg">
                <span class="font-semibold text-xs">Semua</span>
            </a>
        </div>

        <x-tb id="pegawaiTable" search_status=true>
            <x-slot:table_header>
                <x-tb-td nama="nama" sorting=true>Nama Lengkap</x-tb-td>
                <x-tb-td type="select" nama="nip" sorting=true>NIP</x-tb-td>
                <x-tb-td nama="nik" sorting=true>NIK</x-tb-td>
                <x-tb-td nama="hp" sorting=true>No. HP</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>Gender</x-tb-td>
                <x-tb-td type="select" nama="tipe" sorting=true>Tipe Pegawai</x-tb-td>
                @if (session('account')['is_admin'] == 1)
                    <x-tb-td type="select" nama="is_admin" sorting=true>Apakah Admin</x-tb-td>
                @endif
                <x-tb-td type="select" nama="bagian" sorting=true>Prodi/Bagian</x-tb-td>
                {{-- {{ @dd($) }} --}}
                @if ($send[0] == 'Spess')
                    <x-tb-td type="select" nama="serdos" sorting=true>Sertifikasi Dosen</x-tb-td>
                @endif
                @if ($send[0] == 'Semua' || $send[0] == 'Spess')
                    <x-tb-td type="select" nama="aktif" sorting=true>Is Active</x-tb-td>
                @endif
                <x-tb-td nama="action" sorting=false>Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @php $authId = session('account')['id']; @endphp
                @foreach ($users as $user)
                    @if ($user->id != $authId)
                        <tr class="x-tb-cl border-b border-slate-100 hover:bg-slate-50/80 transition-colors duration-200">
                            {{-- Nomor urut diatur oleh JS indexFormatter di x-tb --}}
                            <td class="x-tb-cl-fill fill-table-row px-4 py-4 numbering text-slate-600 font-medium"></td>

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 text-start py-4 whitespace-nowrap align-middle break-words text-wrap font-semibold text-slate-800">
                                {{ $user->nama_lengkap }}
                            </td>

                            <td
                                class="x-tb-cl-fill text-start fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->nip_aktif)
                                    <span class="text-slate-700 font-medium">{{ $user->nip_aktif }}</span>
                                @else
                                    <a href="{{ route('manage.pengawakan.new', ['users_id' => $user->id]) }}"
                                        class="text-slate-400 italic">
                                        Belum dipetakan <br><span
                                            class="text-xs text-blue-500 font-medium route_pop_up hover:text-blue-700 hover:underline transition-colors">klik
                                            untuk set</span>
                                    </a>
                                @endif
                            </td>

                            <td
                                class="x-tb-cl-fill text-start fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap text-slate-600">
                                {{ $user->nik }}
                            </td>

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                <div class="flex items-center gap-3">
                                    <a href="https://wa.me/62{{ $user->telepon }}" target="_blank"
                                        class="flex items-center active:scale-95 justify-center shrink-0 w-8 h-8 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-sm hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all duration-300"
                                        data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-content="Hubungi WhatsApp 📱">
                                        <i class="bi bi-whatsapp text-lg"></i>
                                    </a>
                                    <span class="hidden md:inline-block font-medium text-slate-700 tracking-wide">
                                        {{ $user->telepon }}
                                    </span>
                                </div>
                            </td>

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->jenis_kelamin === 'Perempuan')
                                    <span
                                        class="inline-flex items-center gap-2 text-pink-500 bg-pink-50 px-3 py-1 rounded-lg border border-pink-100 font-medium text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 0C8.134 0 5 3.134 5 7c0 2.68 1.675 4.967 4 6.152V16H8v2h3v4h2v-4h3v-2h-1v-2.848C17.325 11.967 19 9.68 19 7c0-3.866-3.134-7-7-7z" />
                                        </svg>
                                        <span class="hidden md:inline">P</span>
                                    </span>
                                @elseif($user->jenis_kelamin === 'Laki-laki')
                                    <span
                                        class="inline-flex items-center gap-2 text-blue-500 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100 font-medium text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 0c-3.866 0-7 3.134-7 7 0 2.68 1.675 4.967 4 6.152V16H8v2h3v4h2v-4h3v-2h-1v-2.848C17.325 11.967 19 9.68 19 7c0-3.866-3.134-7-7-7zm0 2c2.761 0 5 2.239 5 5s-2.239 5-5 5-5-2.239-5-5 2.239-5 5-5z" />
                                        </svg>
                                        <span class="hidden md:inline">L</span>
                                    </span>
                                @endif
                            </td>

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->tipe_pegawai === 'TPA')
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-purple-100 text-purple-800 border border-purple-200 font-semibold text-xs shadow-sm">
                                        {{ $user->tipe_pegawai }}
                                    </span>
                                @elseif($user->tipe_pegawai === 'Dosen')
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold text-xs shadow-sm">
                                        {{ $user->tipe_pegawai }}
                                    </span>
                                @else
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 font-semibold text-xs shadow-sm">
                                        {{ $user->tipe_pegawai }}
                                    </span>
                                @endif
                            </td>

                            @if (session('account')['is_admin'] == 1)
                                <td
                                    class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                    @if ($user->is_admin == 1)
                                        Iya
                                    @else
                                        Tidak
                                    @endif

                                </td>
                            @endif

                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                @if ($user->bagian_kode)
                                    <p class="cursor-pointer text-slate-700 font-medium hover:text-blue-600 transition-colors"
                                        title="{{ $user->bagian_tipe . ' - ' . $user->bagian_nama }}">
                                        {{ $user->bagian_kode }}
                                    </p>
                                @else
                                    <p class="text-slate-400 italic">Belum dipetakan</p>
                                    <a href="{{ route('manage.pengawakan.new', ['users_id' => $user->id]) }}"
                                        class="text-xs text-blue-500 font-medium route_pop_up hover:text-blue-700 hover:underline transition-colors">
                                        klik untuk set
                                    </a>
                                @endif
                            </td>
                            @if ($send[0] == 'Spess')
                                <td class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle">
                                    @if ($user->id_serdos != null)
                                        <a href="{{ route('manage.sertifikasi-dosen.serdos_file', ['id_serdos' => $user->id_serdos]) }}"
                                            class="inline-flex items-center w-fit gap-2.5 px-3 py-2 text-[11px] font-bold tracking-wider uppercase rounded-xl border transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg active:scale-95 group
                                                bg-white text-indigo-600 border-indigo-100
                                                hover:bg-indigo-600 hover:text-white hover:border-indigo-600">

                                            <div
                                                class="p-1 rounded-lg bg-indigo-50 transition-colors duration-300 group-hover:bg-indigo-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                                    class="transition-transform duration-300 group-hover:rotate-12 group-hover:text-white">
                                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                                    <path d="M10 9h4" />
                                                    <path d="M10 13h4" />
                                                </svg>
                                            </div>

                                            <span class="transition-colors duration-300 group-hover:text-purple-600">
                                                Lihat File
                                            </span>

                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                                class="opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0 group-hover:text-white">
                                                <path d="M5 12h14" />
                                                <path d="m12 5 7 7-7 7" />
                                            </svg>
                                        </a>
                                    @else
                                        <div class="mt-2 flex items-center gap-1.5 text-slate-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                            <span class="text-xs italic font-medium">Belum ada file</span>
                                        </div>
                                    @endif

                                </td>
                            @endif

                            @if ($send[0] == 'Semua' || $send[0] == 'Spess')
                                <td
                                    class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                    <span
                                        class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border {{ $user->is_active ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-slate-100 text-slate-800 border-slate-200' }}">
                                        {{ $user->is_active ? 'Active' : 'Nonactive' }}
                                    </span>
                                </td>
                            @endif



                            <td
                                class="x-tb-cl-fill fill-table-row px-4 py-4 whitespace-nowrap align-middle break-words text-wrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('manage.pegawai.view.personal-info', ['idUser' => $user->id]) }}"
                                        class="px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-sm transition-all duration-300 active:scale-95 route_pop_up">
                                        View Details
                                    </a>

                                    <div class="dropdown">
                                        <button
                                            class="btn btn-light btn-sm rounded-lg border border-slate-200 shadow-sm active:scale-95 hover:bg-slate-100 text-slate-600 px-2 py-1 transition-all"
                                            data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v px-1"></i>
                                        </button>
                                        <ul class="dropdown-menu shadow-md border-0 rounded-xl overflow-hidden mt-1">
                                            <li><a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors route_pop_up"
                                                    href="#">Tambah Pendidikan</a></li>
                                            <li><a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors route_pop_up"
                                                    href="#">Ubah Struktural</a></li>
                                            <li><a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors route_pop_up"
                                                    href="#">Ubah Fungsional</a></li>
                                            <li>
                                                <a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors route_pop_up"
                                                    href="{{ route('manage.pengawakan.new', ['users_id' => $user->id]) }}">
                                                    Tambah Pemetaan Baru
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors route_pop_up"
                                                    href="{{ route('manage.riwayat-nip.list', ['nama' => $user->nama_lengkap]) }}">
                                                    Riwayat NIP
                                                </a>
                                            </li>
                                            @if (session('account')['is_admin'] == 1)
                                                @if ($user->is_admin == 0)
                                                    <li>
                                                        <a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                                                            href="#"
                                                            onclick="event.preventDefault(); document.getElementById('form-set-admin-{{ $user->id }}').submit();">
                                                            Jadikan Admin
                                                        </a>
                                                        <form id="form-set-admin-{{ $user->id }}"
                                                            action="{{ route('manage.pegawai.set-admin', ['idUser' => $user->id]) }}"
                                                            method="POST" class="hidden">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                                                            href="#"
                                                            onclick="event.preventDefault(); document.getElementById('form-set-non-admin-{{ $user->id }}').submit();">
                                                            Cabut Hak Akses Admin
                                                        </a>
                                                        <form id="form-set-non-admin-{{ $user->id }}"
                                                            action="{{ route('manage.pegawai.set-non-admin', ['idUser' => $user->id]) }}"
                                                            method="POST" class="hidden">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </x-slot:table_column>
        </x-tb>
    </div>


    {{-- Navigasi Halaman (Pagination Bawah) --}}
    <div class="mt-4 px-2 pb-6">
        {{ $users->links() }}
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
