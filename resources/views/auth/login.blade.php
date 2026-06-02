@include('layouts.navigation')

<x-guest-layout>
    <!-- Background Wrapper: Memberikan kesan bersih dan lega -->
    <div class="min-h-screen dark:bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="w-full max-w-6xl flex flex-col md:flex-row gap-12 lg:gap-20 items-center justify-between">

            <!-- Kiri: Panel Informasi & Branding -->
            <div class="flex-1 w-full text-left">

                <!-- Judul Sistem -->
                <div class="mb-10">
                    <h1 class="text-5xl md:text-7xl font-extrabold text-[#1C2762] dark:text-gray-100 tracking-tight">
                        SDM
                    </h1>
                    <p class="mt-3 text-xl md:text-2xl font-light text-slate-600 dark:text-slate-400">
                        Sistem Informasi Manajemen Data Pegawai
                    </p>
                    <!-- Aksen garis -->
                    <div class="w-20 h-1.5 mt-6 bg-[#1C2762] dark:bg-blue-500 rounded-full"></div>
                </div>

                <!-- Bagian Akordion Panduan -->
                <div class="mt-10 space-y-4">
                    <!-- Akordion 1: Pegawai Baru -->
                    <details class="group border-l-4 border-[#1C2762] bg-white dark:bg-gray-800 dark:border-blue-500 rounded-r-xl shadow-sm hover:shadow-md transition-shadow duration-200 [&_summary::-webkit-details-marker]:hidden" open>
                        <summary class="flex cursor-pointer items-center justify-between gap-1.5 p-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                Panduan untuk Pegawai Baru
                            </h2>
                            <span class="shrink-0 rounded-full bg-slate-50 p-2 text-gray-500 group-open:bg-[#1C2762] group-open:text-white transition-colors duration-300 dark:bg-gray-700 dark:text-gray-300 dark:group-open:bg-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 transition-transform duration-300 group-open:-rotate-45" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </summary>
                        <div class="px-6 pb-6 leading-relaxed text-slate-600 dark:text-slate-300 text-sm">
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Silakan lakukan login menggunakan <strong>Email Institusi</strong> dan password Anda. <span class="text-xs block mt-1 text-slate-500">(Password awal diberikan oleh pihak SDM, atau dapat Anda periksa pada kotak masuk email pribadi yang didaftarkan selama proses administrasi pegawai).</span></li>
                                <li>Lakukan verifikasi email pribadi Anda sesuai instruksi yang muncul pada sistem.</li>
                                <li>Setelah verifikasi berhasil dilakukan, Anda dapat mengakses sistem sepenuhnya.</li>
                            </ul>
                        </div>
                    </details>

                    <!-- Akordion 2: Lupa Password -->
                    <details class="group border-l-4 border-[#1C2762] bg-white dark:bg-gray-800 dark:border-blue-500 rounded-r-xl shadow-sm hover:shadow-md transition-shadow duration-200 [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex cursor-pointer items-center justify-between gap-1.5 p-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                Kendala Lupa Password?
                            </h2>
                            <span class="shrink-0 rounded-full bg-slate-50 p-2 text-gray-500 group-open:bg-[#1C2762] group-open:text-white transition-colors duration-300 dark:bg-gray-700 dark:text-gray-300 dark:group-open:bg-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 transition-transform duration-300 group-open:-rotate-45" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </summary>
                        <div class="px-6 pb-6 leading-relaxed text-slate-600 dark:text-slate-300 text-sm">
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Pastikan email pribadi Anda sudah berstatus terverifikasi.</li>
                                <li>Jika belum terverifikasi, Anda tidak perlu khawatir. Sistem akan melakukan pengecekan status verifikasi secara otomatis saat Anda mengajukan permintaan pembaruan password.</li>
                                <li>Klik tautan <strong>"Lupa password Anda?"</strong> pada formulir login di samping untuk memulai proses pembaruan kata sandi.</li>
                            </ul>
                        </div>
                    </details>
                </div>
            </div>

            <!-- Kanan: Kartu Login -->
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-slate-100 dark:border-gray-700 overflow-hidden">

                    <!-- Header Kartu Login -->
                    <div class="px-8 py-6 bg-[#1C2762] dark:bg-gray-900 text-center">
                        <h2 class="text-2xl font-bold text-white tracking-wide">Selamat Datang</h2>
                        <p class="text-sm text-blue-200 mt-1 font-light">Silakan masuk ke akun Anda</p>
                    </div>

                    <div class="p-8">
                        <!-- Flash Message Error -->
                        {{-- {{ dd(session()->has('error')) }} --}}
                        {{-- @if (session()->has('error'))
                            <div class="mb-6 p-4 text-sm text-red-800 bg-red-50 border-l-4 border-red-600 dark:bg-red-900/30 dark:text-red-300 rounded-r-md">
                                <ul class="list-disc pl-5">
                                    <li>{{ session('error') }}</li>
                                </ul>
                            </div>
                        @endif --}}
                        @if (session()->has('error_alert'))
                        {{-- {{ dump(session()->has('error_alert')) }} --}}
                            <div class="mb-6 p-4 text-sm text-red-800 bg-red-50 border-l-4 border-red-600 dark:bg-red-900/30 dark:text-red-300 rounded-r-md">
                                <ul class="list-disc pl-5">
                                    <li>{{ session('error_alert') }}</li>
                                </ul>
                            </div>
                        @endif

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <!-- Error Validation -->
                        @if ($errors->any())
                            <div class="mb-6 p-4 text-sm text-red-800 bg-red-50 border-l-4 border-red-600 dark:bg-red-900/30 dark:text-red-300 rounded-r-md">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>
                                            {!! str_replace(
                                                'email pribadi belum terverifikasi',
                                                'email pribadi belum terverifikasi <a onclick="openPopup()" class="font-medium underline text-blue-700 dark:text-blue-400 cursor-pointer hover:text-blue-800">verifikasi sekarang</a>',
                                                $error,
                                            ) !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf

                            <!-- Email -->
                            <div>
                                <x-itxt fill="mb-1" nm="email_institusi" type="email" lbl="Email Institusi" plc="contoh@telkomuniversity.ac.id" max="100" fill="flex-grow"></x-itxt>
                                @error('email_institusi')
                                    <div class="text-sm text-red-600 dark:text-red-400 mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <x-itxt type="password" lbl="Kata Sandi" nm="password" max="50" fill="flex-grow"></x-itxt>
                                @error('password')
                                    <div class="text-sm text-red-600 dark:text-red-400 mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between mt-4">
                                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                    <input id="remember_me" type="checkbox" class="w-4 h-4 text-[#1C2762] bg-gray-100 border-gray-300 rounded focus:ring-[#1C2762] dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600" name="remember">
                                    <span class="text-sm text-slate-600 ms-2 dark:text-gray-400 select-none">
                                        Ingat saya
                                    </span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm font-medium text-[#1C2762] underline cursor-pointer hover:text-blue-800 dark:text-blue-400 transition-colors" onclick="forget_password()">
                                        Lupa kata sandi?
                                    </a>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="pt-2">
                                <x-primary-button class="w-full flex justify-center py-3 text-base bg-[#1C2762] hover:bg-[#131b45] transition-colors duration-200">
                                    {{ __('Masuk') }}
                                </x-primary-button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

<!-- SweetAlert2 Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if (session('message'))
        Swal.fire({
            icon: 'success',
            title: 'Informasi',
            text: "{{ session('message') }}",
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#1C2762'
        });
    @endif

    function openPopup() {
        Swal.fire({
            title: '<h3 class="text-xl font-bold text-gray-800">Verifikasi Email</h3>',
            html: `
            <div class="text-left mt-2 mb-4">
                <p class="text-sm text-gray-600 mb-4">Silakan masukkan email institusi Anda untuk melanjutkan proses verifikasi.</p>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Institusi</label>
                <input
                    id="email"
                    type="email"
                    placeholder="contoh@telkomuniversity.ac.id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C2762] focus:border-transparent transition-all"
                >
            </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Verifikasi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1C2762',
            cancelButtonColor: '#e5e7eb',
            customClass: {
                cancelButton: 'text-gray-800'
            },
            focusConfirm: false,
            reverseButtons: true,
            preConfirm: () => {
                const email = document.getElementById('email').value;
                if (!email) {
                    Swal.showValidationMessage('Alamat email wajib diisi');
                    return false;
                }
                return { email: email };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('a');

                Swal.fire({
                    title: 'Memproses...',
                    html: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const email = result.value.email;
                const form = document.createElement("form");
                form.method = "GET";
                form.action = "{{ route('verify-email.view') }}";

                const inputEmail = document.createElement("input");
                inputEmail.type = "hidden";
                inputEmail.name = "email_institusi";
                inputEmail.value = email;

                form.appendChild(inputEmail);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function forget_password() {
        Swal.fire({
            title: '<h3 class="text-xl font-bold text-gray-800">Pemulihan Kata Sandi</h3>',
            html: `
            <div class="text-left mt-2 mb-4">
                <p class="text-sm text-gray-600 mb-4">Masukkan email institusi Anda. Kami akan mengirimkan instruksi untuk memperbarui kata sandi.</p>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Institusi</label>
                <input
                    id="email"
                    type="email"
                    placeholder="contoh@telkomuniversity.ac.id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C2762] focus:border-transparent transition-all"
                >
            </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim Instruksi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1C2762',
            cancelButtonColor: '#e5e7eb',
            customClass: {
                cancelButton: 'text-gray-800'
            },
            focusConfirm: false,
            reverseButtons: true,
            preConfirm: () => {
                const email = document.getElementById('email').value;
                if (!email) {
                    Swal.showValidationMessage('Alamat email wajib diisi');
                    return false;
                }
                return { email: email };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('a');

                Swal.fire({
                    title: 'Mengirim...',
                    html: 'Sedang mengirim instruksi pembaruan kata sandi.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const email = result.value.email;
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route('forget-password.send') }}";

                const csrfToken = document.createElement("input");
                csrfToken.type = "hidden";
                csrfToken.name = "_token";
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                const inputEmail = document.createElement("input");
                inputEmail.type = "hidden";
                inputEmail.name = "email_institusi";
                inputEmail.value = email;
                form.appendChild(inputEmail);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
