@php
    $active_sidebar = 'Status Pegawai';
@endphp

@extends('kelola_data.base')

@section('header-base')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .max-w-100 { max-width: 100% !important; }
        .nav-active { background-color: #0070ff; }
        .nav-active span { color: white; }
        /* Memperhalus tampilan loading pada Swal */
        .swal2-loader {
            margin-top: 1.5em !important;
        }
    </style>
@endsection

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.74px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Daftar Status Pegawai</span>
            </div>
            <span class="font-normal text-[10.28px] text-[#1f2028]">Kelola master data status kepegawaian Anda disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.74px]">
            <x-print-tb target_id="StatusTable"></x-print-tb>

            {{-- Tombol Tambah --}}
            <button onclick="openStatusModal()" class="flex items-center gap-[5.87px] bg-[#0070ff] px-[11.74px] py-[7.34px] rounded-[5.87px] border border-[#0070ff] hover:bg-[#005fe0] transition text-white">
                <i class="bi bi-plus text-sm"></i>
                <span class="font-medium text-[10.28px]">Tambah Status</span>
            </button>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <x-tb id="StatusTable">
            <x-slot:table_header>
                <x-tb-td nama="status_pegawai" sorting=true>Status Pegawai</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>

            <x-slot:table_column>
                @foreach ($data as $sp)
                    <x-tb-cl>
                        <x-tb-cl-fill><strong>{{ $sp->status_pegawai }}</strong></x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Ubah --}}
                                <button onclick="openStatusModal('{{ $sp->id }}', '{{ $sp->status_pegawai }}')"
                                    class="px-3 py-1 bg-amber-500 text-white rounded-md text-[10px] hover:bg-amber-600 transition">
                                    <i class="bi bi-pencil-square mr-1"></i> Ubah
                                </button>

                                {{-- Tombol Hapus (Tanpa Fungsi/Non-aktif) --}}
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
            <input type="hidden" name="id" id="form-id">
            <input type="hidden" name="status_pegawai" id="form-status-pegawai">
        </form>
    </div>

    @include('kelola_data.pegawai.js.alert-success-from-controller')

    <script>
        function openStatusModal(id = '', currentStatus = '') {
            const isEdit = id !== '';

            Swal.fire({
                title: isEdit ? 'Ubah Status Pegawai' : 'Tambah Status Pegawai',
                html: `
                    <div style="text-align: left;">
                        <label for="swal-input-status" style="display:block; margin-bottom:5px; font-weight:600;">Nama Status Pegawai:</label>
                        <input id="swal-input-status" class="swal2-input"
                               placeholder="Contoh: Pegawai Tetap"
                               value="${currentStatus}"
                               style="margin-top:0; width: 85%;">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                allowOutsideClick: () => !Swal.isLoading(), // Cegah klik luar saat loading
                preConfirm: () => {
                    const statusVal = document.getElementById('swal-input-status').value;
                    if (!statusVal) {
                        Swal.showValidationMessage('Nama status tidak boleh kosong!');
                        return false;
                    }

                    // Trigger Loading Indicator
                    Swal.showLoading(Swal.getConfirmButton());

                    return { status_pegawai: statusVal };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('main-action-form');

                    if (isEdit) {
                        form.action = "{{ route('manage.status-pegawai.update') }}";
                        document.getElementById('form-id').value = id;
                    } else {
                        form.action = "{{ route('manage.status-pegawai.create') }}";
                        document.getElementById('form-id').value = "";
                    }

                    document.getElementById('form-status-pegawai').value = result.value.status_pegawai;

                    // Submit form
                    form.submit();
                }
            });
        }
    </script>
@endsection
