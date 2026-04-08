@include('layouts.navigation')

<x-guest-layout>
    <div class="flex flex-col md:flex-row">

        <!-- Left: Hero branding panel -->
        <div class="flex items-center justify-center min-w-0 p-10 md:flex-1">
            <div class="max-w-full text-left">
                <h1 class="text-[64px] md:text-[128px] leading-none font-extrabold text-[1C2762] dark:text-gray-100">
                    SDM
                </h1>
                <p class="mt-4 text-[20px] md:text-[36px] font-thin text-[1C2762] dark:text-gray-400">
                    Sistem Informasi Manajemen Data Pegawai
                </p>
            </div>
        </div>

        <!-- Right: Login card -->
        <div class="flex items-start justify-center min-w-0 p-10 md:flex-1">
            <div class="w-full max-w-md p-8 bg-white shadow-lg dark:bg-gray-800 rounded-xl">

                @if (session()->has('error'))
                    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
                        <ul class="list-disc pl-5">

                            {{-- @foreach ($errors->all() as $error) --}}
                                {{-- {{ dD($errors->all()) }} --}}
                                <li>
                                    {{ session('error') }}
                                </li>
                            {{-- @endforeach --}}
                        </ul>
                    </div>
                @endif
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Error Validation -->
                @if ($errors->any())
                    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
                        <ul class="list-disc pl-5">

                            @foreach ($errors->all() as $error)
                                {{-- {{ dD($errors->all()) }} --}}
                                <li>
                                    {!! str_replace(
                                        'email pribadi belum terverifikasi',
                                        'email pribadi belum terverifikasi <a onclick="openPopup()" class="underline text-blue-600">verifikasi sekarang</a>',
                                        $error,
                                    ) !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <x-itxt fill="mb-4" nm="email_institusi" type="email" lbl="Email Institusi"
                        plc="john@telkomuniversity.ac.id" max="100" fill="flex-grow"></x-itxt>

                    @error('email_institusi')
                        <div class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </div>
                    @enderror


                    <!-- Password -->
                    <x-itxt type="password" lbl="Password" nm="password" max="15" fill="flex-grow"></x-itxt>

                    @error('password')
                        <div class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </div>
                    @enderror


                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="text-indigo-600 border-gray-300 rounded shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500"
                                name="remember">

                            <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">
                                Remember me
                            </span>
                        </label>
                    </div>


                    <!-- Actions -->
                    <div class="flex items-center justify-between mt-6">

                        @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-600 underline hover:text-indigo-800 dark:text-indigo-400"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif


                        <x-primary-button class="px-6 py-2">
                            {{ __('Log in') }}
                        </x-primary-button>

                    </div>

                </form>
            </div>
        </div>

    </div>
</x-guest-layout>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    @if (session('message'))
        Swal.fire({
            icon: 'success', // bisa diganti 'info', 'error', dll
            title: 'Info',
            text: "{{ session('message') }}",
            confirmButtonText: 'OK'
        });
    @endif
    function openPopup() {

        Swal.fire({
            title: 'Masukkan Email Institusi',
            html: `
        <div class="text-left space-y-2">
            <label class="text-sm font-semibold">
                Email Institusi
            </label>

            <input 
                id="email"
                type="email"
                placeholder="something@telkomuniversity.ac.id"
                class="w-full px-3 py-2 border rounded-lg"
            >
        </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            focusConfirm: false,

            preConfirm: () => {

                const email = document.getElementById('email').value

                if (!email) {
                    Swal.showValidationMessage('Email tidak boleh kosong')
                    return false
                }

                // buat form
                const form = document.createElement("form");
                // form.method = "GET";
                form.action = "{{ route('verify-email.view') }}";

                // input email
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "email_institusi";
                input.value = email;

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            }

        })

    }
</script>
