<!-- Detail Modal (Tailwind - Style Sama dengan Tambah Fakultas) -->
<div id="detailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div id="detailModalBackdrop" class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden z-10">
        <!-- Header -->
        <div class="text-center px-6 pt-6">
            <h2 class="text-3xl font-semibold text-gray-900">Detail Prodi</h2>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- Kode Prodi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Prodi</label>
                <div id="detail_kode_prodi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-800">
                    -
                </div>
            </div>

            <!-- Nama Prodi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Prodi</label>
                <div id="detail_nama_prodi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-800">
                    -
                </div>
            </div>

            <!-- Fakultas -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Fakultas</label>
                <div id="detail_fakultas"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-800">
                    -
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-center mt-6">
                <button type="button" id="closeDetailModalBtn"
                    class="w-full py-2 rounded-md bg-red-500 text-white font-medium hover:bg-red-600 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Detail Modal logic
    function openDetailModal(kode, namaProdi, fakultas) {
        document.getElementById('detail_kode_prodi').textContent = kode || '-';
        document.getElementById('detail_nama_prodi').textContent = namaProdi || '-';
        document.getElementById('detail_fakultas').textContent = fakultas || '-';

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Event listeners
    (function() {
        const closeModalBtn = document.getElementById('closeDetailModalBtn');
        const backdrop = document.getElementById('detailModalBackdrop');

        if (closeModalBtn) closeModalBtn.addEventListener('click', hideDetailModal);
        if (backdrop) backdrop.addEventListener('click', hideDetailModal);

        // Close on ESC
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') hideDetailModal();
        });
    })();
</script>
