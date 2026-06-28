    @php
        $active_sidebar = 'Tambah Formasi';
    @endphp
    @extends('kelola_data.base')

    {{-- @section('header-base')
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


        </style>
    @endsection --}}

    @section('page-name')
        <div
            class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
            <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
                <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                    <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">
                        Tambah Pangkat & Golongan Dosen
                    </span>
                </div>
            </div>
        </div>
    @endsection

    @section('content-base')
        <x-form route="{{ route('manage.pangkat-golongan.store') }}" id="pemetaan-input">
            <div class="grid lg:grid-cols-2 sm:grid-col-1 gap-8">
                {{-- Kolom Kiri --}}
                <div class="flex flex-col justify-start  gap-4">
                    <x-islc lbl="Nama Dosen" nm='dosen_id' full="false">
                        <option value="" disabled selected>-- Pilih Data --</option>
                        @forelse ($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->pegawai->nama_lengkap }}
                            </option>

                        @empty
                        @endforelse
                    </x-islc>

                    <x-islc lbl="Pangkat & Golongan" nm='pangkat_golongan_id' full="false">
                        <option value="" disabled selected>-- Pilih Data --</option>
                        @forelse ($jpgs as $jpg)
                            {{-- {{ dd($jfk->data_jfk->nama_jkf) }} --}}
                            <option value="{{ $jpg->id }}"
                                {{ old('pangkat_golongan_id') == $jpg->id ? 'selected' : '' }}>
                                {{ $jpg->pangkat . ' - ' . $jpg->golongan }}
                            </option>
                        @empty
                        @endforelse
                    </x-islc>
                    <x-itxt lbl="TMT Mulai" type="date" plc="dd/mm/yyyy" nm='tmt_mulai'></x-itxt>

                    <div class="flex flex-col gap-4 justify-end">



                        <p class="text-sm text-gray-600 font-medium ">SK YPT (Bisa Diisi Nanti)*</p>
                        <div class="sk-wrapper w-full border border-gray-300 p-4 rounded-lg shadow-sm bg-white space-y-4">

                            <!-- Tabs -->
                            <div class="flex border-b">
                                <button type="button"
                                    class="btn-tab btn-sk-baru flex-1 py-3 text-center text-sm font-medium border-b-2 border-blue-600 text-blue-600">
                                    Input SK Baru
                                </button>
                                <button type="button"
                                    class="btn-tab btn-sk-existing flex-1 py-3 text-center text-sm font-medium text-gray-600 hover:text-blue-600">
                                    Pilih SK yang Sudah Ada
                                </button>
                            </div>

                            <!-- Section SK Baru -->
                            <div class="section-sk-baru space-y-4">
                                <x-itxt lbl="SK LLKDIKTI" type="file" plc="Pilih Dokumen SK" nm='file_sk'
                                    :req=false></x-itxt>
                                <x-itxt lbl="Nomor SK" plc="Nomor SK" nm='no_sk' max="50" :req=false></x-itxt>
                                <x-islc lbl="Tipe Dokumen" nm='tipe_dokumen' class="flex-1" :req=false>
                                    <option value="" disabled selected>-- Pilih TIPE --</option>
                                    <option value="SK"> SK </option>
                                    <option value="AMANDEMEN"> AMANDEMEN </option>
                                </x-islc>
                            </div>

                            <!-- Section SK Existing -->
                            <div class="section-sk-existing hidden space-y-3">
                                <div class="flex flex-row gap-3 items-end">
                                    <x-islc lbl="Pilih SK LLKDIKTI" nm='sk_llkdikti_id' class="flex-1" :req=false>
                                        <option value="" disabled selected>-- Pilih SK LLKDIKTI --</option>
                                        @foreach ($sk_diktis as $row)
                                            <option value="{{ $row->id }}">{{ $row->no_sk }}</option>
                                        @endforeach
                                    </x-islc>

                                    <a class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                                        Lihat File
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </x-form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                document.querySelectorAll('.sk-wrapper').forEach(wrapper => {

                    const btnBaru = wrapper.querySelector('.btn-sk-baru');
                    const btnExisting = wrapper.querySelector('.btn-sk-existing');
                    const sectionBaru = wrapper.querySelector('.section-sk-baru');
                    const sectionExisting = wrapper.querySelector('.section-sk-existing');

                    if (!btnBaru || !btnExisting || !sectionBaru || !sectionExisting) return;

                    function activateTab(activeBtn, inactiveBtn, showSection, hideSection) {
                        activeBtn.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                        inactiveBtn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');

                        showSection.classList.remove('hidden');
                        hideSection.classList.add('hidden');
                    }

                    btnBaru.addEventListener('click', () => {
                        activateTab(btnBaru, btnExisting, sectionBaru, sectionExisting);
                    });

                    btnExisting.addEventListener('click', () => {
                        activateTab(btnExisting, btnBaru, sectionExisting, sectionBaru);
                    });

                    // Set default tab: SK Baru aktif
                    activateTab(btnBaru, btnExisting, sectionBaru, sectionExisting);
                });

            });
        </script>
    @endsection
