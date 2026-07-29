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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <style>
        .pattern-batik-kawung {
            background-color: #ffffff !important;
            background-image:
                radial-gradient(circle at 50% 50%, transparent 25%, #f1f5f9 25%, #f1f5f9 28%, transparent 28%),
                radial-gradient(circle at 50% 50%, transparent 40%, #f8fafc 40%, #f8fafc 45%, transparent 45%) !important;
            background-size: 60px 60px !important;
            background-position: 0 0, 30px 30px !important;
        }

        .pattern-batik-kawung-dark {
            background-color: #0f172a !important;

            background-image:
                radial-gradient(circle at 50% 50%, transparent 25%, #1e293b 25%, #1e293b 28%, transparent 28%),
                radial-gradient(circle at 50% 50%, transparent 40%, #334155 40%, #334155 45%, transparent 45%) !important;

            background-size: 60px 60px !important;
            background-position: 0 0, 30px 30px !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    @yield('header')
</head>

<body class="font-sans antialiased pattern-batik-kawung bg-yellow-400 h-auto hide-scrollbar">
    {{-- <body class="font-sans antialiased pattern-batik-kawung bg-[#DEDEDE] h-auto hide-scrollbar"> --}}

    <div class="flex-shrink w-full min-h-screen pattern-batik-kawung bg-gray-100 dark:bg-gray-900 hide-scrollbar">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white min-h-screen pattern-batik-kawung dark:bg-gray-800" id="header-app">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-shrink-0 pattern-batik-kawung my-2 min-h-screen">
            @yield('content')
        </main>


    </div>

    <!-- Scripts at the end of body -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js" defer></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script> --}}
    <script
        src="https://unpkg.com/bootstrap-table@1.22.1/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"
        defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('script')

    @include('kelola_data.pegawai.js.alert-success-from-controller')
    @include('components.js.pop-message')
    @include('components.js.route-pop-up-button')
    @if (session('message'))
    <script>
        // Swal.fire({
        //     icon: undefined,
        //     title: 'Berhasil',
        //     text: "{{session('message')}}",
        //     confirmButtonText: 'OK'
        // });

        Swal.fire({
            html: `
                <i class="bi bi-check-circle-fill text-success" style="font-size:48px"></i>
                <div class="mt-2 fw-bold">Berhasil</div>
                <div>"{{session('message')}}"</div>
            `
        });
    </script>
    @endif

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

    @if (session('notify'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'P E M B E R I T A H U A N',
                text: "{{ session('notify') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
        </script>
    @endif

    @if (isset(session()->all()['testing']))
        {{-- {{dd(session('testing'))}} --}}
        @php
            $testing = session()->all()['testing'];
            // dd($testing, 'testing ada', $testing['kode'], $testing['name']);
            $testingQuestions = [
                [
                    'key' => 'L1',
                    'label' => 'Fitur ini berfungsi sesuai kebutuhan saya.',
                    'type' => 'scale',
                    'labels' => ['Tidak Sesuai', 'Sangat Sesuai'], // Kustom label
                ],
                [
                    'key' => 'L2',
                    'label' => 'Fitur ini mudah dipahami dan digunakan.',
                    'type' => 'scale',
                    'labels' => ['Sangat Sulit', 'Sangat Mudah'],
                ],
                [
                    'key' => 'L3',
                    'label' => 'Fitur ini berjalan cepat dan responsif.',
                    'type' => 'scale',
                    'labels' => ['Lambat', 'Sangat Cepat'],
                ],
                [
                    'key' => 'L4',
                    'label' => 'Fitur ini berjalan stabil tanpa error.',
                    'type' => 'scale',
                    'labels' => ['Banyak Bug', 'Sangat Stabil'],
                ],
                [
                    'key' => 'L5',
                    'label' => 'Tampilan fitur ini menarik dan nyaman dilihat?',
                    'type' => 'scale',
                    'labels' => ['Buruk', 'Sangat Bagus'],
                ],
                [
                    'key' => 'L6',
                    'label' => 'Apa kendala utama yang Anda alami?',
                    'type' => 'text',
                ],
            ];
        @endphp


        <x-question-testing
            route="{{ route('testing', ['kode' => $testing['kode'], 'nama_fitur' => $testing['name']]) }}"
            fitur_code="{{ $testing['kode'] }}" fitur_name="{!! html_entity_decode($testing['name']) !!}" :config="$testingQuestions" />
    @endif
</body>

</html>
