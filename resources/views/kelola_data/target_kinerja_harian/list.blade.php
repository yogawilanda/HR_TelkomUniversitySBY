@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Target Kinerja Harian</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Daftar set target harian</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <div class="hidden sm:flex items-center gap-2">
                @include('kelola_data.parts.target_kinerja_toolbar')
            </div>
            <a href="{{ route('manage.target-kinerja.harian.input') }}" class="flex rounded-[5.87px]">
                <div class="flex justify-center items-center gap-[5.87px] bg-[#0070ff] px-[11.75px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>@endif
        <x-tb id="targetHarianTable">
            <x-slot:table_header>
                {{-- <x-tb-td nama="no" sorting=false>No</x-tb-td> --}}
                <x-tb-td nama="pekerjaan" sorting=true>Pekerjaan</x-tb-td>
                <x-tb-td nama="target_kinerja" sorting=true>Target Kinerja</x-tb-td>
                <x-tb-td nama="jumlah" sorting=true>Jumlah</x-tb-td>
                <x-tb-td nama="waktu" sorting=true>Waktu (menit)</x-tb-td>
                <x-tb-td nama="start" sorting=true>Start</x-tb-td>
                <x-tb-td nama="end" sorting=true>End</x-tb-td>
                <x-tb-td nama="action" sorting=false>Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach($items as $i => $it)
                    <x-tb-cl id="{{ $it->id }}">
                        {{-- <x-tb-cl-fill>{{ $i+1 }}</x-tb-cl-fill> --}}
                        <x-tb-cl-fill>{{ $it->pekerjaan }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->targetKinerja->nama ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->jumlah ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->waktu_minutes ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->start ? \Carbon\Carbon::parse($it->start)->format('d/m/Y H:i') : '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $it->end ? \Carbon\Carbon::parse($it->end)->format('d/m/Y H:i') : '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('manage.target-kinerja.harian.view', $it->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-blue-600" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('manage.target-kinerja.harian.isi', $it->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-green-600" title="Isi Laporan">
                                    <i class="bi bi-journal-plus"></i>
                                </a>
                                <a href="{{ route('manage.target-kinerja.harian.assign', $it->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-indigo-600" title="Assign Pegawai">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                                <form action="{{ route('manage.target-kinerja.harian.destroy', $it->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Hapus?')" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-red-600" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforeach
            </x-slot:table_column>
        </x-tb>
    </div>
@endsection
