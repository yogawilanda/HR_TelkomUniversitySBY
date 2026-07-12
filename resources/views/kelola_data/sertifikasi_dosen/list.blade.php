@php
    $active_sidebar = 'Sertifikasi Dosen';
@endphp
@extends('kelola_data.base')
@section('header-base')
    <style>
        .max-w-100 {
            max-width: 100% !important;
        }
    </style>
@endsection
@section('page-name')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch"><span
                    class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Sertifikasi Dosen</span>
            </div><span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Kelola
                data sertifikasi dosen</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
            <x-print-tb target_id="sertifikasiTable"></x-print-tb>
            <x-export-csv-tb target_id="sertifikasiTable"></x-export-csv-tb>
            <a href="{{ route('manage.sertifikasi-dosen.input') }}" class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </a>
        </div>
    </div>
@endsection
@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <x-tb id="sertifikasiTable">
            <x-slot:table_header>
                {{-- <x-tb-td nama="no" sorting=true>No</x-tb-td> --}}
                <x-tb-td nama="nama_dosen" sorting=true>Nama Dosen</x-tb-td>
                <x-tb-td nama="nidn" sorting=true>NIDN</x-tb-td>
                <x-tb-td nama="prodi" sorting=true>Prodi</x-tb-td>
                <x-tb-td nama="nomor_registrasi" sorting=true>Nomor Registrasi</x-tb-td>
                {{-- <x-tb-td nama="no_sk" sorting=true>No SK</x-tb-td> --}}
                {{-- <x-tb-td nama="tanggal_sk" sorting=true>Tanggal SK</x-tb-td> --}}
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach ($sertifikasi as $index => $item)
                    <x-tb-cl id="{{ $item->id }}">
                        {{-- <x-tb-cl-fill>{{ $index + 1 }}</x-tb-cl-fill> --}}
                        <x-tb-cl-fill>{{ $item->dosen->pegawai->nama_lengkap ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->dosen->nidn ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->dosen->prodi->position_name ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $item->nomor_registrasi ?? '-' }}</x-tb-cl-fill>
                        {{-- <x-tb-cl-fill>{{ $item->no_sk ?? '-' }}</x-tb-cl-fill> --}}
                        {{-- <x-tb-cl-fill>{{ $item->tanggal_sk ? $item->tanggal_sk->format('d/m/Y') : '-' }}</x-tb-cl-fill> --}}
                        <x-tb-cl-fill>
                            <div class="flex gap-2">
                                <a href="{{ route('manage.sertifikasi-dosen.view', $item->id) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('manage.sertifikasi-dosen.edit', $item->id) }}"
                                    class="text-yellow-600 hover:text-yellow-800">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                {{-- <form action="{{ route('manage.sertifikasi-dosen.destroy', $item->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form> --}}
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforeach
            </x-slot:table_column>
        </x-tb>
    </div>
@endsection
