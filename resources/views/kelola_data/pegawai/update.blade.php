@php
    $active_sidebar = 'Tambah Pegawai Baru';
@endphp

@extends('kelola_data.base')

@section('header-base')
    <style>
        .max-w-100 {
            max-width: 100% !important;
        }

        .nav-active {
            background-color: #0070ff;
        }

        .nav-active span {
            color: white;
        }
    </style>
@endsection

@section('page-name')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">
                    Ubah Data Pegawai
                </span>
            </div>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
            {{-- <x-export-csv-tb target_id="pegawaiTable"></x-export-csv-tb> --}}
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                        ? route('manage.pegawai.update', ['id_user' => $user->id])
                        : route('profile.update', ['id_user' => session('account')['id']]) }}">


        {{-- Data Diri --}}
        <div class="flex flex-col gap-8 w-full max-w-100 mx-auto rounded-md border p-3">
            <h2 class="text-lg font-semibold text-black text-center">Data Diri Pegawai</h2>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- Kolom Kiri --}}
                <div class="flex flex-col gap-4">
                    <x-itxt lbl="Nama Lengkap" plc="John Doe" nm="nama_lengkap" max="100"
                        val="{{ $user->nama_lengkap }}"></x-itxt>

                    <x-itxt lbl="Username" plc="johndoe" nm="username" max="20"
                        val="{{ $user->username }}"></x-itxt>

                    <x-itxt lbl="Telepon" plc="081234567890" nm="telepon" max="13" :rules="['Harus dimulai dengan 0', 'berjumlah 10-13 digit']"
                        val="{{ $user->telepon }}"></x-itxt>

                    <x-itxt type="textarea" lbl="Alamat" plc="Jl. Telekomunikasi No. 1, Bandung" nm="alamat"
                        max="300" fill="flex-grow" val="{{ $user->alamat }}"></x-itxt>
                </div>

                {{-- Kolom Kanan --}}
                <div class="flex flex-col gap-4">
                    <x-itxt lbl="Nomor Induk Kependudukan (NIK)" plc="3568165xxxxxxxxx" nm="nik" max="20"
                        val="{{ $user->nik }}"></x-itxt>

                    <x-itxt type="email" lbl="Email Pribadi" plc="johndoe@gmail.com" nm="email_pribadi" max="150"
                        val="{{ $user->email_pribadi }}"></x-itxt>

                    <x-itxt type="email" lbl="Email Institusi" plc="john.doe@telkomuniversity.ac.id" nm="email_institusi"
                        max="150" val="{{ $user->email_institusi }}"></x-itxt>

                    <x-islc lbl="Jenis Kelamin" nm="jenis_kelamin">
                        <option value="Laki-laki" {{ $user->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                        </option>
                        <option value="Perempuan" {{ $user->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan
                        </option>
                    </x-islc>

                    <div class="flex flex-col xl:flex-row justify-between w-full gap-3">
                        <x-itxt lbl="Tempat Lahir" fill="flex-1" plc="Surabaya" nm="tempat_lahir"
                            val="{{ $user->tempat_lahir }}"></x-itxt>
                        <x-itxt type="date" fill="flex-1" lbl="Tanggal Lahir" nm="tgl_lahir" max="2025-10-27"
                            rules="none" val="{{ \Carbon\Carbon::parse($user->tgl_lahir)->format('Y-m-d') }}"></x-itxt>
                    </div>
                </div>
            </div>
        </div>
    </x-form>
@endsection

@push('script-under-base')
    @if (session('error') || $errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Waduh!',
                html: "{!! session('error') ?? 'Ada Masalah!' !!}",
                confirmButtonText: 'Oke, Saya Cek Lagi',
                confirmButtonColor: '#d33',
            });
        </script>
    @endif

    @if (session()->has('testing') && session('testing') == 'Ubah Data Pegawai')
        {{-- {{ dd('masuk') }} --}}
        @php
            $testingQuestions = [
                [
                    'key' => 'L1',
                    'label' => 'Apakah fitur yang digunakan berjalan sesuai kebutuhan?',
                    'type' => 'scale',
                    'labels' => ['Tidak Sesuai', 'Sangat Sesuai'], // Kustom label
                ],
                [
                    'key' => 'L2',
                    'label' => 'Fitur ini mudah dipahami dan digunakan.',
                    'type' => 'scale',
                    'labels' => ['Sangat Sulit', 'Sangat Mudah'],
                ],
                [
                    'key' => 'L3',
                    'label' => 'Fitur ini berjalan cepat dan responsif.',
                    'type' => 'scale',
                    'labels' => ['Lambat', 'Sangat Cepat'],
                ],
                [
                    'key' => 'L4',
                    'label' => 'Fitur ini berjalan stabil tanpa error.',
                    'type' => 'scale',
                    'labels' => ['Banyak Bug', 'Sangat Stabil'],
                ],
                [
                    'key' => 'L7',
                    'label' => 'Apakah secara visual sistem dapat digunakan dengan baik di perangkat/browser yang Anda pakai?',
                    'type' => 'scale',
                    'labels' => ['Sangat Bermasalah', 'Sangat Baik']
                ],
                [
                    'key' => 'L5',
                    'label' => 'Tampilan fitur ini menarik dan nyaman dilihat?',
                    'type' => 'scale',
                    'labels' => ['Buruk', 'Sangat Bagus'],
                ],
                [
                    'key' => 'L6',
                    'label' => 'Apa kendala utama yang Anda alami?',
                    'type' => 'text',
                ]

            ];
        @endphp

        <x-question-testing route="{{ route('testing', ['kode' => '1T3', 'nama_fitur' => 'ubah data pegawai']) }}" page="{{ session('testing') }}"
            fitur_code="1T3" :config="$testingQuestions" />
    @endif
@endpush
