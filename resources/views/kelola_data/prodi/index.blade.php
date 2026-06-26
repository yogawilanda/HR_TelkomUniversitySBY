@extends('kelola_data.base')

@section('page-name')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Program Studi</span>
            </div>
            <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">
                Kelola data program studi (Tambah, Edit, Hapus)
            </span>
        </div>
        @if (session('account')['is_admin'] == 1)
            <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
                <button id="openCreateModal" type="button" class="flex rounded-[5.874740123748779px]">
                    <div
                        class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                        <i class="bi bi-plus text-sm text-white"></i>
                        <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah Prodi</span>
                    </div>
                </button>
            </div>
        @endif
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

    {{-- Tabel Daftar Prodi --}}
    <div class="overflow-x-auto gap-3">
        <div class="flex justify-between mb-4 items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Program Studi</h3>
            <div class="text-sm text-gray-600 scale-150">
                Total: <span class="font-semibold">{{ $prodis->count() }}</span> Prodi
            </div>
        </div>


        <x-tb id="prodiTable">
            <x-slot:put_something>
                <div class="flex items-center gap-2 h-full">
                    <x-print-tb target_id="prodiTable"></x-print-tb>
                    <x-export-csv-tb target_id="prodiTable"></x-export-csv-tb>
                </div>
            </x-slot:put_something>
            <x-slot:table_header>
                <x-tb-td nama="kode">Kode</x-tb-td>
                <x-tb-td nama="nama" sorting="true">Nama Program Studi</x-tb-td>
                <x-tb-td type="select" nama="fakultas" sorting="true">Nama Fakultas</x-tb-td>
                <x-tb-td nama="action">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @forelse ($prodis as $index => $prodi)
                    <x-tb-cl id="{{ $prodi->id }}">
                        <x-tb-cl-fill>{{ $prodi->data_prodi->kode }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $prodi->data_prodi->position_name }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $prodi->fakultas->position_name ?? '-' }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center w-full">
                                <div class="flex flex-wrap items-center justify-center gap-3 w-full">
                                    <!-- Tombol Aksi Utama (Detail & Edit) -->
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <button type="button"
                                            onclick="openDetailModal({{ json_encode($prodi->kode) }}, {{ json_encode($prodi->position_name) }}, {{ json_encode($prodi->parent->position_name ?? '-') }})"
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100 shadow-sm transition-all duration-200">
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </button>

                                        <a href="{{ route('manage.prodi.edit', $prodi->id) }}"
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-sm font-medium border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200 shadow-sm transition-all duration-200">
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </a>
                                    </div>

                                    <!-- Dropdown Menu -->
                                    <div class="dropdown flex items-center justify-center">
                                        <button type="button"
                                            class="inline-flex items-center justify-center p-1.5 bg-white border border-slate-300 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-100 shadow-sm transition-all duration-200"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-fw"></i>
                                        </button>

                                        <ul
                                            class="dropdown-menu border-0 shadow-lg rounded-xl mt-2 py-2 min-w-[200px] text-sm overflow-hidden">
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2 px-4 py-2.5 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors route_pop_up"
                                                    href="{{ route('manage.pegawai.list', ['destination' => 'Active', 'tipe' => 'Dosen', 'bagian' => $prodi->data_prodi->kode]) }}">
                                                    <i class="bi bi-mortarboard text-slate-400"></i> Daftar Dosen
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
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

    </div>

    {{-- Info Box --}}
    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start gap-3">
            <i class="bi bi-info-circle-fill text-blue-600 text-xl"></i>
            <div class="flex-1">
                <h4 class="font-semibold text-blue-900 mb-2">Informasi</h4>
                <p class="text-sm text-blue-800">
                    Untuk melihat statistik dosen per prodi (Pendidikan, Jabatan Fungsional, Kepegawaian),
                    silakan buka menu <strong><a href="{{ route('manage.jenjang-pendidikan.list') }}"
                            class="route_pop_up">"Dashboard Prodi"</a></strong> di sidebar.
                </p>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="bi bi-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Hapus Program Studi</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus prodi <strong id="deleteProdiName"></strong>?
                    </p>
                    <p class="text-xs text-red-600 mt-2">
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
                <div class="flex gap-3 px-4 py-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Close modal when clicking outside
        document.getElementById('deleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>

    <!-- Include Create Modal -->
    @if (session('account')['is_admin'] == 1)
        @include('kelola_data.prodi.create')
    @endif
    <!-- Include Detail (Show) Modal -->
    @include('kelola_data.prodi.show')
@endsection

@push('script-under-base')
    @if (session('account')['is_admin'] == 1 && $errors->has('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ $errors->first('error') }}',
                confirmButtonText: 'Oke'
            });
        </script>
    @endif
@endpush
