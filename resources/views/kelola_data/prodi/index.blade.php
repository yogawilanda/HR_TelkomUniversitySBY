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
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
            <button id="openCreateModal" type="button" class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah Prodi</span>
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

    {{-- Tabel Daftar Prodi --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Program Studi</h3>
            <div class="text-sm text-gray-600">
                Total: <span class="font-semibold">{{ $prodis->count() }}</span> Prodi
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">
                        No.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">
                        Kode
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">
                        Nama Program Studi
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">
                        Fakultas
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($prodis as $index => $prodi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center border">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border">
                            <span class="font-mono text-sm text-gray-900">{{ $prodi->kode }}</span>
                        </td>
                        <td class="px-4 py-3 border">
                            <div class="font-medium text-gray-900">{{ $prodi->position_name }}</div>
                        </td>
                        <td class="px-4 py-3 border">
                            <span class="text-gray-700">{{ $prodi->parent->position_name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center border">
                            <div class="flex gap-2 justify-center">
                                <button type="button"
                                    onclick="openDetailModal({{ json_encode($prodi->kode) }}, {{ json_encode($prodi->position_name) }}, {{ json_encode($prodi->parent->position_name ?? '-') }})"
                                    class="px-3 py-1.5 border border-[#1C2762] text-[#1C2762] rounded-md text-xs font-medium hover:bg-[#1C2762] hover:text-white transition duration-200">
                                    View Details
                                </button>
                                <a href="{{ route('manage.prodi.edit', $prodi->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition">
                                    <i class="bi bi-pencil-square"></i>
                                    Edit
                                </a>
                                <button onclick="confirmDelete('{{ $prodi->id }}', '{{ $prodi->position_name }}')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition">
                                    <i class="bi bi-trash"></i>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data program studi.
                            <a href="{{ route('manage.prodi.create') }}" class="text-blue-600 hover:underline">Tambah prodi
                                baru</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Info Box --}}
    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start gap-3">
            <i class="bi bi-info-circle-fill text-blue-600 text-xl"></i>
            <div class="flex-1">
                <h4 class="font-semibold text-blue-900 mb-2">Informasi</h4>
                <p class="text-sm text-blue-800">
                    Untuk melihat statistik dosen per prodi (Pendidikan, Jabatan Fungsional, Kepegawaian),
                    silakan buka menu <strong><a href="{{ route('manage.jenjang-pendidikan.list') }}" class="route_pop_up">"Dashboard Prodi"</a></strong> di sidebar.
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
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(prodiId, prodiName) {
            document.getElementById('deleteProdiName').textContent = prodiName;
            document.getElementById('deleteForm').action = `/manage/prodi/${prodiId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>

    <!-- Include Create Modal -->
    @include('kelola_data.prodi.create')
    <!-- Include Detail (Show) Modal -->
    @include('kelola_data.prodi.show')
@endsection
