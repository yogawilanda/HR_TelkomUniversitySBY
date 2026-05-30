@extends('kinerja_pegawai.base')

@section('page-name')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Laporan Target</h2>
        <p class="text-sm text-gray-500">Informasi mendalam mengenai realisasi target kinerja tertentu.</p>
    </div>
@endsection

@section('content-base')
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Target Kinerja #{{ $id ?? '0' }}</h2>
            <p class="text-sm text-gray-500 mt-1">Status Laporan: <span class="font-bold text-blue-600 uppercase">Dalam Proses</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-l-4 border-blue-500 pl-3">Data Pencapaian</h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-sm font-medium text-gray-500">Target Semester</span>
                    <span class="text-sm font-semibold text-gray-700">100%</span>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-sm font-medium text-gray-500">Realisasi Saat Ini</span>
                    <span class="text-sm font-semibold text-gray-700">75%</span>
                </div>
            </div>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-l-4 border-emerald-500 pl-3">Efektivitas Kerja</h3>
            <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-4xl font-black text-emerald-600">92%</p>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-widest mt-1">Status: Optimal</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-6 rounded-md transition duration-150">Export Laporan</button>
    </div>
</div>
@endsection
