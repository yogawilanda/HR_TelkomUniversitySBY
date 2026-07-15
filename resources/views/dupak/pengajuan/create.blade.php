@extends('layouts.app')

@section('content')
<div class="py-6 min-h-screen bg-gray-50/50 transition-all duration-300">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali ala Dasbor -->
        <a href="{{ route('dupak.pengajuan.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-4 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>

        <!-- Header Page (Gaya Dasbor) -->
        <div class="bg-white shadow rounded-t-lg p-6 pb-5 border-b border-gray-100">
            <h1 class="text-2xl font-semibold text-gray-900">Formulir Pengajuan DUPAK</h1>
            <p class="mt-1 text-sm text-gray-500">Daftar Usulan Penetapan Angka Kredit Jabatan Fungsional Dosen</p>
        </div>

        <!-- Body Form Card -->
        <div class="bg-white shadow rounded-b-lg p-6 sm:p-8">
            <form method="POST" action="{{ route('dupak.pengajuan.store') }}" class="space-y-8">
                @csrf

                <!-- Section: Informasi Dasar -->
                <div class="space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-lg font-semibold text-blue-900 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> Informasi Dasar
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Informasi identitas kepegawaian Anda saat ini.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="nidn" class="block text-sm font-medium text-gray-700 mb-1.5">NIDN</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <!-- FontAwesome Lock Icon matching dashboard style -->
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <!-- Menggunakan pl-10 agar teks bergeser aman kanan dari posisi ikon -->
                                <input type="text" name="nidn" id="nidn" value="{{ $nidn ?? 'NIDN Tidak Ditemukan' }}"
                                    class="block w-full rounded-lg border border-gray-200 bg-gray-50/70 pl-10 pr-3 py-2.5 text-sm text-gray-500 focus:outline-none cursor-not-allowed"
                                    readonly>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400 italic">Otomatis diambil dari data Anda terkini.</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Jabatan Fungsional -->
                <div class="space-y-5 pt-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-lg font-semibold text-blue-900 flex items-center">
                            <i class="fas fa-graduation-cap mr-2"></i> Status Jabatan Fungsional Akademik
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Pemetaan jabatan fungsional lama ke target jabatan baru.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Current Position -->
                        <div>
                            <label for="current_position" class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan Fungsional Saat Ini</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="text" name="current_position" id="current_position" value="{{ $jabatan_fungsional ?? 'Belum ada' }}"
                                    class="block w-full rounded-lg border border-gray-200 bg-gray-50/70 pl-10 pr-3 py-2.5 text-sm text-gray-500 focus:outline-none cursor-not-allowed"
                                    readonly>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400 italic">Otomatis diambil dari data Anda terkini.</p>
                        </div>

                        <!-- Target Position -->
                        <div>
                            <label for="target_position" class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan Fungsional Yang Dituju</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <!-- Aksen panah/target menggunakan warna Biru sesuai ekosistem -->
                                    <i class="fas fa-arrow-right text-blue-600"></i>
                                </div>
                                <input type="text" name="target_position" id="target_position" value="{{ $jfa_tujuan ?? 'Belum ada' }}"
                                    class="block w-full rounded-lg border border-blue-100 bg-blue-50/20 pl-10 pr-3 py-2.5 text-sm text-blue-900 font-semibold focus:outline-none cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Gaya Biru/Blue-900) -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('dupak.pengajuan.index') }}" 
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-all duration-150">
                        Batal
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center justify-center rounded-lg border border-transparent bg-blue-900 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-950 transition-all duration-150">
                        Simpan Pengajuan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection