@extends('layouts.app')

@section('content')
<x-dupak.sidebar />

<div class="mt-16 md:ml-64">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold">
                        Daftar Pengajuan DUPAK
                        @if ($user->is_admin)
                        (Admin View)
                        @endif
                    </h1>
                    @php
                        $btnDisabled = $isMaxJfa;
                    @endphp
                    <!-- add user id to the link so it can be used -->
                    <a href="{{ $btnDisabled ? '#' : route('dupak.pengajuan.create', ['userId' => $dosenId]) }}"
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase border border-transparent rounded-md focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300
                        {{ $btnDisabled ? 'bg-gray-400 cursor-not-allowed opacity-60' : 'bg-blue-900 hover:bg-blue-950 active:bg-blue-900' }}">
                        Buat Pengajuan Baru
                    </a>
                </div>

                @if (session('success'))
                <div class="px-4 py-3 my-4 text-green-700 bg-green-100 border border-green-400 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                @endif

                @if (session('error'))
                <div class="px-4 py-3 my-4 text-red-700 bg-red-100 border border-red-400 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                @endif

                @if ($isMaxJfa)
                <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded">
                    <i class="fas fa-check-circle mr-1"></i>
                    Anda sudah mencapai jabatan fungsional tertinggi (Guru Besar). Pengajuan kenaikan jabatan tidak tersedia.
                </div>
                @endif

                <!-- List Pengajuan -->
                <div class="overflow-hidden bg-white rounded-lg shadow rounded-b-lg">
                    <table class="min-w-full divide-y divide-gray-200 rounded-b-lg">
                        <thead class="bg-blue-900">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
                                    ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
                                    Nama Dosen
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
                                    Tanggal Pengajuan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
                                    Periode Ajuan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pengajuan as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $item->dosen->nama ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $item->dosen->nama ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                </td>
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-sm text-center text-gray-500">
                                    Belum ada data pengajuan yang tersedia.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4 bg-white border-t">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection