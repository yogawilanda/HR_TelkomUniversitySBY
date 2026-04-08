@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Tambah Target Kinerja Harian</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Buat target harian baru</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <div class="hidden sm:flex items-center gap-2">
                @include('kelola_data.parts.target_kinerja_toolbar')
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="max-w-2xl bg-white p-4 rounded shadow">
        <form action="{{ route('manage.target-kinerja.harian.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Pekerjaan</label>
                <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" class="w-full border rounded px-3 py-2" required>
                @error('pekerjaan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Target Kinerja (opsional)</label>
                <select name="target_kinerja_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih --</option>
                    @foreach($targets as $t)
                        <option value="{{ $t->id }}">{{ $t->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Kontrak Type</label>
                    <select name="kontrak_type" class="w-full border rounded px-3 py-2">
                        <option value="">-- Pilih --</option>
                        <option value="institusi" {{ old('kontrak_type')=='institusi' ? 'selected' : '' }}>Institusi</option>
                        <option value="unit" {{ old('kontrak_type')=='unit' ? 'selected' : '' }}>Unit</option>
                        <option value="pribadi" {{ old('kontrak_type')=='pribadi' ? 'selected' : '' }}>Pribadi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Hasil / Result</label>
                    <input type="text" name="result" value="{{ old('result') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Jumlah</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Waktu (menit)</label>
                    <input type="number" name="waktu_minutes" value="{{ old('waktu_minutes') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Bobot</label>
                    <input type="number" name="bobot" value="{{ old('bobot') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm">Aktifkan target harian</label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start</label>
                    <input type="datetime-local" name="start" value="{{ old('start') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End</label>
                    <input type="datetime-local" name="end" value="{{ old('end') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                <a href="{{ route('manage.target-kinerja.harian.list') }}" class="px-4 py-2 bg-gray-300 rounded">Batal</a>
            </div>
        </form>
    </div>
@endsection
