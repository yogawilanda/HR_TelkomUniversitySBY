@php
    use App\Helpers\PhoneHelper;
    $active_sidebar = 'Personal Information';
@endphp

@extends('kelola_data.base-profile')

@section('content-profile')
                                {{-- {{ dd($user) }} --}}

    <style>
        .bg-primary-bs {
            background-color: #1C2762 !important;
        }

        /* Ukuran teks disesuaikan */
        /* .profile-wrapper {
                                                                                        font-size: 16px;
                                                                                        line-height: 1.7;
                                                                                    }
                                                                                    .profile-wrapper dt {
                                                                                        font-size: 14px;
                                                                                    }
                                                                                    .profile-wrapper dd {
                                                                                        font-size: 16px;
                                                                                    }
                                                                                    .profile-wrapper h2 {
                                                                                        font-size: 20px;
                                                                                    }
                                                                                    .profile-wrapper h3 {
                                                                                        font-size: 18px;
                                                                                    } */
    </style>

    <div class="w-full max-w-full profile-wrapper">
        {{-- Content Layout --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Left column: Identity card --}}
            <section class="lg:col-span-1 min-h-full">
                <div
                    class="rounded-2xl border min-h-full border-gray-200 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white ring-2 ring-white dark:ring-gray-900">
                            <div class="flex h-full w-full items-center justify-center text-2xl font-semibold">TA</div>
                        </div>
                        <div class="min-w-0">
                            <h2 class="truncate font-semibold text-gray-900 dark:text-gray-100">
                                {{ $user['nama_lengkap'] }}</h2>
                            <p class="truncate text-gray-500 dark:text-gray-400">Tirex Alfred, S.T., M.T.</p>
                        </div>
                    </div>

                    <dl class="mt-8 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Username</dt>
                            <dd class="truncate font-medium text-gray-900 dark:text-gray-100">{{ $user['username'] }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Password</dt>
                            <dd class="truncate font-medium text-gray-900 dark:text-gray-100">********</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">NIK</dt>
                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100">
                                {{ $user['nik'] }}</dd> --}}

                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:underline"
                                onclick="navigator.clipboard.writeText('{{ $user['nik'] }}');toast('NIK Berhasil di Salin')"
                                title="Klik untuk menyalin">
                                {{ $user['nik'] }}
                            </dd> --}}

                            <x-profile-copy-text subjek="NIK">
                                {{ $user['nik'] }}
                            </x-profile-copy-text>

                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Email Pribadi</dt>
                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:underline"
                                onclick="navigator.clipboard.writeText('{{ $user['email_pribadi'] }}');toast('Email Pribadi Berhasil di Salin')"
                                title="Klik untuk menyalin">
                                {{ $user['email_pribadi'] }}
                            </dd> --}}
                            <x-profile-copy-text subjek="Email Pribadi">
                                {{ $user['email_pribadi'] }}
                            </x-profile-copy-text>

                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Email Institusi</dt>
                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100">
                                {{ $user['email_institusi'] }}
                            </dd> --}}

                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:underline"
                                onclick="navigator.clipboard.writeText('{{ $user['email_institusi'] }}');toast('Email Institusi Berhasil di Salin')"
                                title="Klik untuk menyalin">
                                {{ $user['email_institusi'] }}
                            </dd> --}}
                            <x-profile-copy-text subjek="Email institusi">
                                {{ $user['email_institusi'] }}
                            </x-profile-copy-text>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">No Telepon</dt>
                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100">
                                {{ $user['telepon'] }}
                            </dd> --}}

                            {{-- <dd class="truncate font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:underline"
                                onclick="navigator.clipboard.writeText('{{ $user['telepon'] }}');toast('Telepon Berhasil di Salin')"
                                title="Klik untuk menyalin">
                                {{ $user['telepon'] }}
                            </dd> --}}
                            <x-profile-copy-text subjek="Telepon">
                                {{ $user['telepon'] }}
                            </x-profile-copy-text>
                        </div>
                    </dl>

                    <div class="flex justify-center items-center mt-10">
                        @if ($user['is_active'] == true)
                            <form id="form-nonaktif-{{ $user['id'] }}"
                                action="{{ route('manage.pegawai.set-non-active', ['idUser' => $user['id']]) }}"
                                method="POST" class="inline">
                                @csrf
                                <a href="#"
                                    onclick="event.preventDefault(); konfirmasiNonaktif('{{ $user['id'] }}')"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gradient-to-b from-gray-100 to-gray-50
                                px-3.5 py-2 text-xs font-medium text-gray-700 shadow-sm hover:from-gray-200 hover:to-gray-100
                                focus:outline-none focus:ring-2 focus:ring-gray-400 active:scale-95 transition-all duration-200
                                dark:from-gray-800 dark:to-gray-700 dark:text-gray-100">
                                    <i class="fa-solid fa-power-off text-[13px] text-[#EF4444]"></i>
                                    Nonaktifkan Akun
                                </a>
                            </form>
                        @else
                            <form id="form-aktif-{{ $user['id'] }}"
                                action="{{ route('manage.pegawai.set-active', ['idUser' => $user['id']]) }}" method="POST"
                                class="inline">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); konfirmasiAktif('{{ $user['id'] }}')"
                                    class="inline-flex items-center gap-2 rounded-lg border border-green-200 
                                bg-gradient-to-b from-green-100 to-green-50 px-3.5 py-2 text-xs font-medium text-green-700 
                                shadow-sm hover:from-green-200 hover:to-green-100 focus:outline-none focus:ring-2 
                                focus:ring-green-400 active:scale-95 transition-all duration-200
                                dark:from-green-800 dark:to-green-700 dark:text-green-100">
                                    <i class="fa-solid fa-power-off text-[13px] text-[#10B981]"></i>
                                    Aktifkan Akun
                                </a>
                            </form>
                        @endif
                    </div>



                </div>
            </section>

            {{-- Right column: details --}}
            <section class="lg:col-span-2 space-y-8">

                {{-- Section: Data Personal --}}
                <div
                    class="rounded-2xl border border-gray-200 bg-white pt-0 p-6 shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-6 flex items-center justify-between gap-2">
                        <h3
                            class="font-semibold tracking-wide shadow-sm py-3 px-5 rounded-b-lg bg-blue-500 text-white dark:text-gray-100">
                            Data Personal</h3>
                        <div class="flex md:items-center pt-2 items-end justify-end gap-2">
                            <a href="#"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-b border-blue-200 border-1 px-3.5 py-2 text-xs font-medium text-blue-600 shadow-sm hover:from-blue-500 hover:to-blue-400 hover:text-white active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                                ✏️ <span>Ubah Data</span>
                            </a>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Nama Lengkap</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user['nama_lengkap'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Tempat Lahir</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user['tempat_lahir'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Tanggal Lahir</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user['tgl_lahir'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Jenis Kelamin</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user['jenis_kelamin'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Alamat</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user['alamat'] }}</dd>

                        </div>
                    </dl>
                </div>





                {{-- Section: Status Kepegawaian --}}
                <div
                    class="rounded-2xl border border-gray-200 bg-white pt-0 p-6 shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-6 flex items-start justify-start">
                        <h3
                            class="font-semibold tracking-wide py-3 shadow-sm px-5 rounded-b-lg bg-blue-500 text-white dark:text-gray-100">
                            Data
                            Kepegawaian</h3>
                    </div>
                    <dl class="grid grid-cols-1 gap-x-8 gap-y-4 mb-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Nomor Induk Pegawai (NIP)</dt>

                            <dd
                                class="mt-1 font-semibold text-gray-900 {{ $user['pegawai_detail']['nip'] ?? 'opacity-55' }}">
                                {{ $user['pegawai_detail']['nip'] ?? 'Belum ada data' }}</dd>
                        </div>
                        {{-- {{ dd($user['tipe_pegawai']) }} --}}

                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Status Kepegawaian</dt>
                            <dd class="mt-1">
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    {{ $user['pegawai_detail']['status_pegawai']['status_pegawai'] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Jenis Kepegawaian</dt>
                            <dd class="mt-1">
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200">
                                    {{ $user['tipe_pegawai'] }}
                                </span>
                            </dd>
                        </div>
                        @if ($user['tipe_pegawai'] === 'TPA')
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Nomor Induk Tenaga Ahli (NITK)</dt>
                                <dd
                                    class="mt-1 font-semibold text-gray-900 {{ $user['pegawai_detail']['data_tpa']['nitk'] ?? 'opacity-55' }}">
                                    {{ $user['pegawai_detail']['data_tpa']['nitk'] ?? 'Belum ada data' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Jabatan Fungsional Karyawan (JFK)</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200">

                                        @if (isset($user->tpa->jfk_aktif[0]))
                                            {{ $user->tpa->jfk_aktif[0]->data_jfk->nama_jfk }}
                                        @else
                                            Belum Ada Data
                                        @endif
                                        {{-- {{ dd($user->dosen, $user) }} --}}
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <dl class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Terhitung Mulai Tanggal (TMT)</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                {{ $user['pegawai_detail']['tmt_mulai'] }}
                            </dd>
                        </div>
                        @if ($user['pegawai_detail']['tmt_selesai'] != null)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Tanggal Berhenti</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">-</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Section: Data Kedosenan --}}
                {{-- {{ dd($user['pegawai_detail']['status_pegawai']['tipe_pegawai']) }} --}}
                @if ($user['tipe_pegawai'] === 'Dosen')
                    <div
                        class="rounded-2xl border border-gray-200 bg-white pt-0 p-6 shadow-md dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-6 flex items-start justify-start">
                            <h3
                                class="font-semibold tracking-wide py-3 shadow-sm px-5 rounded-b-lg bg-blue-500 text-white dark:text-gray-100">
                                Data
                                Kedosenan</h3>
                        </div>
                        <dl class="grid grid-cols-1 gap-x-8 gap-y-4 mb-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Nomor Induk Dosen Nasional (NIDN)</dt>
                                <dd
                                    class="mt-1 font-semibold text-gray-900 {{ $user['pegawai_detail']['data_dosen']['nidn'] ?? 'opacity-55' }}">
                                    {{ $user['pegawai_detail']['data_dosen']['nidn']  ?? 'Belum ada data' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Nomor UPTK (NUPTK)</dt>
                                <dd
                                    class="mt-1 font-semibold text-gray-900 {{ $user['pegawai_detail']['data_dosen']['nuptk']  ?? 'opacity-55' }}">
                                    {{ $user['pegawai_detail']['data_dosen']['nuptk'] ?? 'Belum ada data' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Program Studi</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                        {{ $user->dosen->prodi->position_name ?? 'Belum ada data' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Jabatan Fungsional Akademik (JFA)</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200">
                                        {{ $user->dosen->jfa_aktif[0]->jfa->nama_jabatan ?? 'Belum ada data' }}
                                        {{-- {{ dd($user->dosen, $user) }} --}}
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <dl class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Pangkat & Golongan</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                    {{-- {{ dd($user->dosen->pangkat_golongan_aktif[0]->refPangkatGolongan) }} --}}
                                    {{ isset($user->dosen->pangkat_golongan_aktif[0]) ? $user->dosen->pangkat_golongan_aktif[0]->refPangkatGolongan->pangkat . ' Golongan ' . $user->dosen->pangkat_golongan_aktif[0]->refPangkatGolongan->golongan : 'Belum ada data' }}
                                </dd>
                            </div>
                            @if ($user['pegawai_detail']['tmt_selesai'] != null)
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">Tanggal Berhenti</dt>
                                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">-</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                {{-- Kontak Darurat — View-only, Tailwind-only --}}
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-6">
                        <div class="flex align-items-center flex justify-between">
                            <h3 class="text-lg font-semibold tracking-wide text-gray-900 dark:text-gray-100">Kontak Darurat
                            </h3>
                            <a href="{{ session('account')['is_admin'] && $user['id'] != session('account')['id']
                                ? route('manage.emergency-contact.list', ['id_User' => $user['id']])
                                : route('profile.emergency-contacts.list', ['id_User' => session('account')['id']]) }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-b border-blue-200 border-1 px-3.5 py-2 text-xs font-medium text-blue-600 shadow-sm hover:from-blue-500 hover:to-blue-400 hover:text-white active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                                ✏️ <span>Ubah Data</span>
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pilih salah satu kontak saat keadaan darurat.
                        </p>
                    </div>

                    <div class="grid grid-cols-1  gap-5 xl:grid-cols-2">
                        @forelse ($user['emergency_contacts'] as $contact)
                            <!-- Card 1 -->
                            <div
                                class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-transparent transition-all hover:shadow-md dark:border-gray-800/80 dark:bg-gray-900/70">
                                <div
                                    class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-blue-500/80 to-indigo-500/80">
                                </div>

                                <div class="flex items-start gap-4">
                                    {{-- <div
                                    class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white text-sm font-semibold">
                                    JP
                                </div> --}}
                                    {{-- {{ dd($contact) }} --}}
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="truncate text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $contact['nama_lengkap'] }}</h4>
                                            <span
                                                class="rounded-full bg-blue-50 px-2 py-0.5 text-center text-[11px] font-medium text-blue-700 ring-1 ring-blue-200 dark:bg-blue-950/30 dark:text-blue-200 dark:ring-blue-900/40">
                                                {{ implode(' ', array_slice(preg_split('/[\s-]+/', trim($contact['status_hubungan'])), 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="mt-3 space-y-2 text-sm">
                                            <div class="flex gap-2 text-gray-700 dark:text-gray-300">
                                                <svg class="mt-0.5 h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M2 5.5C2 4.672 2.672 4 3.5 4h2.1c.6 0 1.117.39 1.28.966l.83 2.986a1.35 1.35 0 01-.387 1.382l-1.14 1.04a12.06 12.06 0 006.843 6.842l1.041-1.14c.353-.388.9-.544 1.382-.387l2.986.83c.575.163.966.68.966 1.28v2.1c0 .828-.672 1.5-1.5 1.5H18C9.716 21 3 14.284 3 6V5.5z" />
                                                </svg>
                                                <span>{{ $contact['telepon'] ?? 'Belum diisi' }}</span>
                                            </div>
                                            <div class="flex gap-2 text-gray-700 dark:text-gray-300">
                                                <svg class="mt-0.5 h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor">
                                                    <path stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M4 6l8 6 8-6M4 6v12h16V6" />
                                                </svg>
                                                <span>{{ $contact['email'] ?? 'Belum diisi' }}</span>
                                            </div>
                                            <div class="flex gap-2 text-gray-700 dark:text-gray-300">
                                                <svg class="mt-0.5 h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor">
                                                    <path stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M12 21s-6-4.35-6-9a6 6 0 1112 0c0 4.65-6 9-6 9z" />
                                                    <circle cx="12" cy="12" r="2" fill="currentColor" />
                                                </svg>
                                                {{-- {{ Dd( $contact['alamat']) }} --}}
                                                <span>{{ $contact['alamat'] ?? $contact['alamat'] == null || $contact['alamat'] == '' ? 'Belum diisi' : 'Belum diisi' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col items-center justify-center py-10 px-4">
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium bg-gray-400 text-gray-100 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500">
                                    
                                    Belum ada data kontak darurat
                                </span>
                            </div>
                        @endforelse
                    </div>
                </div>






                {{-- Section: Catatan --}}
                <div
                    class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-gray-600 dark:border-gray-800 dark:bg-gray-900/40 dark:text-gray-300">
                    <p>
                        Tip: Gunakan tombol
                        <span class="rounded bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-800">Salin</span>
                        untuk cepat menyalin email. Klik
                        <span
                            class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-700 dark:bg-blue-950/40 dark:text-blue-200">Ubah
                            Data</span>
                        untuk memperbarui informasi.
                    </p>
                </div>
            </section>
        </div>
    </div>

    <!-- Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="copyToast" class="toast align-items-center bg-blue-950 border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-white" id="copyToastBody">
                    Teks berhasil disalin ke clipboard!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyButtons = document.querySelectorAll('.copy');
            const toastEl = document.getElementById('copyToast');
            const toastBody = document.getElementById('copyToastBody');
            const toast = new bootstrap.Toast(toastEl, {
                delay: 2000,
                autohide: true
            });

            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (!navigator.clipboard) {
                        toastBody.textContent = '⚠️ Browser tidak mendukung Clipboard API.';
                        toastEl.classList.add('bg-blue-950');
                        toast.show();
                        return;
                    }

                    setTimeout(async () => {
                        try {
                            toastBody.textContent =
                                'Teks berhasil disalin ke clipboard!';
                            toastEl.classList.remove('bg-danger');
                            toastEl.classList.add('bg-blue-950');
                            toast.show();
                        } catch (err) {
                            toastBody.textContent = 'Gagal mengakses clipboard.';
                            toastEl.classList.remove('bg-blue-950');
                            toastEl.classList.add('bg-danger');
                            toast.show();
                        }
                    }, 150);
                });
            });
        });
    </script>
@endsection
