@extends('kelola_data.base')

@section('header-base')
    <style>
        .max-w-100 {
            max-width: 100% !important;
        }

        .nav-active {
            background-color: #0070ff;

            span {
                color: white;
            }
        }
    </style>
@endsection

@section('page-name')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Fakultas</span>
            </div>
            <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">
                Anda dapat melihat semua fakultas yang terdaftar di sistem disini
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
            <button id="openCreateModal" type="button" class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </button>
        </div>
    </div>
@endsection

@section('content-base')
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
    {{-- {{ dd(session('error')) }} --}}
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        <x-tb id="fakultasTable">
            <x-slot:put_something>
                <div class="flex items-center gap-2 h-full">
                    <x-print-tb target_id="fakultasTable"></x-print-tb>
                    <x-export-csv-tb target_id="fakultasTable"></x-export-csv-tb>
                </div>
            </x-slot:put_something>
            <x-slot:table_header>
                <x-tb-td nama="kode">Kode</x-tb-td>
                <x-tb-td nama="nama">Nama Fakultas</x-tb-td>
                <x-tb-td nama="jumlah_prodi">Jumlah Prodi</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @forelse ($fakultas as $index => $f)
                    <x-tb-cl id="{{ $f->id }}">
                        <x-tb-cl-fill>{{ $f->kode }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $f->position_name }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $f->prodi_count ?? 0 }} Prodi</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                @if (session('account')['is_admin'] == 1)
                                    <!-- Edit Button -->
                                    <a href="{{ route('manage.fakultas.edit', $f->id) }}" data-bs-container="body"
                                        data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="hover"
                                        data-bs-content="Edit Fakultas"
                                        class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-[#f9fafb] transition duration-150 ease-in-out">
                                        <i class="bi bi-pencil text-[#0070ff] text-[14px]"></i>
                                    </a>
                                @endif

                                <!-- View Details Button -->
                                <button type="button"
                                    onclick="openDetailModal('{{ $f->kode }}', '{{ addslashes($f->position_name) }}', {{ $f->children_count ?? 0 }})"
                                    class="px-3 py-1.5 border border-[#1C2762] text-[#1C2762] rounded-md text-xs font-medium hover:bg-[#1C2762] hover:text-white transition duration-200">
                                    View Details
                                </button>

                                @php
                                    $cek = isset($f->prodi_count) && $f->prodi_count>0
                                @endphp
                                <a @if($cek==true) href="{{ route('manage.prodi.index',['fakultas'=>$f->position_name]) }}" @endif
                                    class="px-3 py-1.5 border @if($cek==false) opacity-50 @endif border-[#1C2762] text-[#1C2762] rounded-md text-xs font-medium hover:bg-[#1C2762] hover:text-white transition duration-200">
                                    Daftar Prodi Terkait
                                </a>

                                {{-- @if (session('account')['is_admin'] == 1)
                                    <!-- Delete Button -->
                                    <button type="button"
                                        onclick="openDeleteFakultasModal('{{ $f->id }}', '{{ addslashes($f->position_name) }}', '{{ route('manage.fakultas.destroy', $f->id) }}')"
                                        data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top"
                                        data-bs-trigger="hover" data-bs-content="Hapus Fakultas"
                                        class="flex items-center justify-center w-7 h-7 rounded-md border border-[#d0d5dd] bg-white hover:bg-red-50 transition duration-150 ease-in-out">
                                        <i class="bi bi-trash text-red-600 text-[14px]"></i>
                                    </button>
                                @endif --}}
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data fakultas
                        </td>
                    </tr>
                @endforelse
            </x-slot:table_column>
        </x-tb>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $fakultas->links('vendor.pagination.custom') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            [...popoverTriggerList].map(el => new bootstrap.Popover(el));
        });
    </script>

    <!-- Include Create Modal -->
    @include('kelola_data.fakultas.create')

    <!-- Include Detail Modal -->
    @include('kelola_data.fakultas.show')

    <!-- Include Delete Modal -->
    @include('kelola_data.fakultas.delete')
@endsection
