<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ trim($__env->yieldContent('title'))
            ? config('app.name') . ' | ' . $__env->yieldContent('title')
            : config('app.name') }}
    </title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .pattern-batik-kawung {
            background-color: #ffffff !important;
            background-image:
                radial-gradient(circle at 50% 50%, transparent 25%, #f1f5f9 25%, #f1f5f9 28%, transparent 28%),
                radial-gradient(circle at 50% 50%, transparent 40%, #f8fafc 40%, #f8fafc 45%, transparent 45%) !important;
            background-size: 60px 60px !important;
            background-position: 0 0, 30px 30px !important;
        }
    </style>

    @yield('header')
</head>

<body class="font-sans antialiased pattern-batik-kawung bg-[#DEDEDE] h-auto hide-scrollbar">

    <div class="flex-shrink w-full min-h-screen bg-gray-100 dark:bg-gray-900 hide-scrollbar">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white min-h-screen dark:bg-gray-800" id="header-app">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-shrink-0 pattern-batik-kawung">
            @yield('content')
        </main>
    </div>

    <!-- Scripts at the end of body -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script
        src="https://unpkg.com/bootstrap-table@1.22.1/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('script')

    @include('kelola_data.pegawai.js.alert-success-from-controller')
    @include('components.js.pop-message')
    @include('components.js.route-pop-up-button')
    <script>
        @if (session('message'))
            Swal.fire({
                icon: 'success',
                title: 'Info',
                text: "{{ session('message') }}",
                confirmButtonText: 'OK'
            });
        @endif
    </script>
</body>

</html>
