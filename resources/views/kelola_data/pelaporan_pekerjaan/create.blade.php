@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-medium">Isi Laporan Pekerjaan</h2>
            <p class="text-sm text-gray-600">{{ $target->pekerjaan }}</p>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white p-4 rounded shadow max-w-2xl">
        <form action="{{ route('manage.target-kinerja.harian.submit-report', $target->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Realisasi</label>
                <textarea name="realisasi" rows="4" class="w-full border rounded px-3 py-2">{{ old('realisasi') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Realisasi Jumlah</label>
                    <input type="number" name="realisasi_jumlah" value="{{ old('realisasi_jumlah') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Realisasi Waktu (menit)</label>
                    <input type="number" name="realisasi_waktu_minutes" value="{{ old('realisasi_waktu_minutes') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Pencapaian (%)</label>
                    <input type="number" name="pencapaian_percent" value="{{ old('pencapaian_percent') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Evidence (link)</label>
                    <input type="text" name="evidence" value="{{ old('evidence') }}" class="w-full border rounded px-3 py-2" placeholder="https://...">
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Kirim</button>
                <a href="{{ route('manage.target-kinerja.harian.list') }}" class="px-4 py-2 bg-gray-300 rounded">Batal</a>
            </div>
        </form>
    </div>
@endsection
