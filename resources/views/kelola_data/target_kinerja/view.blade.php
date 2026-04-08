@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-medium">Detail Target Kinerja</h2>
            <p class="text-sm text-gray-600">Detail dan informasi target kinerja</p>
        </div>
        <div class="flex items-center gap-2">
            @include('kelola_data.parts.target_kinerja_toolbar')
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white p-4 rounded-lg shadow max-w-2xl">
        <div class="mb-4">
            <h3 class="font-semibold">Nama</h3>
            <p>{{ $targetKinerja->nama }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Responsibility</h3>
            <p>{{ $targetKinerja->responsibility ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Satuan</h3>
            <p>{{ $targetKinerja->satuan ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Bobot</h3>
            <p>{{ $targetKinerja->bobot }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Keterangan</h3>
            <p>{{ $targetKinerja->keterangan ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Target (%)</h3>
            <p>{{ $targetKinerja->target_percent ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Status</h3>
            <p>{{ $targetKinerja->status ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Unit Penanggung Jawab</h3>
            <p>{{ $targetKinerja->unit_penanggung_jawab ?? '-' }}</p>
        </div>

        {{-- Evidence moved to laporan (pelaporan_pekerjaan) --}}

        <div class="mb-4">
            <h3 class="font-semibold">Periode</h3>
            <p>{{ $targetKinerja->periode ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <h3 class="font-semibold">Active</h3>
            <p>{{ $targetKinerja->is_active ? 'Ya' : 'Tidak' }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('manage.target-kinerja.edit', $targetKinerja->id) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-yellow-400 text-xs font-medium rounded hover:brightness-95 transition"><i class="bi bi-pencil"></i> Edit</a>
            <a href="{{ route('manage.target-kinerja.list') }}" class="px-4 py-2 bg-gray-300 rounded">Kembali</a>
        </div>
    </div>
@endsection
