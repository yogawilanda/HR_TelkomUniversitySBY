@php
    $active_sidebar = 'Daftar Studi Lanjut';
@endphp

@extends('kelola_data.base')

@section('header-base')
    <style>
        .max-w-100 { max-width: 100% !important; }

        /* Row Hover Effect */
        tr.hover-row:hover {
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        /* Status Badge Styling */
        .status-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid;
            letter-spacing: 0.3px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            position: relative;
        }

        /* Glow effect untuk dot */
        .status-dot::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            opacity: 0.3;
            background: inherit;
            filter: blur(1px);
        }

        /* Action Button */
        .btn-icon-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
            color: #64748b;
        }
    </style>
@endsection

@section('page-name')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full px-6 py-6 bg-white border-b border-gray-100">
        <div>
            <h1 class="font-bold text-2xl tracking-tight text-gray-900">Studi Lanjut</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi pendidikan lanjutan pegawai.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-1 shadow-sm">
                <x-print-tb target_id="studiLanjutTable"></x-print-tb>
                <div class="w-px h-5 bg-gray-200 mx-1"></div>
                <x-export-csv-tb target_id="studiLanjutTable"></x-export-csv-tb>
            </div>

            <a href="{{ route('manage.studi-lanjut.input') }}"
               class="flex items-center gap-2 bg-[#0070ff] hover:bg-[#005fe0] text-white px-4 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm active:scale-95">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Data</span>
            </a>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="px-6 py-8">
        <div class="overflow-hidden">
            <x-tb id="studiLanjutTable">
                <x-slot:table_header>
                    <x-tb-td nama="nama_pegawai" sorting=true>Nama Pegawai</x-tb-td>
                    <x-tb-td type="select" nama="negara" sorting=true>Negara</x-tb-td>
                    <x-tb-td nama="studi" sorting=true>Jenjang & Prodi</x-tb-td>
                    <x-tb-td type="select" sorting=true>Universitas</x-tb-td>
                    <x-tb-td type="select" nama="status" sorting=true>Status</x-tb-td>
                    <x-tb-td nama="tanggal_mulai" sorting=true>Mulai</x-tb-td>
                    <x-tb-td nama="action" class="text-center">Aksi</x-tb-td>
                </x-slot:table_header>

                <x-slot:table_column>
                    @foreach($studiLanjut as $item)
                        <x-tb-cl id="{{ $item->id }}" class="hover-row">

                            <x-tb-cl-fill>
                                <span class="font-semibold text-gray-900">{{ $item->user->nama_lengkap ?? 'N/A' }}</span>
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[11px] font-bold border border-gray-200 uppercase tracking-wide">
                                        {{ $item->negara }}
                                    </span>
                                </div>
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-blue-600 uppercase">{{ $item->jenjang }}</span>
                                    <span class="text-sm text-gray-700 leading-tight">{{ $item->program_studi }}</span>
                                </div>
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                <span class="text-sm text-gray-500 italic">{{ $item->universitas }}</span>
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                @if($item->status == 'Sedang Berjalan')
                                    <div class="status-wrapper bg-blue-50 text-blue-700 border-blue-100">
                                        <span class="status-dot bg-blue-600"></span>
                                        {{ $item->status }}
                                    </div>
                                @elseif($item->status == 'Selesai')
                                    <div class="status-wrapper bg-emerald-50 text-emerald-700 border-emerald-100">
                                        <span class="status-dot bg-emerald-600"></span>
                                        {{ $item->status }}
                                    </div>
                                @else
                                    <div class="status-wrapper bg-amber-50 text-amber-700 border-amber-100">
                                        <span class="status-dot bg-amber-600"></span>
                                        {{ $item->status }}
                                    </div>
                                @endif
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                <span class="text-sm text-gray-600 font-medium">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                </span>
                            </x-tb-cl-fill>

                            <x-tb-cl-fill>
                                <div class="flex gap-1 justify-center">
                                    <a href="{{ route('manage.studi-lanjut.view', $item->id) }}"
                                       class="btn-icon-action hover:bg-blue-50 hover:text-blue-600" title="View">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('manage.studi-lanjut.edit', $item->id) }}"
                                       class="btn-icon-action hover:bg-emerald-50 hover:text-emerald-600" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    {{-- <form action="{{ route('manage.studi-lanjut.destroy', $item->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus data studi {{ $item->user->nama_lengkap }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-action hover:bg-red-50 hover:text-red-600" title="Hapus">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form> --}}
                                </div>
                            </x-tb-cl-fill>
                        </x-tb-cl>
                    @endforeach
                </x-slot:table_column>
            </x-tb>
        </div>
    </div>
@endsection
