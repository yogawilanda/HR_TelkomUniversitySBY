@php
    $active_sidebar = 'Master JFK';
@endphp

@extends('kelola_data.base')

@section('header-base')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .max-w-100 { max-width: 100% !important; }
        .nav-active { background-color: #0070ff; }
        .nav-active span { color: white; }
        .swal2-loader {
            margin-top: 1.5em !important;
        }
    </style>
@endsection

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.74px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Daftar Jabatan Fungsional (JFK)</span>
            </div>
            <span class="font-normal text-[10.28px] text-[#1f2028]">Kelola master data nama jabatan fungsional kesehatan Anda disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.74px]">
            <x-print-tb target_id="JFKTable"></x-print-tb>

            <button onclick="openJFKModal()" class="flex items-center gap-[5.87px] bg-[#0070ff] px-[11.74px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition text-white">
                <i class="bi bi-plus text-sm"></i>
                <span class="font-medium text-[10.28px]">Tambah JFK</span>
            </button>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <x-tb id="JFKTable">
            <x-slot:table_header>
                <x-tb-td nama="nama_jfk" sorting=true>Nama JFK</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @foreach ($data as $jfk)
                    <x-tb-cl>
                        <x-tb-cl-fill><strong>{{ $jfk->nama_jfk }}</strong></x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openJFKModal('{{ $jfk->id }}', '{{ $jfk->nama_jfk }}')"
                                    class="px-3 py-1 bg-amber-500 text-white rounded-md text-[10px] hover:bg-amber-600 transition">
                                    <i class="bi bi-pencil-square mr-1"></i> Ubah
                                </button>

                                {{-- <button type="button" class="px-3 py-1 bg-red-500 text-white rounded-md text-[10px] opacity-50 cursor-not-allowed">
                                    <i class="bi bi-trash mr-1"></i> Hapus
                                </button> --}}
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforeach
            </x-slot:table_column>
        </x-tb>

        {{-- Form Hidden --}}
        <form id="main-action-form" action="" method="POST" class="hidden">
            @csrf
            {{-- Menggunakan POST karena route Anda hanya mendukung POST --}}
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="id" id="form-id">
            <input type="hidden" name="nama_jfk" id="form-nama-jfk">
        </form>
    </div>

    @include('kelola_data.pegawai.js.alert-success-from-controller')

    <script>
        function openJFKModal(id = '', currentJFK = '') {
            const isEdit = id !== '';

            Swal.fire({
                title: isEdit ? 'Ubah Nama JFK' : 'Tambah Nama JFK',
                html: `
                    <div style="text-align: left;">
                        <label for="swal-input-jfk" style="display:block; margin-bottom:5px; font-weight:600;">Nama JFK:</label>
                        <input id="swal-input-jfk" class="swal2-input"
                               placeholder="Contoh: Perawat Ahli Madya"
                               value="${currentJFK}"
                               style="margin-top:0; width: 85%;">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: () => {
                    const jfkVal = document.getElementById('swal-input-jfk').value;
                    if (!jfkVal) {
                        Swal.showValidationMessage('Nama JFK tidak boleh kosong!');
                        return false;
                    }

                    Swal.showLoading(Swal.getConfirmButton());
                    return { nama_jfk: jfkVal };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('main-action-form');
                    const methodInput = document.getElementById('form-method');
                    const idInput = document.getElementById('form-id');
                    const nameInput = document.getElementById('form-nama-jfk');

                    // Reset Method ke POST (sesuai dukungan route Anda)
                    methodInput.value = "POST";
                    nameInput.value = result.value.nama_jfk;

                    if (isEdit) {
                        // Pasang ID ke URL rute update
                        let url = "{{ route('manage.jfk.ref.update', ':id') }}";
                        form.action = url.replace(':id', id);
                        idInput.value = id;
                    } else {
                        // Rute store
                        form.action = "{{ route('manage.jfk.ref.store') }}";
                        idInput.value = "";
                    }

                    form.submit();
                }
            });
        }
    </script>
@endsection
