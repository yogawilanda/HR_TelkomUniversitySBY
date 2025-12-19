@php
    $active_sidebar = 'Kontrak Unit';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Kontrak Unit</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Kelola data kontrak unit</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <a href="{{ route('manage.kontrak-unit.input') }}" class="flex rounded-[5.87px]">
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
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <x-tb id="kontrakUnitTable">
            <x-slot:table_header>
                <x-tb-td nama="kontrak" sorting=true>Kontrak Manajemen</x-tb-td>
                <x-tb-td nama="nama_unit" sorting=true>Nama Unit</x-tb-td>
                <x-tb-td nama="pekerjaan">Pekerjaan</x-tb-td>
                <x-tb-td nama="target">Target</x-tb-td>
                <x-tb-td nama="status">Status Kinerja</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach ($kontrakUnit as $item)
                    <x-tb-cl id="{{ $item->id }}">
                        <x-tb-cl-fill>{{ $item->kontrakManajemen->nama ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->nama_unit }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ Str::limit($item->pekerjaan, 50) }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->jumlah ?? 0 }} {{ $item->result ?? '' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            @if($item->kinerjaUnit)
                                <span class="px-2 py-1 rounded text-xs 
                                    @if($item->kinerjaUnit->status === 'completed') bg-green-100 text-green-800 
                                    @elseif($item->kinerjaUnit->status === 'in_progress') bg-blue-100 text-blue-800 
                                    @else bg-gray-100 text-gray-800 
                                    @endif">
                                    {{ ucfirst($item->kinerjaUnit->status) }}
                                </span>
                            @else
                                -
                            @endif
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('manage.kontrak-unit.assign', $item->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-green-600" title="Assign Pegawai">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                                <a href="{{ route('manage.kontrak-unit.view', $item->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-blue-600" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('manage.kontrak-unit.edit', $item->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-yellow-600" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('manage.kontrak-unit.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-red-600" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">
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
