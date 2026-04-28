@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col pt-16 px-4 pb-12">
    <div class="mx-auto max-w-3xl w-full flex-grow">

        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            {{ session('error') }}
        </div>
        @endif

        <!-- Header Section -->
        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-4">
            <a href="{{ route('dupak.pengajuan.show', $pengajuan->id) }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail
            </a>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Pengajuan DUPAK</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Perbarui informasi pengajuan kenaikan jabatan.</p>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('dupak.pengajuan.update', $pengajuan->id) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pengajuan</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Periode Awal -->
                    <div>
                        <label for="start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode Awal</label>
                        <input type="date" name="start" id="start" value="{{ old('start', $pengajuan->start) }}"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                        @error('start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Periode Akhir -->
                    <div>
                        <label for="end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode Akhir</label>
                        <input type="date" name="end" id="end" value="{{ old('end', $pengajuan->end) }}"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                        @error('end')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Ajaran Awal -->
                    <div>
                        <label for="TahunAjaranAjuanAwal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran Awal</label>
                        <input type="text" name="TahunAjaranAjuanAwal" id="TahunAjaranAjuanAwal" value="{{ old('TahunAjaranAjuanAwal', $pengajuan->TahunAjaranAjuanAwal) }}" placeholder="Contoh: 2024/2025"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                        @error('TahunAjaranAjuanAwal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Ajaran Akhir -->
                    <div>
                        <label for="TahunAjaranAjuanAkhir" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran Akhir</label>
                        <input type="text" name="TahunAjaranAjuanAkhir" id="TahunAjaranAjuanAkhir" value="{{ old('TahunAjaranAjuanAkhir', $pengajuan->TahunAjaranAjuanAkhir) }}" placeholder="Contoh: 2024/2025"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                        @error('TahunAjaranAjuanAkhir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Semester Ajuan -->
                    <div class="md:col-span-2">
                        <label for="semesterAjuan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester Ajuan</label>
                        <select name="semesterAjuan" id="semesterAjuan"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semesterAjuan', $pengajuan->semesterAjuan) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semesterAjuan', $pengajuan->semesterAjuan) == 'Genap' ? 'selected' : '' }}>Genap</option>
                            <option value="Ganjil & Genap" {{ old('semesterAjuan', $pengajuan->semesterAjuan) == 'Ganjil & Genap' ? 'selected' : '' }}>Ganjil & Genap</option>
                        </select>
                        @error('semesterAjuan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Info Read-only -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Tetap</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="font-semibold ml-2">{{ $pengajuan->status }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">JFA Asal:</span>
                        <span class="font-semibold ml-2">{{ $pengajuan->jabatanAsal->nama_jabatan ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">JFA Tujuan:</span>
                        <span class="font-semibold ml-2">{{ $pengajuan->jabatanTujuan->nama_jabatan ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Dibuat:</span>
                        <span class="font-semibold ml-2">{{ $pengajuan->created_at->format('d F Y') }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4 italic">* Informasi di atas tidak dapat diubah.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('dupak.pengajuan.show', $pengajuan->id) }}"
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-950 transition font-semibold">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

