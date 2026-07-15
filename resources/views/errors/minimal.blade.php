@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 px-6">

    {{-- Emoji Besar di Paling Atas --}}
    <div class="flex justify-center mb-6">
        <svg class="w-32 h-32" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
            <circle cx="256" cy="256" r="256" fill="#ffd93b" />
            <circle cx="160" cy="208" r="32" fill="#3e4347" />
            <circle cx="352" cy="208" r="32" fill="#3e4347" />
            <path d="M370 330c-40 40-188 40-228 0" fill="none" stroke="#3e4347" stroke-width="32" stroke-linecap="round" />
        </svg>
    </div>

    <div class="max-w-xl mx-auto text-center">

        <div class="flex items-center justify-center">
            <h1 class="px-4 text-5xl font-bold text-gray-700 dark:text-gray-300 border-r border-gray-400">
                @yield('code')
            </h1>

            <div class="ml-4 text-xl uppercase text-gray-700 dark:text-gray-300 tracking-wider">
                @yield('message')
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <a href="{{ route('dupak.dashboard') }}"
            class="mt-6 inline-block px-6 py-3 bg-blue-900 hover:bg-blue-950 rounded-lg text-white">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection