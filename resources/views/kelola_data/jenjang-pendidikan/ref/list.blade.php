@php
    $active_sidebar = 'Jenjang Pendidikan';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <x-page-freeze></x-page-freeze>
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Master Jenjang Pendidikan</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">
                Kelola data jenjang pendidikan mulai dari tingkat hingga urutan akademik
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <a href="{{ route('manage.jenjang-pendidikan.ref.new') }}"
             class="flex route_pop_up bg-[#0070ff] px-[11.75px] py-[7.34px] rounded-[5.87px] text-white text-xs gap-1 hover:bg-[#005fe0] transition"
                >
                <i class="bi bi-plus text-sm"></i>
                <span class="font-medium text-[10.28px]">Tambah Jenjang</span>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    <x-modal-view :footer="false" :head="false" id="modal-detail-jenjang" title="Detail Jenjang">
        <div class="flex flex-col gap-3 px-8 py-8">
            <div class="flex items-center gap-5">
                <span class="font-semibold text-xl text-[#101828]">Data Jenjang</span>
                <button id="btn-edit-jenjang"
                    class="bg-[#0070ff] text-white font-medium text-xs px-3 py-1 rounded border border-[#0070ff] hover:bg-[#005bd4] transition">
                    Ubah Data
                </button>
            </div>
            <div class="flex gap-12 w-full mt-4">
                <div class="flex flex-col gap-2 w-1/2 text-sm font-light text-black">
                    <span>Kode Jenjang</span>
                    <span>Tingkat</span>
                    <span>Urutan</span>
                    <span>Gelar</span>
                </div>
                <div class="flex flex-col gap-2 w-1/2 text-sm font-normal text-black">
                    <span id="det-kode">-</span>
                    <span id="det-tingkat">-</span>
                    <span id="det-urut">-</span>
                    <span id="det-gelar">-</span>
                </div>
            </div>
        </div>
    </x-modal-view>

    <div class="w-full">
        <x-tb id="JenjangTable">
            <x-slot:table_header>
                <x-tb-td nama="kode">Kode Jenjang</x-tb-td>
                <x-tb-td nama="tingkat">Tingkat</x-tb-td>
                <x-tb-td sorting="true" nama="urut">Urutan</x-tb-td>
                <x-tb-td nama="gelar">Gelar</x-tb-td>
                <x-tb-td>Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @foreach ($data as $d)
                    <x-tb-cl id="{{ $d['id'] }}" idTargetModal="modal-detail-jenjang">
                        <x-tb-cl-fill id="col-kode" clsText="text-center">{{ $d['jenjang_pendidikan'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill id="col-tingkat">{{ $d['tingkat'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill id="col-urut" clsText="text-center">{{ $d['urutan'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill id="col-gelar" clsText="text-center">{{ $d['kode_gelar'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('manage.jenjang-pendidikan.ref.edit', ['id' => $d->id]) }}"
                                    class="px-4 py-1.5 border route_pop_up border-blue-600 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-600 hover:text-white transition-all duration-200">
                                    Ubah
                                </a>

                                {{-- <form action="" method="post" class="m-0">
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')"
                                        class="px-4 py-1.5 border border-red-500 text-red-500 rounded-lg text-xs font-medium hover:bg-red-500 hover:text-white transition-all duration-200">
                                        Hapus
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

@push('script-under-base')
    <script>
        document.addEventListener('click', function(e) {
            const row = e.target.closest('.x-tb-cl');
            if (row && !e.target.closest('a')) {
                const modal = document.querySelector('#modal-detail-jenjang');
                const id = row.getAttribute('id');

                modal.querySelector('#det-kode').textContent = row.querySelector('#col-kode').textContent;
                modal.querySelector('#det-tingkat').textContent = row.querySelector('#col-tingkat').textContent;
                modal.querySelector('#det-urut').textContent = row.querySelector('#col-urut').textContent;
                modal.querySelector('#det-gelar').textContent = row.querySelector('#col-gelar').textContent;

                modal.querySelector('#btn-edit-jenjang').setAttribute('onclick',
                    `window.location.href='/manage/jenjang/update/${id}'`);
            }
        });
    </script>
@endpush
