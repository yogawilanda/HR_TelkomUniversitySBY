@include('layouts.navigation')

<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 w-full max-w-md text-center">

            <!-- Pemberitahuan Email OTP -->

            <h2 class="text-xl sm:text-2xl font-bold mb-2">Verifikasi Email</h2>

            @if (request()->input('email_institusi'))
                <p class="text-gray-500 text-sm sm:text-base mb-4">
                    Kode verifikasi telah dikirim ke email pribadi anda:
                    <span class="font-semibold">{{ $email_pribadi }}</span> , Silahkan masukkan kode di kotak dibawah
                    ini.
                </p>
            @endif
            {{-- <p class="text-gray-500 text-sm sm:text-base mb-6">
                Masukkan kode verifikasi yang telah dikirim ke email pribadi kamu.
            </p> --}}



            <form id="otpForm" action="{{ route('verify-email.send') }}" method="POST">
                @csrf

                <!-- Input OTP -->
                <div class="flex justify-center gap-2 sm:gap-3 mb-2">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1" inputmode="numeric"
                            class="otp w-10 h-10 sm:w-12 sm:h-12 text-center border rounded-lg @error('otp') border-red-500 @enderror">
                    @endfor
                </div>

                <!-- Tampilkan error OTP jika ada -->
                @error('otp')
                    <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                @enderror

                <!-- Hidden fields -->
                <input type="hidden" name="otp" id="otpFull">
                <input type="hidden" name="email_institusi"
                    value="{{ old('email_institusi') ?? request()->input('email_institusi') }}">

                <!-- Shortcut info -->
                <div class="text-xs text-gray-500 mb-6 space-y-1">
                    <p>Tekan <span class="font-semibold bg-gray-100 px-2 py-1 rounded">Q</span> untuk reset kode</p>
                    <p>Tekan <span class="font-semibold bg-gray-100 px-2 py-1 rounded">F2</span> untuk kirim verifikasi
                    </p>
                </div>

                <!-- Submit button -->

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow flex items-start space-x-3 animate-fadeIn"
                        role="alert">
                        <!-- Icon -->
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 16 16"
                            xmlns="http://www.w3.org/2000/svg" aria-label="Warning:">
                            <path
                                d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.706c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1-2.002 0 1 1 0 0 1 2.002 0z" />
                        </svg>

                        <!-- Text -->
                        <div>
                            <strong class="font-bold">Terjadi Kesalahan!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                <li>{{ session('error') }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tailwind Animate -->
                    <style>
                        @keyframes fadeIn {
                            from {
                                opacity: 0;
                                transform: translateY(-10px);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }

                        .animate-fadeIn {
                            animation: fadeIn 0.5s ease-in-out;
                        }
                    </style>
                @endif
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 mt-2 sm:py-3 rounded-lg hover:bg-blue-700 transition">
                    Verifikasi
                </button>

            </form>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll(".otp");
        const form = document.getElementById("otpForm");
        const otpFull = document.getElementById("otpFull");

        // Auto pindah input
        inputs.forEach((input, index) => {
            input.addEventListener("input", (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, "");
                if (e.target.value && index < inputs.length - 1) inputs[index + 1].focus();
            });
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && input.value === "" && index > 0) inputs[index - 1].focus();
            });
        });

        // Paste OTP
        inputs[0].addEventListener("paste", function(e) {
            let pasteData = e.clipboardData.getData("text").replace(/[^0-9]/g, "");
            if (pasteData.length === inputs.length) {
                inputs.forEach((input, i) => input.value = pasteData[i]);
            }
            e.preventDefault();
        });

        // Gabungkan OTP sebelum submit
        form.addEventListener("submit", function() {
            otpFull.value = Array.from(inputs).map(input => input.value).join('');
        });

        // Shortcut keyboard
        document.addEventListener("keydown", function(e) {
            if (e.key.toLowerCase() === "q") {
                inputs.forEach(input => input.value = "");
                inputs[0].focus();
            }
            if (e.key === "F2") {
                e.preventDefault();
                form.submit();
            }
        });
    </script>
</x-guest-layout>
