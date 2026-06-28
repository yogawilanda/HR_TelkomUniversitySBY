@php
    $active_sidebar = 'Formasi Struktural';
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
                    Ubah Formasi
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.formasi.update-data', ['idFormasi' => $formation_target->id]) }}" id="level-input">
        <div class="grid gap-8 ">
            <!-- Kolom Kiri -->
            <div class="flex flex-col gap-4">
                <x-islc lbl="Level Formasi" nm='level_id' :req=true>
                    <option value="" disabled selected>-- Pilih Data --</option>
                    @forelse ($levels as $level)
                        <option value="{{ $level->id }}" {{ $level->id==old('level_id', $formation_target->level_id)?'Selected':'' }}>{{ $level->nama_level }}</option>
                    @empty
                        <option value="-" disabled>-- No Data --</option>
                    @endforelse
                </x-islc>

                <x-itxt :val="$formation_target->nama_formasi" lbl="Nama Formasi" plc="Direktur" nm='nama_formasi' max="100"></x-itxt>
                <x-itxt type="number" :val="$formation_target->kuota" lbl="Kuota" plc="DIR" nm='kuota' max="12"></x-itxt>

                <x-islc lbl="Atasan Formasi" nm='atasan_formasi_id' :req=false>
                    <option value="" disabled selected>-- Pilih Data --</option>
                    @forelse ($formations as $formation)
                        <option value="{{ $formation->id }}"
                            {{ $formation->id==old('atasan_formasi_id', $formation_target->atasan_formasi_id)?'Selected':'' }}
                            >{{ $formation->nama_formasi }}</option>
                    @empty
                        <option value="-" disabled>-- No Data --</option>
                    @endforelse
                </x-islc>
                {{-- Tipe Bagian (PENTING: beri name untuk diakses JS) --}}


                {{-- Bungkus tiap field yang tergantung tipe dengan div agar bisa di-hide --}}
                {{-- <div id="wrap-bagian"> --}}
                    <x-islc lbl="Bagian / Unit Kerja" nm='work_position_id' :req=false>
                        <option value="" disabled selected>-- Pilih Data --</option>
                        @forelse ($work_position as $bagian)
                        {{-- {{ dd($bagian) }} --}}
                            {{-- {{ dd('for',$bagian->id,'target',$formation_target->bagian_data->id),$formation_target }} --}}
                            <option value="{{ $bagian->id }}"
                                {{ $bagian->id==old('work_position_id', $formation_target->work_position_id)?'Selected':'' }}
                                >{{ $bagian->type_work_position.' - '.$bagian->position_name }}</option>
                        @empty
                            <option value="-" disabled>-- No Data --</option>
                        @endforelse
                    </x-islc>
                {{-- </div> --}}
            </div>
        </div>
    </x-form>
@endsection
