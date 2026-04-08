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
                    Tambah Pegawai Baru
                </span>
            </div>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
            {{-- <x-export-csv-tb target_id="pegawaiTable"></x-export-csv-tb> --}}
        </div>
    </div>
@endsection

@section('content-base')
    <x-form route="{{ route('manage.pegawai.create') }}" id="pegawai-input">

        {{-- Data Diri --}}
        <div class="flex flex-col gap-8 w-full max-w-100 mx-auto rounded-md border p-3">
            <h2 class="text-lg font-semibold text-black text-center">Data Diri Pegawai</h2>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- Kolom Kiri --}}
                <div class="flex flex-col gap-4">
                    <x-itxt lbl="Nama Lengkap" plc="John Doe" nm="nama_lengkap" max="100"></x-itxt>

                    <x-itxt lbl="Username" plc="johndoe" nm="username" max="20"></x-itxt>

                    <x-itxt lbl="Telepon" plc="081234567890" nm="telepon" max="13" :rules="['Harus dimulai dengan 0', 'berjumlah 10-13 digit']"></x-itxt>

                    <x-itxt lbl="No Telepon Darurat" plc="081234567890" nm="emergency_contact_phone" max="13"
                        :rules="['Harus dimulai dengan 0', 'berjumlah 10-13 digit']" :required="false"></x-itxt>

                    <x-itxt type="textarea" lbl="Alamat" plc="Jl. Telekomunikasi No. 1, Bandung" nm="alamat"
                        max="300" fill="flex-grow"></x-itxt>
                </div>

                {{-- Kolom Kanan --}}
                <div class="flex flex-col gap-4">
                    <x-itxt lbl="Nomor Induk Kependudukan (NIK)" plc="3568165xxxxxxxxx" nm="nik"
                        max="20"></x-itxt>

                    <x-itxt type="email" lbl="Email Pribadi" plc="johndoe@gmail.com" nm="email_pribadi"
                        max="150"></x-itxt>

                    <x-itxt type="email" lbl="Email Institusi" plc="john.doe@telkomuniversity.ac.id" nm="email_institusi"
                        max="150"></x-itxt>

                    <x-islc lbl="Jenis Kelamin" nm="jenis_kelamin">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </x-islc>

                    <div class="flex flex-col xl:flex-row justify-between w-full gap-3">
                        <x-itxt lbl="Tempat Lahir" fill="flex-1" plc="Surabaya" nm="tempat_lahir"></x-itxt>
                        <x-itxt type="date" fill="flex-1" lbl="Tanggal Lahir" nm="tgl_lahir" max="2025-10-27"
                            rules="none"></x-itxt>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row justify-between items-start gap-8 w-full max-w-100 md:mx-auto">
            {{-- Selector Tipe Pegawai (SELALU TAMPIL) --}}
            <div class="flex flex-col gap-8 flex-1 flex-grow w-full md:mx-auto rounded-md border p-3">

                {{-- <div class="w-full max-w-100 md:mx-auto rounded-md border p-3 my-8"> --}}
                <h2 class="text-lg font-semibold text-black text-center">Data Kepegawaian</h2>

                @php
                    $selectedType = old('tipe_pegawai', request('type') ?? 'Dosen');
                @endphp
                <x-islc lbl="Tipe Pegawai" nm="tipe_pegawai" id="tipe_pegawai" required>
                    <option value="" disabled>-- Pilih Tipe --</option>
                    <option value="Dosen" {{ $selectedType === 'Dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="TPA" {{ $selectedType === 'Tpa' ? 'selected' : '' }}>TPA</option>
                </x-islc>

                <x-islc lbl="Status Kepegawaian" nm="status_kepegawaian">
                    {{-- {{ dd($status_pegawai_options) }} --}}
                    @foreach ($status_pegawai_options as $status)
                        <option value="{{ (string) data_get($status, 'id') }}"
                            class="opsi-kepegawaian {{ $status->tipe_pegawai }}">
                            {{ $status->status_pegawai }}</option>
                    @endforeach
                </x-islc>

                <x-itxt lbl="Nomor Induk Pegawai" plc="1234567890" nm="nip" max="30" :req=false></x-itxt>

                <x-itxt type="date" lbl="Tanggal Berlaku NIP" plc="dd/mm/yyyy" nm="tmt_mulai" max="none"
                    :rules="['Silahkan masukkan tanggal pertama karyawan aktif bekerja.']"></x-itxt>

            </div>
        </div>

        <div id="emergency-contacts" class="flex flex-col gap-6 border p-3">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold">Emergency Contact</h3>
                <button type="button" id="add-contact" class="px-3 py-1 border rounded text-sm">+ Tambah</button>
            </div>


            <div id="contacts-container" class=""></div>
        </div>
    </x-form>
@endsection

@section('script-base')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('contacts-container');
            const addBtn = document.getElementById('add-contact');
            let index = 0;

            function contactTemplate(i) {
                return `
                        <div class="rounded-md border p-3 mt-4 space-y-4" data-index="${i}">
                            <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Kontak #${i + 1}</span>
                            <button type="button" class="text-red-600 text-xs remove-contact">Hapus</button>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                            <label>
                                <span>Nama Lengkap</span>
                                <input type="text" name="emergency_contacts[${i}][nama_lengkap]" placeholder="Jane Doe" maxlength="100" class="border bg-gray-100 p-2 rounded w-full">
                            </label>

                            <label>
                                <span>Status Hubungan</span>
                                <select name="emergency_contacts[${i}][status_hubungan]" class="border bg-gray-100 p-2 rounded w-full">
                                <option value="">-- Pilih --</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Suami/Istri">Suami/Istri</option>
                                <option value="Saudara">Saudara</option>
                                <option value="Teman">Teman</option>
                                <option value="Lainnya">Lainnya</option>
                                </select>
                            </label>

                            <label>
                                <span>Telepon</span>
                                <input type="text" name="emergency_contacts[${i}][telepon]" placeholder="081234567890" maxlength="13" class="border bg-gray-100 p-2 rounded w-full">
                            </label>

                            <label>
                                <span>Email</span>
                                <input type="email" name="emergency_contacts[${i}][email]" placeholder="jane.doe@gmail.com" maxlength="150" class="border bg-gray-100 p-2 rounded w-full">
                            </label>

                            <label class="md:col-span-2">
                                <span>Alamat</span>
                                <textarea name="emergency_contacts[${i}][alamat]" placeholder="Jl. Telekomunikasi No. 1, Bandung" maxlength="300" class="border bg-gray-100 p-2 rounded w-full"></textarea>
                            </label>
                            </div>
                        </div>
                `;
            }

            function addContact() {
                container.insertAdjacentHTML('beforeend', contactTemplate(index));
                index++;
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                const removeButtons = container.querySelectorAll('.remove-contact');
                removeButtons.forEach(btn => {
                    btn.onclick = function() {
                        const block = this.closest('[data-index]');
                        block.remove();
                        renumberContacts();
                    };
                });
            }

            function renumberContacts() {
                const blocks = container.querySelectorAll('[data-index]');
                blocks.forEach((block, i) => {
                    block.setAttribute('data-index', i);
                    block.querySelector('span.text-sm').textContent = `Kontak #${i + 1}`;
                    block.querySelectorAll('input, select, textarea').forEach(input => {
                        input.name = input.name.replace(/\[\d+\]/, `[${i}]`);
                    });
                });
                index = blocks.length;
            }

            addBtn.addEventListener('click', addContact);

            // default satu kontak
            addContact();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipePegawai = document.querySelector('select[name="tipe_pegawai"]') || document.getElementById(
                'tipe_pegawai');
            const statusKepegawaian = document.querySelector('select[name="status_kepegawaian"]');
            const dataTPA = document.getElementById('data-tpa');
            const dataDosen = document.getElementById('data-dosen');

            if (!tipePegawai || !statusKepegawaian || !dataTPA || !dataDosen) {
                console.warn('Elemen penting tidak ditemukan.');
                return;
            }



            function setSectionRequired(sectionEl, isRequired) {
                if (!sectionEl) return;
                const fields = sectionEl.querySelectorAll('input:not([type="hidden"]), select, textarea');
                fields.forEach(el => {
                    if (isRequired) {
                        el.setAttribute('required', 'required');
                        el.setAttribute('aria-required', 'true');
                    } else {
                        el.removeAttribute('required');
                        el.removeAttribute('aria-required');
                    }
                });
            }

            function showHideByType(type) {
                if (type === 'Dosen') {
                    dataDosen.classList.remove('hidden');
                    dataTPA.classList.add('hidden');
                    setSectionRequired(dataDosen, true);
                    setSectionRequired(dataTPA, false);
                } else if (type === 'TPA') {
                    dataTPA.classList.remove('hidden');
                    dataDosen.classList.add('hidden');
                    setSectionRequired(dataTPA, true);
                    setSectionRequired(dataDosen, false);
                } else {
                    dataTPA.classList.add('hidden');
                    dataDosen.classList.add('hidden');
                    setSectionRequired(dataTPA, false);
                    setSectionRequired(dataDosen, false);
                }
            }

            // ⬇️ FILTER TANPA MENGUBAH VALUE TERPILIH
            function filterStatusOptions(type) {
                statusOptions.forEach(({
                    el,
                    classes
                }) => {
                    // Biarkan placeholder (yang disabled) tetap terlihat
                    const isPlaceholder = el.disabled && el.value === '';
                    if (isPlaceholder) {
                        el.hidden = false;
                        return;
                    }

                    // Tampilkan hanya opsi yang mengandung kelas tipe (Dosen/TPA)
                    const classList = (classes || '').split(/\s+/);
                    el.hidden = !classList.includes(type);
                });
                // Penting: TIDAK mengubah statusKepegawaian.value di sini
            }

            function handleTypeChange() {
                const type = tipePegawai.value;
                filterStatusOptions(type);
                showHideByType(type);
            }

            // Inisialisasi
            handleTypeChange();

            // Re-filter saat tipe pegawai berubah
            tipePegawai.addEventListener('change', handleTypeChange);
        });
        document.querySelectorAll(".tom-select").forEach(function(el) {
            if (!el.tomselect) {
                new TomSelect(el);
            }
        });
    </script>
    @if (session('error') || $errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Waduh!',
                // Cara yang benar
                html: "{!! session('error') ?? 'Ada Masalah!' !!}",
                confirmButtonText: 'Oke, Saya Cek Lagi',
                confirmButtonColor: '#d33',
            });
        </script>
    @endif
@endsection
