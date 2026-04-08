@php
    $active_sidebar = 'Import Pegawai';
@endphp

@extends('kelola_data.base')

@section('title-page')
    {{ $active_sidebar }}
@endsection

@section('header-base')
@endsection

@section('page-name')
    Validasi Data import
@endsection

@section('content-base')
    <form action="{{ route('manage.pegawai.import.save-data') }}" id="formSaveData" method="POST"
        class="flex flex-grow-0 flex-col gap-2 max-w-100">
        @csrf
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59c.75 1.334-.214 2.986-1.742 2.986H3.48c-1.528 0-2.492-1.652-1.742-2.986L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 012 0v3a1 1 0 01-1 1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h3 class="text-base font-semibold leading-6">
                        Silahkan cek lagi data Anda
                    </h3>
                    <p class="mt-1 text-sm text-amber-800">
                        Ini adalah validasi untuk yang terakhir kalinya.
                    </p>

                    <ul class="mt-3 space-y-2 text-sm text-amber-900">
                        <li class="flex gap-2">
                            <span class="mt-1 inline-block h-2 w-2 shrink-0 rounded-full bg-red-500"></span>
                            <span>
                                Silahkan isi data yang masih <b>Kosong</b>
                                (ditandai berwarna <span class="font-semibold text-red-600">merah</span>).
                            </span>
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 inline-block h-2 w-2 shrink-0 rounded-full bg-red-500"></span>
                            <span>
                                Emergency Contact jika hanya terisi 1 disalah 1 data maka akan <b>dianggap tidak ada</b>
                                (<span class="font-semibold text-red-600">minimal ada 2</span>) dari keseluruhan data
                                emergency contact agar terbaca oleh sistem.)
                            </span>
                        </li>

                        <li class="flex gap-2">
                            <span class="mt-1 inline-block h-2 w-2 shrink-0 rounded-full bg-amber-500"></span>
                            <span>
                                Cara edit: klik data, maka Anda akan bisa mengeditnya.
                            </span>
                        </li>

                        <!-- 🔥 TAMBAHAN BARU -->
                        <li class="flex gap-2">
                            <span class="mt-1 inline-block h-2 w-2 shrink-0 rounded-full bg-sky-500"></span>
                            <span>
                                Jika sudah memvalidasi dan melengkapi seluruh data,
                                silahkan menuju ke <b>bagian paling bawah halaman</b> dan
                                klik tombol
                                <a href="#import-data-btn"
                                    class="font-semibold text-sky-600 underline underline-offset-2 hover:text-sky-700">
                                    Import Data Sekarang
                                </a>.
                            </span>
                        </li>
                    </ul>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1
                           text-xs font-medium text-amber-900 ring-1 ring-amber-200">
                            <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                            Kosong = merah
                        </span>

                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1
                           text-xs font-medium text-amber-900 ring-1 ring-amber-200">
                            <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
                            Klik untuk edit
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <x-tb id="formasiTable" search_status=false>
            <x-slot:table_header>
                <x-tb-td nama="action">Aksi</x-tb-td>
                <x-tb-td nama="nama_lengkap">Nama Lengkap*</x-tb-td>
                <x-tb-td nama="nik">Nomor Induk Kependudukan*</x-tb-td>
                <x-tb-td nama="username">Username*</x-tb-td>
                <x-tb-td nama="telepon">Telepon*</x-tb-td>
                <x-tb-td nama="email_pribadi">Email Pribadi*</x-tb-td>
                <x-tb-td nama="email_institusi">Email Institusi*</x-tb-td>
                <x-tb-td nama="no_telepon_darurat">No Telepon Darurat*</x-tb-td>
                <x-tb-td nama="jenis_kelamin">Jenis Kelamin*</x-tb-td>
                <x-tb-td nama="alamat">Alamat*</x-tb-td>
                <x-tb-td nama="tempat_lahir">Tempat Lahir*</x-tb-td>
                <x-tb-td nama="tanggal_lahir">Tanggal Lahir (hh/bb/tttt)*</x-tb-td>
                <x-tb-td nama="tipe_pegawai">Tipe Pegawai*</x-tb-td>
                <x-tb-td nama="status_kepegawaian">Status Kepegawaian*</x-tb-td>
                <x-tb-td nama="nip">NIP*</x-tb-td>
                <x-tb-td nama="tanggal_berlaku_nip">Tanggal Berlaku NIP (hh/bb/tttt)</x-tb-td>
                <x-tb-td nama="jabatan">Jabatan*</x-tb-td>

                <x-tb-td nama="ec1_action">Aksi Emergency Contact 1 (EC1)</x-tb-td>
                <x-tb-td nama="ec1_status_hubungan">EC1 Status Hubungan</x-tb-td>
                <x-tb-td nama="ec1_nama_lengkap">EC1 Nama Lengkap</x-tb-td>
                <x-tb-td nama="ec1_telepon">EC1 Telepon</x-tb-td>
                <x-tb-td nama="ec1_email">EC1 Email</x-tb-td>
                <x-tb-td nama="ec1_alamat">EC1 Alamat</x-tb-td>

                <x-tb-td nama="ec2_action">Aksi Emergency Contact 2 (EC2)</x-tb-td>
                <x-tb-td nama="ec2_status_hubungan">EC2 Status Hubungan</x-tb-td>
                <x-tb-td nama="ec2_nama_lengkap">EC2 Nama Lengkap</x-tb-td>
                <x-tb-td nama="ec2_telepon">EC2 Telepon</x-tb-td>
                <x-tb-td nama="ec2_email">EC2 Email</x-tb-td>
                <x-tb-td nama="ec2_alamat">EC2 Alamat</x-tb-td>

                <x-tb-td nama="ec3_action">Aksi Emergency Contact 3 (EC3)</x-tb-td>
                <x-tb-td nama="ec3_status_hubungan">EC3 Status Hubungan</x-tb-td>
                <x-tb-td nama="ec3_nama_lengkap">EC3 Nama Lengkap</x-tb-td>
                <x-tb-td nama="ec3_telepon">EC3 Telepon</x-tb-td>
                <x-tb-td nama="ec3_email">EC3 Email</x-tb-td>
                <x-tb-td nama="ec3_alamat">EC3 Alamat</x-tb-td>

                <x-tb-td nama="ec4_action">Aksi Emergency Contact 4 (EC4)</x-tb-td>
                <x-tb-td nama="ec4_status_hubungan">EC4 Status Hubungan</x-tb-td>
                <x-tb-td nama="ec4_nama_lengkap">EC4 Nama Lengkap</x-tb-td>
                <x-tb-td nama="ec4_telepon">EC4 Telepon</x-tb-td>
                <x-tb-td nama="ec4_email">EC4 Email</x-tb-td>
                <x-tb-td nama="ec4_alamat">EC4 Alamat</x-tb-td>

                {{-- <x-tb-td type="select" nama="bagian" >SK YPT</x-tb-td> --}}
                {{-- <x-tb-td nama="action" >Action</x-tb-td> --}}
                {{-- <x-tb-td nama="email_pribadi"></x-tb-td> --}}
            </x-slot:table_header>

            <x-slot:table_column>

                {{-- @if (old())
                    {{ dd(old(),$data) }}
                @endif --}}


                @php
                    $cek = null;
                    $arrayData = null;
                    if (old()) {
                        // dd(count(old()['nama_lengkap']));
                        $index = count(old()['nama_lengkap']) ?? null;
                        $arrayData = old();
                        $cek = 'a';
                    } elseif (session()->has('old_data_import')) {
                        if (session('old_data_import') != null) {
                            $index = count(session('old_data_import')['nama_lengkap']);
                            $arrayData = session('old_data_import');
                            // dd($index);
                            $cek = 'b';
                        } else {
                            $index = null;
                            $cek = 'd';
                        }
                    } else {
                        $index = null;
                        $cek = 'c';
                    }
                @endphp
                {{-- {{ dd($cek) }} --}}

                @if ($index != null)
                    @for ($f = 0; $f < $index; $f++)
                        <x-tb-cl>
                            @php
                                $save_temp = $arrayData['nama_lengkap'][$f];
                            @endphp
                            <x-editable-cell :idx="$f" editable="false" name="action" isNeed="false"
                                :has_Special_Button='true' onclick="deleteAll('{{ $save_temp }}','{{ $f }}',this)"
                                caption_special_button="Hapus semua data milik {{ $save_temp }} {{ $cek }}" />

                            {{-- DATA UTAMA --}}
                            <x-editable-cell :idx="$f" name="nama_lengkap" :value="$arrayData['nama_lengkap'][$f]" />
                            <x-editable-cell :idx="$f" name="nik" :value="$arrayData['nik'][$f]" />
                            <x-editable-cell :idx="$f" name="username" :value="$arrayData['username'][$f]" />
                            <x-editable-cell :idx="$f" name="telepon" :value="$arrayData['telepon'][$f]" />
                            <x-editable-cell :idx="$f" name="email_pribadi" :value="$arrayData['email_pribadi'][$f]" />
                            <x-editable-cell :idx="$f" name="email_institusi" :value="$arrayData['email_institusi'][$f]" />
                            <x-editable-cell :idx="$f" name="telepon_darurat" :value="$arrayData['telepon_darurat'][$f]" />
                            <x-editable-cell :idx="$f" name="jenis_kelamin" :value="$arrayData['jenis_kelamin'][$f]" />
                            <x-editable-cell :idx="$f" name="alamat" :value="$arrayData['alamat'][$f]" />
                            <x-editable-cell :idx="$f" name="tempat_lahir" :value="$arrayData['tempat_lahir'][$f]" />
                            <x-editable-cell :idx="$f" name="tgl_lahir" :value="$arrayData['tgl_lahir'][$f]" />
                            <x-editable-cell :idx="$f" name="tipe_pegawai" :value="$arrayData['tipe_pegawai'][$f]" />
                            <x-editable-cell :idx="$f" name="status_kepegawaian" :value="$arrayData['status_kepegawaian'][$f]" />
                            <x-editable-cell :idx="$f" name="nip" :value="$arrayData['nip'][$f]" />
                            <x-editable-cell :idx="$f" name="tmt_mulai" :value="$arrayData['tmt_mulai'][$f]" />
                            <x-editable-cell :idx="$f" name="jabatan" :value="$arrayData['jabatan'][$f]" />

                            {{-- EMERGENCY CONTACT 1 --}}
                            <x-editable-cell :idx="$f" editable="false" name="action1" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(1,'{{ $save_temp }}','{{ $f }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 1 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$f" name="ec1_status_hubungan" :value="$arrayData['ec1_status_hubungan'][$f]" />
                            <x-editable-cell :idx="$f" name="ec1_nama_lengkap" :value="$arrayData['ec1_nama_lengkap'][$f]" />
                            <x-editable-cell :idx="$f" name="ec1_telepon" :value="$arrayData['ec1_telepon'][$f]" />
                            <x-editable-cell :idx="$f" name="ec1_email" :value="$arrayData['ec1_email'][$f]" />
                            <x-editable-cell :idx="$f" name="ec1_alamat" :value="$arrayData['ec1_alamat'][$f]" />

                            {{-- EMERGENCY CONTACT 2 --}}
                            <x-editable-cell :idx="$f" editable="false" name="action2" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(2,'{{ $save_temp }}','{{ $f }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 2 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$f" name="ec2_status_hubungan" :value="$arrayData['ec2_status_hubungan'][$f]" />
                            <x-editable-cell :idx="$f" name="ec2_nama_lengkap" :value="$arrayData['ec2_nama_lengkap'][$f]" />
                            <x-editable-cell :idx="$f" name="ec2_telepon" :value="$arrayData['ec2_telepon'][$f]" />
                            <x-editable-cell :idx="$f" name="ec2_email" :value="$arrayData['ec2_email'][$f]" />
                            <x-editable-cell :idx="$f" name="ec2_alamat" :value="$arrayData['ec2_alamat'][$f]" />

                            {{-- EMERGENCY CONTACT 3 --}}
                            <x-editable-cell :idx="$f" editable="false" name="action3" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(3,'{{ $save_temp }}','{{ $f }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 3 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$f" name="ec3_status_hubungan" :value="$arrayData['ec3_status_hubungan'][$f]" />
                            <x-editable-cell :idx="$f" name="ec3_nama_lengkap" :value="$arrayData['ec3_nama_lengkap'][$f]" />
                            <x-editable-cell :idx="$f" name="ec3_telepon" :value="$arrayData['ec3_telepon'][$f]" />
                            <x-editable-cell :idx="$f" name="ec3_email" :value="$arrayData['ec3_email'][$f]" />
                            <x-editable-cell :idx="$f" name="ec3_alamat" :value="$arrayData['ec3_alamat'][$f]" />

                            {{-- EMERGENCY CONTACT 4 --}}
                            <x-editable-cell :idx="$f" editable="false" name="action4" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(4,'{{ $save_temp }}','{{ $f }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 4 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$f" name="ec4_status_hubungan" :value="$arrayData['ec4_status_hubungan'][$f]" />
                            <x-editable-cell :idx="$f" name="ec4_nama_lengkap" :value="$arrayData['ec4_nama_lengkap'][$f]" />
                            <x-editable-cell :idx="$f" name="ec4_telepon" :value="$arrayData['ec4_telepon'][$f]" />
                            <x-editable-cell :idx="$f" name="ec4_email" :value="$arrayData['ec4_email'][$f]" />
                            <x-editable-cell :idx="$f" name="ec4_alamat" :value="$arrayData['ec4_alamat'][$f]" />
                        </x-tb-cl>
                    @endfor
                @else
                    @forelse ($data as $i => $row)
                        {{-- {{ dd($data) }} --}}
                        <x-tb-cl>
                            @php
                                $save_temp = $row['nama_lengkap'];
                            @endphp
                            <x-editable-cell :idx="$i" editable="false" name="action" isNeed="false"
                                :has_Special_Button='true' onclick="deleteAll('{{ $save_temp }}','{{ $i }}',this)"
                                caption_special_button="Hapus semua data milik {{ $save_temp }} {{ $cek }}" />

                            {{-- DATA UTAMA --}}
                            <x-editable-cell :idx="$i" name="nama_lengkap" :value="old('nama_lengkap.' . $i, $row['nama_lengkap'])" />
                            <x-editable-cell :idx="$i" name="nik" :value="old('nik.' . $i, $row['nik'])" />
                            <x-editable-cell :idx="$i" name="username" :value="old('username.' . $i, $row['username'])" />
                            <x-editable-cell :idx="$i" name="telepon" :value="old('telepon.' . $i, $row['telepon'])" />
                            <x-editable-cell :idx="$i" name="email_pribadi" :value="old('email_pribadi.' . $i, $row['email_pribadi'])" />
                            <x-editable-cell :idx="$i" name="email_institusi" :value="old('email_institusi.' . $i, $row['email_institusi'])" />
                            <x-editable-cell :idx="$i" name="telepon_darurat" :value="old('telepon_darurat.' . $i, $row['telepon_darurat'])" />
                            <x-editable-cell :idx="$i" name="jenis_kelamin" :value="old('jenis_kelamin.' . $i, $row['jenis_kelamin'])" />
                            <x-editable-cell :idx="$i" name="alamat" :value="old('alamat.' . $i, $row['alamat'])" />
                            <x-editable-cell :idx="$i" name="tempat_lahir" :value="old('tempat_lahir.' . $i, $row['tempat_lahir'])" />
                            <x-editable-cell :idx="$i" name="tgl_lahir" :value="old('tgl_lahir.' . $i, $row['tgl_lahir'])" />
                            <x-editable-cell :idx="$i" name="tipe_pegawai" :value="old('tipe_pegawai.' . $i, $row['tipe_pegawai'])" />
                            <x-editable-cell :idx="$i" name="status_kepegawaian" :value="old('status_kepegawaian.' . $i, $row['status_kepegawaian'])" />
                            <x-editable-cell :idx="$i" name="nip" :value="old('nip.' . $i, $row['nip'])" />
                            <x-editable-cell :idx="$i" name="tmt_mulai" :value="old('tmt_mulai.' . $i, $row['tmt_mulai'])" />
                            <x-editable-cell :idx="$i" name="jabatan" :value="old('jabatan.' . $i, $row['jabatan'])" />

                            {{-- EMERGENCY CONTACT 1 --}}
                            <x-editable-cell :idx="$i" editable="false" name="action1" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(1,'{{ $save_temp }}','{{ $i }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 1 milik {{ $save_temp }}" />
                            {{-- {{ dd($row['ec1']) }} --}}
                            {{-- {{ dd($row['ec1']==true) }} --}}
                            <x-editable-cell :idx="$i" name="ec1_status_hubungan"
                                IsEcTrue="{{ $row['ec1'] == true ? 'true' : 'false' }}" :value="old('ec1_status_hubungan.' . $i, $row['ec1_status_hubungan'])" />
                            <x-editable-cell :idx="$i" name="ec1_nama_lengkap"
                                IsEcTrue="{{ $row['ec1'] == true ? 'true' : 'false' }}" :value="old('ec1_nama_lengkap.' . $i, $row['ec1_nama_lengkap'])" />
                            <x-editable-cell :idx="$i" name="ec1_telepon"
                                IsEcTrue="{{ $row['ec1'] == true ? 'true' : 'false' }}" :value="old('ec1_telepon.' . $i, $row['ec1_telepon'])" />
                            <x-editable-cell :idx="$i" name="ec1_email"
                                IsEcTrue="{{ $row['ec1'] == true ? 'true' : 'false' }}" :value="old('ec1_email.' . $i, $row['ec1_email'])" />
                            <x-editable-cell :idx="$i" name="ec1_alamat"
                                IsEcTrue="{{ $row['ec1'] == true ? 'true' : 'false' }}" :value="old('ec1_alamat.' . $i, $row['ec1_alamat'])" />

                            {{-- EMERGENCY CONTACT 2 --}}
                            <x-editable-cell :idx="$i" editable="false" name="action2" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(2,'{{ $save_temp }}','{{ $i }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 2 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$i" name="ec2_status_hubungan"
                                IsEcTrue="{{ $row['ec2'] == true ? 'true' : 'false' }}" :value="old('ec2_status_hubungan.' . $i, $row['ec2_status_hubungan'])" />
                            <x-editable-cell :idx="$i" name="ec2_nama_lengkap"
                                IsEcTrue="{{ $row['ec2'] == true ? 'true' : 'false' }}" :value="old('ec2_nama_lengkap.' . $i, $row['ec2_nama_lengkap'])" />
                            <x-editable-cell :idx="$i" name="ec2_telepon"
                                IsEcTrue="{{ $row['ec2'] == true ? 'true' : 'false' }}" :value="old('ec2_telepon.' . $i, $row['ec2_telepon'])" />
                            <x-editable-cell :idx="$i" name="ec2_email"
                                IsEcTrue="{{ $row['ec2'] == true ? 'true' : 'false' }}" :value="old('ec2_email.' . $i, $row['ec2_email'])" />
                            <x-editable-cell :idx="$i" name="ec2_alamat"
                                IsEcTrue="{{ $row['ec2'] == true ? 'true' : 'false' }}" :value="old('ec2_alamat.' . $i, $row['ec2_alamat'])" />

                            {{-- EMERGENCY CONTACT 3 --}}
                            <x-editable-cell :idx="$i" editable="false" name="action3" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(3,'{{ $save_temp }}','{{ $i }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 3 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$i" name="ec3_status_hubungan"
                                IsEcTrue="{{ $row['ec3'] == true ? 'true' : 'false' }}" :value="old('ec3_status_hubungan.' . $i, $row['ec3_status_hubungan'])" />
                            <x-editable-cell :idx="$i" name="ec3_nama_lengkap"
                                IsEcTrue="{{ $row['ec3'] == true ? 'true' : 'false' }}" :value="old('ec3_nama_lengkap.' . $i, $row['ec3_nama_lengkap'])" />
                            <x-editable-cell :idx="$i" name="ec3_telepon"
                                IsEcTrue="{{ $row['ec3'] == true ? 'true' : 'false' }}" :value="old('ec3_telepon.' . $i, $row['ec3_telepon'])" />
                            <x-editable-cell :idx="$i" name="ec3_email"
                                IsEcTrue="{{ $row['ec3'] == true ? 'true' : 'false' }}" :value="old('ec3_email.' . $i, $row['ec3_email'])" />
                            <x-editable-cell :idx="$i" name="ec3_alamat"
                                IsEcTrue="{{ $row['ec3'] == true ? 'true' : 'false' }}" :value="old('ec3_alamat.' . $i, $row['ec3_alamat'])" />

                            {{-- EMERGENCY CONTACT 4 --}}
                            <x-editable-cell :idx="$i" editable="false" name="action4" isNeed="false"
                                :has_Special_Button='true'
                                onclick="konfirmasiAksi(4,'{{ $save_temp }}','{{ $i }}',this)"
                                caption_special_button="Hapus semua data Emergency Contact 4 milik {{ $save_temp }}" />
                            <x-editable-cell :idx="$i" name="ec4_status_hubungan"
                                IsEcTrue="{{ $row['ec4'] == true ? 'true' : 'false' }}" :value="old('ec4_status_hubungan.' . $i, $row['ec4_status_hubungan'])" />
                            <x-editable-cell :idx="$i" name="ec4_nama_lengkap"
                                IsEcTrue="{{ $row['ec4'] == true ? 'true' : 'false' }}" :value="old('ec4_nama_lengkap.' . $i, $row['ec4_nama_lengkap'])" />
                            <x-editable-cell :idx="$i" name="ec4_telepon"
                                IsEcTrue="{{ $row['ec4'] == true ? 'true' : 'false' }}" :value="old('ec4_telepon.' . $i, $row['ec4_telepon'])" />
                            <x-editable-cell :idx="$i" name="ec4_email"
                                IsEcTrue="{{ $row['ec4'] == true ? 'true' : 'false' }}" :value="old('ec4_email.' . $i, $row['ec4_email'])" />
                            <x-editable-cell :idx="$i" name="ec4_alamat"
                                IsEcTrue="{{ $row['ec4'] == true ? 'true' : 'false' }}" :value="old('ec4_alamat.' . $i, $row['ec4_alamat'])" />

                        </x-tb-cl>
                    @empty
                        <p>No Data</p>
                    @endforelse
                @endif
            </x-slot:table_column>
        </x-tb>
        <p class="mt-2 text-xs text-gray-500">
            Pastikan semua data tidak berwarna merah sebelum mengimpor.
        </p>
        <button type="button" id="import-data-btn" onclick="formSaveData(this,event)"
            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-3
                text-sm font-semibold text-white shadow-sm transition
                hover:bg-emerald-700 focus:outline-none focus:ring-2
                focus:ring-emerald-500 focus:ring-offset-2">
            <!-- save icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4zm-5 16a3 3 0 1 1 0-6 3 3 0 0 1 0 6zM6 5h9v4H6V5z" />
            </svg>
            Import Data Sekarang atau Tekan F2
        </button>
        {{-- @if (session('error') || $errors->any())
        {{ dd(session('error'),$errors->any()) }}
        @endif --}}
        <input type="hidden" class="data-changes-input" name="DataChanges" value="false">
    </form>

    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    @if (session()->has('message'))
        <script>
            showValidationAlert();

            function showValidationAlert() {
                Swal.fire({
                    title: '<strong>Silahkan Cek Kembali Data Anda</strong>',
                    icon: 'info',
                    html: `
                        <div style="text-align: left; font-size: 0.95rem; line-height: 1.6;">
                            <p>Ini adalah validasi untuk yang terakhir kalinya. Pastikan semua data sudah benar sebelum diimpor.</p>
                            
                            <hr style="margin: 15px 0; border-top: 1px solid #eee;">
                            
                            <ul style="list-style-type: none; padding-left: 0;">
                                <li style="margin-bottom: 10px;">
                                    <span style="color: #d33; font-weight: bold;">●</span> 
                                    Isi data yang masih <b>Kosong</b> (ditandai baris berwarna <span style="background-color: #ffebee; color: #d33; padding: 2px 5px; border-radius: 4px;">merah</span>).
                                </li>
                                <li style="margin-bottom: 10px;">
                                    <span style="color: #3085d6; font-weight: bold;">●</span> 
                                    <b>Cara Edit:</b> Cukup klik pada data yang ingin diperbaiki.
                                </li>
                                <li style="margin-bottom: 10px;">
                                    <span style="color: #28a745; font-weight: bold;">●</span> 
                                    Jika sudah lengkap, scroll ke <b>bagian paling bawah</b> dan klik tombol <b>Import Data Sekarang</b>.
                                </li>
                            </ul>
                        </div>
                    `,
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    width: '600px' // Diperlebar sedikit agar teks instruksi tidak terlalu padat
                });
            }
        </script>
    @endif



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



    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === "F2" || e.keyCode === 114) {
                e.preventDefault(); // cegah fungsi default (kalau ada)
                document.getElementById('import-data-btn').click();
            }
        });
    </script>

    <script></script>

    <script>
        function cekEMergencyContact() {
            all_editable_cell = document.querySelectorAll('editable-cell');
            // all_editable_cell
        }

        function konfirmasiAksi(whichEc, owner, idx, elemen) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Data Emergency ${whichEc} ${owner} yang dihapus tidak bisa dikembalikan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lakukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    let parent = elemen.closest(`.x-tb-cl[data-index='${idx}']`);

                    let status = parent.querySelector(
                        `.editable-cell[data-name='ec${whichEc}_status_hubungan'] input`);
                    status.value = null;
                    ParentTurnToNull(status)

                    let telepon = parent.querySelector(`.editable-cell[data-name='ec${whichEc}_telepon'] input`);
                    telepon.value = null;
                    ParentTurnToNull(telepon)

                    let nama = parent.querySelector(`.editable-cell[data-name='ec${whichEc}_nama_lengkap'] input`);
                    nama.value = null;
                    ParentTurnToNull(nama)

                    let email = parent.querySelector(`.editable-cell[data-name='ec${whichEc}_email'] input`);
                    email.value = null;
                    ParentTurnToNull(email)

                    let alamat = parent.querySelector(`.editable-cell[data-name='ec${whichEc}_alamat'] input`);
                    alamat.value = null;
                    ParentTurnToNull(alamat)



                    Swal.fire(
                        'Berhasil!',
                        'Tindakan berhasil dilakukan.',
                        'success'
                    )
                }
            })
        }


        function deleteAll(owner, idx) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Semua Data ${owner} yang dihapus tidak bisa dikembalikan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lakukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let toDelete = document.querySelector(`.x-tb-cl[data-index='${idx}']`);
                    if (toDelete.remove()) {
                        Swal.fire(
                            'Berhasil!',
                            'Tindakan berhasil dilakukan.',
                            'success'
                        )
                    }

                }
            })
        }

        function ParentTurnToNull(elemen) {
            let parent = elemen.closest('.editable-cell');
            parent.setAttribute('data-value', '');
            parent.querySelector('span').textContent = "";
        }
    </script>
    <script>
        let detectChanges = "false";
        // ====== GUARD: Konfirmasi keluar sebelum submit ======
        let hasUnsavedChanges = false;
        let isSubmitting = false;

        function markDirty() {
            console.log('load dirty');

            hasUnsavedChanges = true;
        }

        const form = document.querySelector('#formSaveData');
        if (form) {
            form.addEventListener('submit', () => {
                isSubmitting = true;
                hasUnsavedChanges = false;
            });
        }

        async function confirmLeave() {
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Keluar dari halaman ini?',
                html: `
                <div style="text-align:left">
                    <p>Perubahan Anda <b>belum disimpan</b> karena data ini masih <b>pratinjau (preview)</b> dan belum diimpor.</p>
                    <p class="mt-2">Jika Anda keluar sekarang, Anda akan <b>kehilangan perubahan</b> yang belum disubmit.</p>
                    <p class="mt-2"><b>Apakah Anda yakin ingin keluar?</b></p>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                allowOutsideClick: false
            });
            return result.isConfirmed;
        }

        // intercept klik link keluar halaman
        document.addEventListener('click', async (e) => {
            const a = e.target.closest('a');
            if (!a) return;

            const href = a.getAttribute('href') || '';
            const target = a.getAttribute('target') || '';

            if (href.startsWith('#') || target === '_blank') return;
            if (!hasUnsavedChanges || isSubmitting) return;

            e.preventDefault();
            const ok = await confirmLeave();
            if (ok) window.location.href = href;
        });

        // intercept back/forward
        history.pushState({
            guard: true
        }, '', location.href);
        window.addEventListener('popstate', async () => {
            if (!hasUnsavedChanges || isSubmitting) return;

            history.pushState({
                guard: true
            }, '', location.href);
            const ok = await confirmLeave();
            if (ok) {
                hasUnsavedChanges = false;
                history.back();
            }
        });

        // intercept refresh/close tab
        window.addEventListener('beforeunload', (e) => {
            if (!hasUnsavedChanges || isSubmitting) return;
            e.preventDefault();
            e.returnValue = '';
        });

        // ====== HELPERS: format tanggal dd/mm/yyyy <-> yyyy-mm-dd (untuk input type=date) ======
        function ddmmyyyyToISO(v) {
            // "13/11/2025" -> "2025-11-13"
            if (!v) return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(v)) return v; // sudah ISO
            const m = v.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
            if (!m) return '';
            const dd = m[1].padStart(2, '0');
            const mm = m[2].padStart(2, '0');
            const yyyy = m[3];
            return `${yyyy}-${mm}-${dd}`;
        }

        function isoToDDMMYYYY(v) {
            // "2025-11-13" -> "13/11/2025"
            if (!v) return '';
            const m = v.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (!m) return v;
            return `${m[3]}/${m[2]}/${m[1]}`;
        }

        // ====== EDITABLE CELL: SweetAlert dinamis ======
        document.addEventListener('click', async (e) => {
            const el = e.target.closest('.editable-cell');
            if (!el) return;

            const field = (el.dataset.name || '').trim();
            const currentRaw = (el.dataset.value ?? '').toString();

            // default config (text)
            let swalConfig = {
                title: 'Edit Data',
                input: 'text',
                inputLabel: field,
                inputValue: currentRaw,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            };

            // 1) Jenis Kelamin (dropdown)
            if (field === 'jenis_kelamin') {
                swalConfig = {
                    ...swalConfig,
                    input: 'select',
                    inputOptions: {
                        'Laki-laki': 'Laki-laki',
                        'Perempuan': 'Perempuan',
                    },
                    inputValue: currentRaw || '',
                    inputPlaceholder: '-- pilih --',
                };
            }

            // 2) Tanggal Lahir & Tanggal Berlaku NIP (date)
            if (field === 'tgl_lahir' || field === 'tmt_mulai' || field === 'tanggal_berlaku_nip') {
                swalConfig = {
                    ...swalConfig,
                    input: 'date',
                    // SweetAlert pakai value ISO (yyyy-mm-dd)
                    inputValue: ddmmyyyyToISO(currentRaw),
                };
            }

            // 3) Tipe Pegawai (dropdown)
            if (field === 'tipe_pegawai') {
                swalConfig = {
                    ...swalConfig,
                    input: 'select',
                    inputOptions: {
                        'TPA': 'TPA',
                        'Dosen': 'Dosen',
                    },
                    inputValue: currentRaw || '',
                    inputPlaceholder: '-- pilih --',
                };
            }
            let statusKepegawaianOptions = {!! json_encode($refStatusKepegawaian) !!};
            let jabatanOption = {!! json_encode($refFormasi) !!};
            // Let Jabatan = {{ $refStatusKepegawaian }}


            // 4) Status Kepegawaian (dropdown dari $refStatusKepegawaian)
            if (field === 'status_kepegawaian') {
                swalConfig = {
                    ...swalConfig,
                    input: 'select',
                    inputOptions: statusKepegawaianOptions, // {id: 'TENAGA LEPAS HARIAN', ...}
                    inputValue: currentRaw ||
                        '', // pastikan value ini cocok (id). kalau kamu simpan teks, bilang ya nanti aku ubah.
                    inputPlaceholder: '-- pilih --',
                };
            }


            if (field === 'jabatan') {
                swalConfig = {
                    ...swalConfig,
                    input: 'select',
                    inputOptions: jabatanOption, // {id: 'TENAGA LEPAS HARIAN', ...}
                    inputValue: currentRaw ||
                        '', // pastikan value ini cocok (id). kalau kamu simpan teks, bilang ya nanti aku ubah.
                    inputPlaceholder: '-- pilih --',
                };
            }

            const result = await Swal.fire(swalConfig);
            if (!result.isConfirmed) return;

            let finalValue = (result.value ?? '').toString();

            // jika date: simpan balik ke format dd/mm/yyyy (biar sesuai label tabel kamu)
            if (field === 'tgl_lahir' || field === 'tmt_mulai' || field === 'tanggal_berlaku_nip') {
                finalValue = isoToDDMMYYYY(finalValue);
            }

            // update dataset
            el.dataset.value = finalValue;

            // update teks tampilan
            const span = el.querySelector('span');
            if (span) {
                if (finalValue === '') {
                    span.textContent = 'PERLU DIISI';
                    span.classList.add('text-red-500');
                } else {
                    span.textContent = finalValue;
                    span.classList.remove('text-red-500');
                }
            }

            // update hidden input
            const hidden = el.querySelector('input[type="hidden"]');
            if (hidden) hidden.value = finalValue;

            // tandai dirty
            console.log('load proses');
            detectChanges = "true";
            let remove = el.querySelector('.eror_caution')
            if (remove) {
                el.querySelector('span').classList.remove('text-red-500');
                el.classList.remove('bg-red-50', 'ring-1', 'ring-red-400');
                remove.remove()
            }
            markDirty();
        });

        // tombol submit form
        function formSaveData(elemen, event) {

            form_loading(elemen)
            event.preventDefault();
            document.querySelector('.data-changes-input').value = detectChanges;
            document.querySelector('#formSaveData')?.submit();
        }

        function form_loading(elemen) {
            console.log(elemen.checkValidity())
            if (!elemen.closest('form').checkValidity()) {
                console.log('masuk', 'cek')
                Pop_message('Validasi Data', 'Silakan periksa kembali dan lengkapi semua field yang bertanda *.', false,
                    'warning');
                return;
            } {
                console.log('masuk', 'proses')
                Pop_message('Mohon Tunggu....', 'Sedang melakukan validasi data', true);
            }

        }
    </script>

    <script>
        let allInputEditableCell = document.querySelectorAll('.editable-cell input');
        allInputEditableCell.forEach(element => {
            element.addEventListener('input', function(event) {
                // 4. Gunakan event.target untuk merujuk ke input yang sedang diketik
                let value = event.target.value;
                let cell = event.target.closest('.editable-cell');

                // 5. Update teks pada span
                cell.querySelector('span').textContent = value;
            });
        });
    </script>
@endsection
