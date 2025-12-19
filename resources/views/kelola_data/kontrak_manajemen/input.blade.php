@php
    $active_sidebar = 'Kontrak Manajemen';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Input Kontrak Manajemen</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Tambah kontrak manajemen baru</span>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('manage.kontrak-manajemen.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kontrak *</label>
                    <input type="text" name="nama" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('nama') }}">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bobot</label>
                    <input type="number" name="bobot" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('bobot', 0) }}">
                    @error('bobot')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsibility</label>
                    <input type="text" name="responsibility" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('responsibility') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Penanggung Jawab</label>
                    <input type="text" name="unit_penanggung_jawab" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('unit_penanggung_jawab') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                    <input type="text" name="satuan" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('satuan') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Percent (%)</label>
                    <input type="number" step="0.01" name="target_percent" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('target_percent') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <input type="text" name="periode" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('periode') }}" placeholder="e.g. 2025">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('start') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" name="end" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('end') }}">
                </div>
            </div>

            <div class="mb-4 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('manage.kontrak-manajemen.list') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
