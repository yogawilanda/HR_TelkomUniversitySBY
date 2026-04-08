@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-medium">Detail Target Harian</h2>
            <p class="text-sm text-gray-600">{{ $item->pekerjaan }}</p>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white p-4 rounded shadow max-w-2xl">
        <div class="mb-3">
            <strong>Pekerjaan:</strong>
            <div>{{ $item->pekerjaan }}</div>
        </div>
        <div class="mb-3">
            <strong>Target Kinerja:</strong>
            <div>{{ $item->targetKinerja->nama ?? '-' }}</div>
        </div>
        <div class="mb-3">
            <strong>Jumlah:</strong>
            <div>{{ $item->jumlah ?? '-' }}</div>
        </div>
        <div class="mb-3">
            <strong>Waktu (menit):</strong>
            <div>{{ $item->waktu_minutes ?? '-' }}</div>
        </div>
        <div class="mb-3">
            <strong>Start:</strong>
            <div>{{ $item->start ? \Carbon\Carbon::parse($item->start)->format('d/m/Y H:i') : '-' }}</div>
        </div>
        <div class="mb-3">
            <strong>End:</strong>
            <div>{{ $item->end ? \Carbon\Carbon::parse($item->end)->format('d/m/Y H:i') : '-' }}</div>
        </div>

        <div class="flex gap-2 mt-4">
            <a href="{{ route('manage.target-kinerja.harian.list') }}" class="px-4 py-2 bg-gray-300 rounded">Kembali</a>
            <a href="{{ route('manage.target-kinerja.harian.isi', $item->id) }}" class="px-4 py-2 bg-green-600 text-white rounded">Isi Laporan</a>
        </div>
    </div>
@endsection
