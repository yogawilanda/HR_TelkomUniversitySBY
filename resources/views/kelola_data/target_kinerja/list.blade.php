@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Target Kinerja</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Kelola data target kinerja</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <div class="hidden sm:flex items-center gap-2">
                @include('kelola_data.parts.target_kinerja_toolbar')
            </div>
            <a href="{{ route('manage.target-kinerja.input') }}" class="flex rounded-[5.87px]">
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
        <x-tb id="targetKinerjaTable">
            <x-slot:table_header>
                {{-- <x-tb-td nama="no" sorting=true>No</x-tb-td> --}}
                <x-tb-td nama="nama" sorting=true>Nama</x-tb-td>
                <x-tb-td nama="bobot" sorting=true>Bobot</x-tb-td>
                <x-tb-td nama="responsibility">Responsibility</x-tb-td>
                <x-tb-td nama="status">Status</x-tb-td>
                <x-tb-td nama="is_active">Active</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach ($targetKinerja as $index => $item)
                    <x-tb-cl id="{{ $item->id }}">
                        <x-tb-cl-fill>{{ $item->nama }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->bobot }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->responsibility ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->status ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->is_active ? 'Ya' : 'Tidak' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('manage.target-kinerja.assign', $item->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-green-600" title="Assign Pegawai">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                                <a href="{{ route('manage.target-kinerja.view', $item->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-blue-600" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('manage.target-kinerja.edit', $item->id) }}" class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out text-yellow-600" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('manage.target-kinerja.destroy', $item->id) }}" method="POST" class="inline">
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
