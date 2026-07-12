@php
        // Silakan sesuaikan nama sidebar sesuai dengan yang Anda gunakan di aplikasi
        $active_sidebar = 'Kelompok Keahlian';
    @endphp
    @extends('kelola_data.base')

    @section('page-name')
        <div
            class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
            <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
                <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                    <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">
                        Tambah Dosen ke Sub-KK
                    </span>
                </div>
            </div>
        </div>
    @endsection

    @section('content-base')
        <x-form route="{{ route('manage.kelompok-keahlian.dosen-with-kk.store') }}" id="pegawai-input">
            <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-8">
                {{-- Kolom Kiri --}}
                <div class="flex flex-col justify-start gap-4">

                    {{-- Komponen Select Nama Dosen --}}
                    <x-islc lbl="Nama Dosen" nm="dosen_id" full="false" id="select-dosen">
                        <option value="" id="placeholder-dosen" disabled selected>Pilih Dosen...</option>

                        {{-- Contoh implementasi loop jika data dikirim dari controller --}}
                        @forelse ($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->pegawai->nama_lengkap }}
                            </option>
                        @empty
                        <option value="" id="placeholder-subkk" disabled selected>Belum ada Dosen Terdaftar</option>

                        @endforelse
                    </x-islc>

                    {{-- Komponen Select Tujuan Sub-KK --}}
                    <x-islc lbl="Tujuan Sub-KK" nm="sub_kk_id" full="false" id="select-subkk">
                        <option value="" id="placeholder-subkk" disabled selected>Pilih Sub-KK...</option>

                        {{-- Contoh implementasi loop jika data dikirim dari controller --}}
                        @forelse ($sub_kk as $sub)
                            <option value="{{ $sub->id }}" {{ old('sub_kk_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->nama.' - '.$sub->KK->kode }}
                            </option>
                        @empty
                        <option value="" id="placeholder-subkk" disabled selected>Belum ada Sub Kelompok Keahlian Terdaftar</option>
                        @endforelse
                    </x-islc>

                </div>
            </div>
        </x-form>
    @endsection
