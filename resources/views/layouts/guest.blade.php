<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        svg {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .pattern-batik-kawung {
            background-color: #ffffff !important;
            background-image:
                radial-gradient(circle at 50% 50%, transparent 25%, #f1f5f9 25%, #f1f5f9 28%, transparent 28%),
                radial-gradient(circle at 50% 50%, transparent 40%, #f8fafc 40%, #f8fafc 45%, transparent 45%) !important;
            background-size: 60px 60px !important;
            background-position: 0 0, 30px 30px !important;
        }

        .second-pattern-batik-kawung {
            background-color: #ffffff !important;
            background-image:
                radial-gradient(circle at 50% 50%, transparent 25%, #f1f5f9 25%, #f1f5f9 28%, transparent 28%),
                radial-gradient(circle at 50% 50%, transparent 40%, #f8fafc 40%, #f8fafc 45%, transparent 45%) !important;
            background-size: 60px 60px !important;
            background-position: 0 0, 30px 30px !important;
        }
    </style>
</head>

<body class="font-sans pattern-batik-kawung antialiased text-white-900">
    <div
        class="flex pattern-batik-kawung flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0 dark:bg-gray-900">

        <div class="">
            {{ $slot }}
        </div>
    </div>
    <x-footer></x-footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @include('components.js.pop-message')

    @include('components.js.route-pop-up-button')
    @if (session('error_alert'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Waduh, Ada Masalah!',
                text: "{{ session('error_alert') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke, Saya Mengerti'
            });
        </script>
    @endif

</body>

</html>
