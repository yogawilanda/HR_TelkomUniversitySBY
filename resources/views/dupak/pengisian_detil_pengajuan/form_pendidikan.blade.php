<!-- FORM DETIL INPUT PENDIDIKAN -->

@extends('layouts.app')

@section('content')

<div class="mt-16 md:ml-64 sm:ml-12 lg:ml-64">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
			<!-- link ke dupak dashboard -->
		    <a href="{{ route('dupak.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>

                <h1 class="mb-3 text-2xl font-semibold">Formulir Detil Pengajuan DUPAK</h1>
                <h2 class="text-xl">Daftar Usulan Penetapan Angka Kredit</h2>

                <!-- {{ route('dupak.pengajuan.store') }} -->
                <form method="POST" action="" class="space-y-6">
                    @csrf

                    <!-- Unsur Utama -->
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h2 class="mb-4 text-2xl font-medium text-gray-900">Unsur Utama: Pendidikan</h2>

                        <!-- Pendidikan -->
                        <div class="mb-6">
                            <div class="space-y-4">
                                <!-- A. Pendidikan Formal -->
                                <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                    <label class="block mb-2 text-sm font-semibold text-gray-700">A. Pendidikan Formal (Ijazah)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-4">
                                            <select name="details[formal][idJenisInput]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">-- Pilih Jenjang --</option>
                                                <option value="101">Sarjana (S1)</option>
                                                <option value="104">Magister (S2) Linier</option>
                                                <option value="105">Magister (S2) Non-Linier</option>
                                                <option value="106">Doktor (S3) Linier</option>
                                                <option value="107">Doktor (S3) Non-Linier</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-6">
                                            <input type="text" name="details[formal][deskripsi_kegiatan]" placeholder="Nama PT, Program Studi, & Gelar" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <input type="number" step="0.01" name="details[formal][angka_kredit_total]" placeholder="AK" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="md:col-span-12">
                                            <input type="url" name="details[formal][link_bukti_pendukung]" placeholder="Link Bukti Ijazah (Google Drive/URL)" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>

                                <!-- B. Diklat Prajabatan -->
                                <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                    <label class="block mb-2 text-sm font-semibold text-gray-700">B. Diklat Prajabatan (STTPL)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-10">
                                            <input type="text" name="details[diklat][deskripsi_kegiatan]" placeholder="Nama Diklat Prajabatan" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <input type="number" step="0.01" name="details[diklat][angka_kredit_total]" placeholder="AK" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="md:col-span-12">
                                            <input type="url" name="details[diklat][link_bukti_pendukung]" placeholder="Link Bukti Sertifikat Diklat" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="button" onclick="window.history.back()" class="px-4 py-2 mr-4 text-white bg-gray-500 rounded-md hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Simpan Data Pendidikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection