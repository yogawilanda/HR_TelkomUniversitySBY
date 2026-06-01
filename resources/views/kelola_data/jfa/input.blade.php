@php
    $active_sidebar = 'Tambah JFA';
@endphp
@extends('kelola_data.base')

@section('header-base')
    <style>
        .max-w-100 {
            max-width: 100% !important;
        }

        .nav-active {
            background-color: #0070ff;

            span {
                color: white;
            }
        }

        .input-sk-toggle button.active {
            background-color: #f3f4f6;
            border-bottom: 2px solid #0070ff;
            font-weight: 600;
        }
    </style>
@endsection

@section('page-name')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">
                    Tambah Data JFA Dosen
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.jfa.store') }}" id="form-jfa" enctype="multipart/form-data">
        <div class="grid gap-8">
            <div class="flex flex-col gap-6">

                {{-- Data Utama --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-islc lbl="Dosen" nm="dosen_id">
                        <option value="" disabled selected>-- Pilih Dosen --</option>
                        @forelse ($dosens as $dosen)
                            <option value="{{ $dosen->id }}"
                                {{ old('dosen_id', request('dosen_id')) == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->pegawai->nama_lengkap }}</option>
                        @empty
                            <option value="" disabled selected>Tidak ada dosen terdaftar</option>
                        @endforelse
                    </x-islc>

                    <x-islc lbl="Jabatan Fungsional (JFA)" nm="ref_jfa_id">
                        <option value="" disabled selected>-- Pilih JFA --</option>
                        @forelse ($jfas as $jfa)
                            <option value="{{ $jfa->id }}"
                                {{ old('ref_jfa_id', request('ref_jfa_id')) == $jfa->id ? 'selected' : '' }}>
                                {{ $jfa->nama_jabatan }}</option>
                        @empty
                            <option value="" disabled selected>Tidak ada JFA terdaftar</option>
                        @endforelse
                    </x-islc>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-itxt lbl="TMT Mulai" type="date" nm='tmt_mulai'></x-itxt>
                    <x-itxt lbl="TMT Selesai" type="date" nm='tmt_selesai' :req=false></x-itxt>
                </div>

                <hr class="my-2">

                {{-- SECTION 1: SK LLDIKTI (WAJIB) --}}
                <div class="space-y-2 ">
                    <label class="block text-sm font-semibold text-gray-700">Dokumen SK LLDIKTI <span
                            class="text-red-500">*</span></label>
                    <div class="w-full border bg-gray-200 border-gray-300 rounded-lg p-4 gap-4 flex flex-col">
                        <div class="flex flex-row border-b-2 gap-0 justify-between input-sk-toggle">
                            <button type="button"
                                class="flex flex-grow justify-center items-center py-2 rounded-t-lg active"
                                id="btn-lldikti-baru">
                                Input SK Baru
                            </button>
                            <button type="button" class="flex flex-grow justify-center items-center py-2 rounded-t-lg"
                                id="btn-lldikti-existing">
                                Pilih SK Terdaftar
                            </button>
                        </div>

                        <div id="section-lldikti-baru" class="space-y-4">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                        <!-- Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-amber-800">
                                            Perhatian
                                        </h3>

                                        <p class="mt-1 text-sm leading-relaxed text-amber-700">
                                            Pastikan seluruh kolom pada form ini dalam keadaan kosong sebelum memilih
                                            <span class="font-semibold">SK Yang Sudah Terdaftar</span>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <x-itxt lbl="Upload File SK LLDIKTI" type="file" nm='file_sk_lldikti' :req=false></x-itxt>
                            <x-itxt lbl="Nomor SK LLDIKTI" nm='no_sk_lldikti' :req=false max="49"></x-itxt>
                            <x-itxt lbl="Keterangan SK" plc="Nomor SK" nm='keterangan_sk_lldikti' max="200"
                                :req=false></x-itxt>
                            <x-islc lbl="Tipe Dokumen" nm='tipe_dokumen_sk_lldikti' class="flex-1" :req=false>
                                <option value="" selected>-- Pilih TIPE --</option>
                                <option value="SK" {{ old('tipe_dokumen_sk_lldikti') == 'SK' ? 'selected' : '' }}> SK
                                </option>
                                <option value="AMANDEMEN"
                                    {{ old('tipe_dokumen_sk_lldikti') == 'AMANDEMEN' ? 'selected' : '' }}> AMANDEMEN
                                </option>
                            </x-islc>
                        </div>

                        <div id="section-lldikti-existing" class="hidden">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-red-800">
                                            Perhatian
                                        </h3>

                                        <p class="mt-1 text-sm leading-relaxed text-red-700">
                                            Pastikan semua field pada Input SK kosong sebelum memilih
                                            <span class="font-semibold">SK yang sudah terdaftar</span>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <x-islc lbl="Pilih SK LLKDIKTI" nm='sk_llkdikti_id' :req=false>
                                <option value="" disabled selected>-- Pilih Data SK --</option>
                                @forelse ($sk_diktis as $sk)
                                    <option value="{{ $sk->id }}"
                                        {{ old('sk_llkdikti_id', request('sk_llkdikti_id')) == $sk->id ? 'selected' : '' }}>
                                        {{ $sk->no_sk }}</option>
                                @empty
                                    <option value="" disabled>Tidak ada SK LLKDIKTI terdaftar</option>
                                @endforelse
                            </x-islc>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: SK PENGAKUAN YPT (OPSIONAL) --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">SK Pengakuan YPT (Bisa Kosong)</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 gap-4 flex flex-col">
                        <div class="flex flex-row border-b-2 gap-0 justify-between input-sk-toggle">
                            <button type="button"
                                class="flex flex-grow justify-center items-center py-2 rounded-t-lg active"
                                id="btn-ypt-baru">
                                Input SK Baru
                            </button>
                            <button type="button" class="flex flex-grow justify-center items-center py-2 rounded-t-lg"
                                id="btn-ypt-existing">
                                Pilih SK Terdaftar
                            </button>
                        </div>

                        <div id="section-ypt-baru" class="space-y-4">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                        <!-- Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-amber-800">
                                            Perhatian
                                        </h3>

                                        <p class="mt-1 text-sm leading-relaxed text-amber-700">
                                            Pastikan seluruh kolom pada form ini dalam keadaan kosong sebelum memilih
                                            <span class="font-semibold">SK Yang Sudah Terdaftar</span>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <x-itxt lbl="Upload File SK YPT" type="file" nm='file_sk_ypt' :req=false></x-itxt>
                            <x-itxt lbl="Nomor SK YPT" nm='no_sk_ypt' :req=false max="49"></x-itxt>
                            <x-itxt lbl="Keterangan SK" plc="Nomor SK" nm='keterangan_sk_ypt' max="200"
                                :req=false></x-itxt>
                            <x-islc lbl="Tipe Dokumen" nm='tipe_dokumen_sk_ypt' class="flex-1" :req=false>
                                <option value="" selected>-- Pilih TIPE --</option>
                                <option value="SK" {{ old('tipe_dokumen_sk_ypt') == 'SK' ? 'selected' : '' }}> SK
                                </option>
                                <option value="AMANDEMEN"
                                    {{ old('tipe_dokumen_sk_ypt') == 'AMANDEMEN' ? 'selected' : '' }}> AMANDEMEN </option>
                            </x-islc>
                        </div>

                        <div id="section-ypt-existing" class="hidden">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-red-800">
                                            Perhatian
                                        </h3>

                                        <p class="mt-1 text-sm leading-relaxed text-red-700">
                                            Pastikan semua field pada Input SK kosong sebelum memilih
                                            <span class="font-semibold">SK yang sudah terdaftar</span>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <x-islc lbl="Pilih SK Pengakuan YPT" nm='sk_pengakuan_ypt_id' :req=false>
                                <option value="" disbaled selected>-- Pilih Data SK --</option>
                                @forelse ($sk_ypts as $sk)
                                    <option value="{{ $sk->id }}"
                                        {{ old('sk_pengakuan_ypt_id', request('sk_pengakuan_ypt_id')) == $sk->id ? 'selected' : '' }}>
                                        {{ $sk->no_sk }}</option>
                                @empty
                                    <option value="" disabled>Tidak ada SK YPT terdaftar</option>
                                @endforelse
                            </x-islc>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </x-form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper function untuk handle toggle
            function setupToggle(btnBaruId, btnExistId, sectionBaruId, sectionExistId) {
                const btnBaru = document.getElementById(btnBaruId);
                const btnExist = document.getElementById(btnExistId);
                const sectionBaru = document.getElementById(sectionBaruId);
                const sectionExist = document.getElementById(sectionExistId);

                btnBaru.addEventListener('click', () => {
                    btnBaru.classList.add('active');
                    btnExist.classList.remove('active');
                    sectionBaru.classList.remove('hidden');
                    sectionExist.classList.add('hidden');

                });

                btnExist.addEventListener('click', () => {
                    btnExist.classList.add('active');
                    btnBaru.classList.remove('active');
                    sectionExist.classList.remove('hidden');
                    sectionBaru.classList.add('hidden');
                });
            }

            // Inisialisasi Toggle untuk LLDIKTI
            setupToggle('btn-lldikti-baru', 'btn-lldikti-existing', 'section-lldikti-baru',
                'section-lldikti-existing');

            // Inisialisasi Toggle untuk YPT
            setupToggle('btn-ypt-baru', 'btn-ypt-existing', 'section-ypt-baru', 'section-ypt-existing');
        });
    </script>
@endsection
