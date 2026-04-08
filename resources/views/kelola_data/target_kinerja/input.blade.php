@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-medium">Tambah Target Kinerja</h2>
            <p class="text-sm text-gray-600">Buat target kinerja baru</p>
        </div>
        <div class="flex items-center gap-2">
            @include('kelola_data.parts.target_kinerja_toolbar')
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white p-4 rounded-lg shadow max-w-2xl">
        <form action="{{ route('manage.target-kinerja.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="w-full border rounded px-3 py-2" required>
                @error('nama')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Responsibility</label>
                <input type="text" name="responsibility" value="{{ old('responsibility') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Satuan</label>
                <input type="text" name="satuan" value="{{ old('satuan') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Bobot</label>
                <input type="number" name="bobot" value="{{ old('bobot', 0) }}" class="w-full border rounded px-3 py-2">
                @error('bobot')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Target (%)</label>
                <input type="number" name="target_percent" value="{{ old('target_percent') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih --</option>
                    <option value="institusi" {{ old('status') == 'institusi' ? 'selected' : '' }}>Institusi</option>
                    <option value="unit" {{ old('status') == 'unit' ? 'selected' : '' }}>Unit</option>
                    <option value="pribadi" {{ old('status') == 'pribadi' ? 'selected' : '' }}>Pribadi</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Unit Penanggung Jawab</label>
                <input type="text" name="unit_penanggung_jawab" value="{{ old('unit_penanggung_jawab') }}" class="w-full border rounded px-3 py-2">
            </div>

            {{-- Evidence moved to laporan (pelaporan_pekerjaan) --}}

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Periode</label>
                <input type="text" name="periode" value="{{ old('periode') }}" class="w-full border rounded px-3 py-2" placeholder="e.g. 2025 Q4">
            </div>



            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start</label>
                    <input type="date" name="start" value="{{ old('start') }}" class="w-full border rounded px-3 py-2" required>
                    @error('start')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End</label>
                    <input type="date" name="end" value="{{ old('end') }}" class="w-full border rounded px-3 py-2" required>
                    @error('end')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Keterangan</label>
                <textarea name="keterangan" class="w-full border rounded px-3 py-2">{{ old('keterangan') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="form-checkbox">
                    <span class="ml-2">Active</span>
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition"><i class="bi bi-save"></i> Simpan</button>
                <a href="{{ route('manage.target-kinerja.list') }}" class="px-4 py-2 bg-gray-300 rounded text-gray-700">Batal</a>
            </div>
        </form>
    </div>
@endsection
