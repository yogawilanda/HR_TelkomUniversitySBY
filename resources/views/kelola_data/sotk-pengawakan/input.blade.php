@php
    $active_sidebar = 'Tambah Pemetaan';
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
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">
                    Tambah Pemetaan Baru
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.pengawakan.create') }}" id="pemetaan-input">
        <div class="grid gap-8 ">
            <!-- Kolom Kiri -->
            <div class="flex flex-col gap-4">

                <x-islc lbl="Pegawai" nm="users_id" is_disabled="{{ request()->filled('users_id') }}">
                    <option value="" disabled selected>-- Pilih Data --</option>

                    @forelse ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('users_id') == $user->id || request('users_id') == $user->id)>
                            {{ $user->nama_lengkap }}
                        </option>
                    @empty
                        <option value="-" disabled>-- No Data --</option>
                    @endforelse

                </x-islc>

                <x-islc lbl="Formasi" nm='formasi_id'>
                    <option value="" disabled selected>-- Pilih Data --</option>
                    @forelse ($formations as $formation)
                        <option value="{{ $formation->id }}" {{ old('formasi_id') == $formation->id ? 'selected' : '' }}>
                            {{ $formation->nama_formasi }}</option>
                    @empty
                        <option value="-" disabled>-- No Data --</option>
                    @endforelse
                </x-islc>

                <div class="space-y-2">
                    <x-islc lbl="Apakah Ini Divisi Utama Pegawai?" nm='is_main_position'>
                        <option value="" disabled selected>-- Pilih Data --</option>
                        <option value="0" {{ old('is_main_position') == "0" ? 'selected' : '' }}>Bukan</option>
                        <option value="1" {{ old('is_main_position') == "1" ? 'selected' : '' }}>Iya</option>
                    </x-islc>

                    <div class="text-sm text-gray-600 bg-gray-50 border rounded-md p-3">
                        <p>
                            Divisi utama pegawai hanya dapat ditentukan jika sesuai dengan pemetaan berdasarkan tipe
                            pegawai.
                        </p>

                        <p class="mt-2">
                            Contoh:
                        </p>

                        <ul class="list-disc pl-5 space-y-1">
                            <li>
                                Jika tipe pegawai adalah <strong>TPA</strong>, maka divisi utama tidak dapat ditetapkan pada
                                formasi di luar lingkup TPA.
                            </li>
                            <li>
                                Jika tipe pegawai adalah <strong>Dosen</strong>, maka divisi utama harus berada dalam
                                lingkup
                                kedosenan.
                            </li>
                        </ul>

                        <p class="mt-2">
                            Namun, pegawai <strong>tetap dapat dipetakan ke divisi di luar lingkup tipe pegawai</strong>,
                            hanya saja divisi tersebut <strong>tidak dapat dijadikan sebagai divisi utama</strong>.
                        </p>
                    </div>
                </div>

                <x-itxt lbl="TMT Mulai" type="date" plc="dd/mm/yyyy" nm='tmt_mulai'></x-itxt>

                <div class="w-full border border-gray-300 border-1 p-3 gap-3 flex flex-col">
                    <div class="flex flex-row border-b-2 gap-0 justify-between input-sk">
                        <button type="button" class="flex flex-grow justify-center items-center py-2 rounded-t-lg active"
                            id="btn-sk-baru">
                            Input SK Baru
                        </button>
                        <button type="button" class="flex flex-grow justify-center items-center py-2 rounded-t-lg"
                            id="btn-sk-existing">
                            Pilih SK yang Sudah ada
                        </button>
                    </div>

                    {{-- SECTION: INPUT SK BARU --}}
                    <div id="section-sk-baru">
                        <x-itxt lbl="SK YPT" type="file" plc="Pilih Dokumen SK" nm='file_sk' :req=false></x-itxt>
                        <x-itxt lbl="Nomor SK" plc="Nomor SK" nm='no_sk' max="50" :req=false></x-itxt>
                    </div>

                    {{-- SECTION: PILIH SK YANG SUDAH ADA --}}
                    <div class="flex flex-row gap-3 justify-center items-end hidden" id="section-sk-existing">
                        <x-islc lbl="Pilih SK YPT" nm='sk_ypt_id' :req=false>
                            <option value="" disabled selected>-- Pilih SK --</option>
                            @forelse ($sk_ypts as $sk_ypt)
                                <option value="{{ $sk_ypt->id }}" {{ old('sk_ypt_id') == $sk_ypt->id ? 'selected' : '' }}>
                                    {{ $sk_ypt->no_sk }}
                                </option>
                            @empty
                                <option value="-" disabled>-- No Data --</option>
                            @endforelse
                        </x-islc>
                        <a
                            class="px-4 py-2 h-fit bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                            Lihat File
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </x-form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSkBaru = document.getElementById('btn-sk-baru');
            const btnSkExisting = document.getElementById('btn-sk-existing');
            const sectionSkBaru = document.getElementById('section-sk-baru');
            const sectionSkExist = document.getElementById('section-sk-existing');

            function showSkBaru() {
                btnSkBaru.classList.add('active');
                btnSkExisting.classList.remove('active');

                sectionSkBaru.classList.remove('hidden');
                sectionSkExist.classList.add('hidden');
            }

            function showSkExisting() {
                btnSkExisting.classList.add('active');
                btnSkBaru.classList.remove('active');

                sectionSkExist.classList.remove('hidden');
                sectionSkBaru.classList.add('hidden');
            }

            btnSkBaru.addEventListener('click', showSkBaru);
            btnSkExisting.addEventListener('click', showSkExisting);
        });
    </script>
@endsection
