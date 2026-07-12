@php
    $active_sidebar = 'Tambah Studi Lanjut';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col justify-center gap-[2.9373700618743896px] grow">
            <div class="flex items-center justify-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Tambah Studi Lanjut</span>
            </div>
            <span class="font-normal justify-center text-center text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">
                Tambah data studi lanjut pegawai baru
            </span>
        </div>
    </div>
@endsection

@section('content-base')
<div class="container mx-auto px-4 pb-6">
    {{-- <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Data Studi Lanjut</h1>
    </div> --}}

    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <x-form route="{{ route('manage.studi-lanjut.store') }}" cancelRoute="{{ route('manage.studi-lanjut.list') }}" id="studi-lanjut-input">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-islc lbl="Pegawai" nm="users_id">
                        <option value="" disabled selected>-- Pilih Pegawai --</option>
                        @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('users_id', request()->input('users_id'))==$p->id?'selected':'' }}>{{ $p->nama_lengkap }}</option>
                        @endforeach
                    </x-islc>
                </div>

                <div>
                    <x-islc lbl="Jenjang" nm="jenjang">
                        <option value="" disabled selected>-- Pilih Jenjang --</option>
                        <option value="S2" {{ old('jenjang', request()->input('jenjang'))== 'S2'?'selected':'' }}>S2</option>
                        <option value="S3" {{ old('jenjang', request()->input('jenjang'))== 'S3'?'selected':'' }}>S3</option>
                    </x-islc>
                </div>

                <div>
                    <x-itxt lbl="Program Studi" nm="program_studi" plc="Masukkan program studi" />
                </div>

                <div>
                    <x-itxt lbl="Universitas" nm="universitas" plc="Masukkan nama universitas" />
                </div>

                <div>
                    <x-itxt lbl="Negara" nm="negara" plc="Masukkan negara" />
                </div>

                <div>
                    <x-islc lbl="Status" nm="status">
                        <option value="" disabled selected>-- Pilih Status --</option>
                        <option value="Dalam Perencanaan" {{ old('status', request()->input('status'))== 'Dalam Perencanaan'?'selected':'' }}>Dalam Perencanaan</option>
                        <option value="Sedang Berjalan" {{ old('status', request()->input('status'))== 'Sedang Berjalan'?'selected':'' }}>Sedang Berjalan</option>
                        <option value="Selesai" {{ old('status', request()->input('status'))== 'Selesai'?'selected':'' }}>Selesai</option>
                        <option value="Cuti" {{ old('status', request()->input('status'))== 'Cuti'?'selected':'' }}>Cuti</option>
                    </x-islc>
                </div>

                <div>
                    <x-itxt lbl="Tanggal Mulai" nm="tanggal_mulai" type="date" />
                </div>

                <div>
                    <x-itxt lbl="Tanggal Selesai" nm="tanggal_selesai" type="date" :req="false" />
                </div>

                <div class="md:col-span-2">
                    <x-itxt lbl="Sumber Dana" nm="sumber_dana" plc="Masukkan sumber dana" :req="false" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea
                        name="keterangan"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan keterangan tambahan"
                        value="{{ old('keterangan') }}"
                    ></textarea>
                </div>
            </div>
        </x-form>
    </div>
@endsection
