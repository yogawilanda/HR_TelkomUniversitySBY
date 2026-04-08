@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Daftar Laporan Pekerjaan</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Daftar laporan untuk approval</span>
        </div>
                <div class="flex items-center gap-2">
            @include('kelola_data.parts.target_kinerja_toolbar')
        </div>
    </div>
@endsection

@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>@endif
        <x-tb id="pelaporanTable">
            <x-slot:table_header>
                {{-- <x-tb-td nama="no" sorting=false>No</x-tb-td> --}}
                <x-tb-td nama="target_harian" sorting=true>Target Harian</x-tb-td>
                <x-tb-td nama="realisasi" sorting=false>Realisasi</x-tb-td>
                <x-tb-td nama="realisasi_jumlah" sorting=true>Realisasi Jumlah</x-tb-td>
                <x-tb-td nama="realisasi_waktu" sorting=true>Realisasi Waktu</x-tb-td>
                <x-tb-td nama="status" sorting=true>Status Approval</x-tb-td>
                <x-tb-td nama="action" sorting=false>Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach($items as $i => $it)
                    <x-tb-cl id="{{ $it->id }}">
                        {{-- <x-tb-cl-fill>{{ $i+1 }}</x-tb-cl-fill> --}}
                        <x-tb-cl-fill>{{ $it->targetHarian->pekerjaan ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ Str::limit($it->realisasi, 60) }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->effective_jumlah ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->effective_waktu_minutes ?? '-' }}</x-tb-cl-fill>
                            <x-tb-cl-fill>{{ ucfirst($it->status ?? 'pending') }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('manage.target-kinerja.harian.reports.approval', $it->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-blue-600" title="Open">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforeach
            </x-slot:table_column>
        </x-tb>
    </div>
@endsection
