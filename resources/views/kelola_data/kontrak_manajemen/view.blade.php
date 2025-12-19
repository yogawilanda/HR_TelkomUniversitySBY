@php
    $active_sidebar = 'Kontrak Manajemen';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Detail Kontrak Manajemen</span>
            </div>
            <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Informasi kontrak manajemen</span>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white rounded-lg shadow p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Informasi Kontrak</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Nama Kontrak</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->nama }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Bobot</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->bobot }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <p class="text-gray-900">
                        <span class="px-2 py-1 rounded text-xs 
                            @if($kontrakManajemen->status === 'active') bg-green-100 text-green-800 
                            @elseif($kontrakManajemen->status === 'completed') bg-blue-100 text-blue-800 
                            @else bg-gray-100 text-gray-800 
                            @endif">
                            {{ ucfirst($kontrakManajemen->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Periode</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->periode ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Unit Penanggung Jawab</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->unit_penanggung_jawab ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Target Percent</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->target_percent ?? '-' }}%</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tanggal Mulai</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->start ? $kontrakManajemen->start->format('d-m-Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tanggal Selesai</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->end ? $kontrakManajemen->end->format('d-m-Y') : '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600">Keterangan</label>
                    <p class="text-gray-900">{{ $kontrakManajemen->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Kontrak Unit</h2>
                <a href="{{ route('kelola.kontrak-unit.input') }}?kontrak_manajemen_id={{ $kontrakManajemen->id }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                    <i class="bi bi-plus"></i> Tambah Kontrak Unit
                </a>
            </div>
            
            @if($kontrakManajemen->kontrakUnit->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pekerjaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($kontrakManajemen->kontrakUnit as $unit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->nama_unit }}</td>
                                    <td class="px-6 py-4">{{ Str::limit($unit->pekerjaan, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->jumlah ?? 0 }} {{ $unit->result ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($unit->kinerjaUnit)
                                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                                {{ ucfirst($unit->kinerjaUnit->status) }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('kelola.kontrak-unit.view', $unit->id) }}" 
                                            class="text-blue-600 hover:text-blue-800 mr-3">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('kelola.kontrak-unit.edit', $unit->id) }}" 
                                            class="text-yellow-600 hover:text-yellow-800">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada kontrak unit</p>
            @endif
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('kelola.kontrak-manajemen.list') }}" 
                class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                Kembali
            </a>
            <a href="{{ route('kelola.kontrak-manajemen.edit', $kontrakManajemen->id) }}" 
                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition">
                Edit
            </a>
        </div>
    </div>
@endsection
