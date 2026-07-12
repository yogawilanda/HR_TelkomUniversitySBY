@php
    $active_sidebar = 'Tambah Pemetaan CoE';
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
                    Perbaharui Pemetaan Dosen CoE
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.coe.dosen.update',['id_coe' => $data->id]) }}" id="form-pemetaan-coe">
        <div class="grid gap-8">
            <div class="flex flex-col gap-4">

                {{-- Dosen --}}
                <x-islc lbl="Dosen" nm="dosen_id">
                    <option value="" disabled selected>-- Pilih Dosen --</option>
                    @forelse ($dosen as $item)
                        <option value="{{ $item->id }}"
                            {{ old('dosen_id', $data->dosen_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->pegawai->nama_lengkap }}</option>
                    @empty
                        <option value="" disabled selected>-- Belum Ada Dosen @if (session('account')['is_admin'] == 1)
                                , Silahkan Tambah dulu
                            @endif --</option>
                    @endforelse
                </x-islc>
                <x-islc lbl="Center of Excellence (CoE)" nm="coe_id">
                    <option value="" disabled selected>-- Pilih CoE --</option>
                    @forelse ($coe as $item)
                        <option value="{{ $item->id }}"
                            {{ old('coe_id', $data->coe_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_coe }} ({{ $item->research->kode }})</option>
                    @empty
                        <option value="" disabled selected>-- Belum Ada Coe @if (session('account')['is_admin'] == 1)
                                , Silahkan Tambah dulu
                            @endif --</option>
                    @endforelse
                </x-islc>

                {{-- TMT Mulai --}}
                <x-itxt lbl="TMT Mulai" type="date" nm="tmt_mulai"
                    val="{{ old('tmt_mulai') ?? date('Y-m-d', strtotime($data->tmt_mulai)) }}">
                </x-itxt>

                {{-- TMT Selesai --}}

                <x-itxt lbl="TMT Selesai (Opsional)" type="date"
                    val="{{ old('tmt_selesai') ?? ($data->tmt_selesai ? \Carbon\Carbon::parse($data->tmt_selesai)->format('Y-m-d') : '') }}"
                    nm="tmt_selesai" :req="false">
                </x-itxt>

            </div>
        </div>
    </x-form>
@endsection
