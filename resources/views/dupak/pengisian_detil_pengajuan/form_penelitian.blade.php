@extends('layouts.app')

@section('content')

<div class="mt-16 md:ml-64 sm:ml-12 lg:ml-64">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <a href="{{ route('dupak.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>
                <h1 class="mb-3 text-2xl font-semibold">Formulir Detil Pengajuan DUPAK</h1>
                <h2 class="text-xl">Daftar Usulan Penetapan Angka Kredit</h2>

                <!-- {{ route('dupak.pengajuan.store') }} -->
                <form method="POST" action="" class="space-y-6">
                    @csrf

                    <!-- Unsur Utama -->
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h2 class="mb-4 text-2xl font-medium text-gray-900">Unsur Utama: Penelitian</h2>

                        <!-- Bagian 1: Informasi Karya Ilmiah -->
                        <div class="mb-6">
                            <h3 class="mb-3 font-bold text-lg text-gray-800">1. Informasi Karya Ilmiah</h3>
                            <div class="space-y-4">
                                <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">Judul Penelitian</label>
                                            <input type="text" name="judul" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
                                            <input type="text" name="nama" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Penulis</label>
                                            <input type="text" name="penulis" placeholder="Contoh: Penulis Mandiri / Penulis Pertama" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tanggal Publikasi</label>
                                            <input type="date" name="tanggalPublikasi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status Syarat Utama</label>
                                            <select name="statusSyaratUtama" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="0">Bukan Syarat Utama</option>
                                                <option value="1">Syarat Utama</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 2: Atribut Publikasi -->
                        <div class="mb-6">
                            <h3 class="mb-3 font-bold text-lg text-gray-800">2. Atribut Publikasi</h3>
                            <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Nama Jurnal / Seminar</label>
                                        <input type="text" name="namaJurnal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jenis Publikasi</label>
                                        <input type="text" name="jenisPublikasi" placeholder="Contoh: Jurnal Internasional" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Penerbit / Penyelenggara</label>
                                        <input type="text" name="penerbitpenyelenggara" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Akreditasi / Peringkat</label>
                                        <input type="text" name="akre" placeholder="Contoh: Q1 / Sinta 2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ISSN</label>
                                        <input type="text" name="issn" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Volume</label>
                                        <input type="text" name="vol" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nomor</label>
                                        <input type="text" name="no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Halaman</label>
                                        <input type="text" name="halaman" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 3: Hasil Cek Kemiripan (Plagiarisme) -->
                        <div class="mb-6">
                            <h3 class="mb-3 font-bold text-lg text-gray-800">3. Hasil Cek Kemiripan (%)</h3>
                            <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Similarity (Include)</label>
                                        <input type="number" step="0.01" name="similarityInclude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Similarity (Exclude)</label>
                                        <input type="number" step="0.01" name="similarityExclude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Similarity AI</label>
                                        <input type="number" step="0.01" name="similarityAI" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">Link Bukti Similarity (Drive/URL)</label>
                                        <input type="url" name="LinksimilarityInclude" placeholder="Link Laporan Turnitin/Similarity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 4: Tautan & Bukti Fisik -->
                        <div class="mb-6">
                            <h3 class="mb-3 font-bold text-lg text-gray-800">4. Tautan Bukti Fisik</h3>
                            <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm space-y-3">
                                <input type="url" name="Link1" placeholder="Link Artikel / Jurnal (Wajib)" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <input type="url" name="LinkKorespondensi" placeholder="Link Bukti Korespondensi (Jika ada)" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="url" name="Link2" placeholder="Link Bukti Pendukung Lain 1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <input type="url" name="Link3" placeholder="Link Bukti Pendukung Lain 2" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <label class="block text-sm font-medium text-gray-700 mt-2">Rincian / Catatan Tambahan</label>
                                <textarea name="rincian" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="button" onclick="window.history.back()" class="px-4 py-2 mr-4 text-white bg-gray-500 rounded-md hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Simpan Data Penelitian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection