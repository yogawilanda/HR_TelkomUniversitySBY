@php
    $active_sidebar = 'Tambah Pegawai Baru';
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
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">
                    Update Jenjang Pendidikan {{ isset($user_data) && $user_data->id == session('account')['id']? 'Anda' : 'Pegawai' }} {{ session('account')['nama_lengkap']??$user_data->nama_lengkap }}
                </span>
            </div>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.jenjang-pendidikan.update-data', ['id_jp' => $id_jp, 'secret' => request('wht')=='user'?'yes':'no']) }}">


        {{-- Data Pendidikan Pegawai --}}
        <div class="flex flex-col gap-8 w-full max-w-100 md:mx-auto rounded-md border p-3">
            {{-- <h2 class="text-lg font-semibold text-black text-center">Data Pendidikan Pegawai (Opsional)</h2> --}}

            <div class="grid md:grid-cols-2 gap-8">
                <div class="flex flex-col gap-4">
                    {{-- {{ dump(request()->id_user) }} --}}
                    <div class="{{ request()->id_user==null? '' : 'hidden' }}">
                        <x-islc lbl="Staff" nm="users_id">
                            <option  disabled selected>-- Pilih Data --</option>
                            {{-- {{ dd($data_user) }} --}}
                            @foreach ($users as $option)
                                <option value="{{ $option->id }}" {{ old('users_id', $data_user->users_id) == $option->id ? 'selected' : '' }}>{{ $option->nama_lengkap }}</option>
                            @endforeach
                        </x-islc>
                    </div>

                    <x-islc lbl="Jenjang Pendidikan" nm="jenjang_pendidikan_id">
                        <option  disabled selected>-- Pilih Data --</option>

                        @foreach ($jenjang_pendidikans as $option)
                            <option value="{{ $option->id }}"
                                {{ old('jenjang_pendidikan_id', $data_user->refJenjangPendidikan->id) == $option->id ? 'selected' : '' }} >{{ $option->jenjang_pendidikan }}</option>
                        @endforeach
                    </x-islc>

                    <x-itxt lbl="Bidang Pendidikan / Fakultas" plc="Informatika" nm="bidang_pendidikan"
                        max="150" val="{{ old('bidang_pendidikan', $data_user->bidang_pendidikan) }}"></x-itxt >

                    <x-itxt lbl="Jurusan / Program Studi" plc="Rekayasa Perangkat Lunak" nm="jurusan"
                        max="150" val="{{ old('jurusan', $data_user->jurusan) }}"></x-itxt>

                    <x-itxt lbl="Nama Kampus" plc="Telkom University" nm="nama_kampus" max="150" val="{{ old('nama_kampus', $data_user->nama_kampus) }}"></x-itxt>

                    <x-itxt lbl="Alamat Kampus" plc="Jl. Telekomunikasi No. 1, Bandung" nm="alamat_kampus"
                        max="300" val="{{ old('alamat_kampus', $data_user->alamat_kampus) }}"></x-itxt>
                </div>

                <div class="flex flex-col gap-4">
                    <x-itxt type="number" lbl="Tahun Lulus" plc="2024" nm="tahun_lulus" min="1900"
                        max="{{ now()->year }}" val="{{ old('tahun_lulus', $data_user->tahun_lulus) }}" ></x-itxt>

                    <x-itxt type="number" lbl="Nilai IPK" plc="3.75" nm="nilai" step="0.01" min="0"
                        max="4" :rules="['maksimal ipk 4.00']" val="{{ old('nilai', $data_user->nilai) }}" />


                    <div class="flex flex-col xl:flex-row justify-between w-full gap-3">
                        <x-itxt lbl="Gelar yang Didapat" fill="flex-grow" plc="Sarjana Komputer" nm="gelar"
                            max="50" val="{{ old('gelar', $data_user->gelar) }}"></x-itxt>
                        <x-itxt lbl="Singkatan Gelar" plc="S.Kom." fill="flex-grow" nm="singkatan_gelar"
                            max="20" val="{{ old('singkatan_gelar', $data_user->singkatan_gelar) }}"></x-itxt>
                    </div>

                    <label class="text-sm font-medium text-gray-700">Ijazah / Sertifikat Kelulusan (PDF/JPG)</label>
                    <input type="file" name="ijazah_file" accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full rounded-md border px-3 py-2 text-sm" />
                </div>
            </div>
        </div>
    </x-form>
@endsection
