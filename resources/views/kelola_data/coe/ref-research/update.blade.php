@php
    $active_sidebar = 'Tambah Data Baru';
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
                    Perbarui Data Research COE Baru
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content-base')

    <x-form route="{{ route('manage.coe.ref-reserach.update',['id'=>$data->id])}}" id="form-update-data">
        <div class="grid gap-8">
            <div class="flex flex-col gap-4">

                {{-- Input Nama --}}
                <x-itxt
                    lbl="Nama"
                    nm="nama"
                    plc="Masukkan nama..."
                    max="200"
                    val="{{ $data->nama }}">
                </x-itxt>

                {{-- Input Kode --}}
                <x-itxt
                    lbl="Kode"
                    nm="kode"
                    plc="Masukkan kode..."
                    max="50" val="{{ $data->kode }}">
                </x-itxt>

            </div>
        </div>
    </x-form>
@endsection
