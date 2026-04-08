<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- REMOVED style="height: fit-content !important; min-height: fit-content !important;" -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>
        @yield('title') | {{ config('app.name', 'Laravel') }}
    </title> --}}

    <title>
        {{ trim($__env->yieldContent('title'))
            ? config('app.name') . ' | ' . $__env->yieldContent('title')
            : config('app.name') }}
    </title>


    <link rel="preconnect" href="https://fonts.bunny.net">
    <!-- <link rel="stylesheet" href="{{ asset('style.css') }}"> -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- Fonts -->

    <!-- Scripts -->
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
</head>

<body class="font-sans antialiased pattern-batik-kawung bg-[#DEDEDE] h-auto hide-scrollbar">

    <div class="flex-shrink w-full min-h-screen bg-gray-100 dark:bg-gray-900 hide-scrollbar">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            {{-- <header class="bg-white shadow dark:bg-gray-800" id="header-app"> --}}
            <header class="bg-white min-h-screen dark:bg-gray-800" id="header-app">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-shrink-0 pattern-batik-kawung">
            @yield('content')
        </main>
    </div>


</body>

@once
    @yield('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script
        src="https://unpkg.com/bootstrap-table@1.22.1/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce
<script>
    
    @if (session('message'))
// {{ dd(session('message')) }}
        Swal.fire({
            icon: 'success', // bisa diganti 'info', 'error', dll
            title: 'Info',
            text: "{{ session('message') }}",
            confirmButtonText: 'OK'
        });
    @endif
</script>


</html>
