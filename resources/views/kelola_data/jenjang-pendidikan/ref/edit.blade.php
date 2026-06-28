@php
    $active_sidebar = 'Tambah Jenjang Pendidikan';
@endphp
@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.69px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.94px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">
                    Perbarui Referensi Jenjang Pendidikan
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.jenjang-pendidikan.ref.update') }}" id="form-jenjang">
        <div class="grid gap-8">
            <div class="flex flex-col gap-4">

                <x-itxt lbl="" val="{{ $data->id }}" nm="id" fill="hidden" plc="" max="10"> </x-itxt>
                <div class="flex flex-col gap-1">
                    <x-itxt lbl="Kode Jenjang Pendidikan" val="{{ $data->jenjang_pendidikan }}" nm="jenjang_pendidikan" plc="Contoh: S1" max="10"
                        :req="true" />
                </div>

                <div class="flex flex-col gap-1">
                    <x-itxt lbl="Tingkat" nm="tingkat" val="{{ $data->tingkat }}" plc="Contoh: Sarjana" max="100" :req="true" />
                </div>

                <div class="flex flex-col gap-2">
                    <x-itxt lbl="Urutan" type="number" val="{{ $data->urutan }}" nm="urutan" plc="Masukkan Angka Urutan" :req="true" />

                    {{-- Keterangan Urutan --}}
                    <div
                        class="text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-md p-3 flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <p>
                            Lihat pada
                            <strong house>
                                <a href="{{ route('manage.jenjang-pendidikan.ref.list') }}" target="_blank"
                                    rel="noopener noreferrer">
                                    tabel list referensi jenjang pendidikan
                                </a>
                            </strong>
                            agar paham urutan mana yang sudah digunakan dan menentukan prioritas tingkatan.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <x-itxt lbl="Kode Gelar" nm="kode_gelar" val="{{ $data->kode_gelar }}" plc="Contoh: S.T." max="20" :req="true" />

                </div>

            </div>
        </div>
    </x-form>
@endsection
