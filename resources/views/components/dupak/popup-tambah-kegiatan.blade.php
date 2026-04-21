@props(['kegiatanUtama' => [], 'pengajuanId' => null])

<div id="add-kegiatan-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

	<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
		<!-- Overlay Background -->
		<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

		<!-- <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span> -->

		<!-- Modal Panel -->
		<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
			<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

				<!-- Modal Header -->
				<div class="flex justify-between items-center pb-3 border-b border-gray-200">
					<h3 class="text-xl font-semibold text-gray-900" id="modal-title">
						Tambah Kegiatan Baru (DUPAK)
					</h3>
					<button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="closeModal()">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					</button>
				</div>

				<!-- Modal Body (Form) -->
				<!-- route : kegiatan.store -->
				<form id="kegiatan-form" action="#" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
					@csrf

					<div>
						<label for="kategori" class="block text-sm font-medium text-gray-700">Kategori Kegiatan</label>
						<select id="kategori" name="kategori" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm">
							<option value="">Pilih Kategori</option>
							@foreach ($kegiatanUtama as $utama)
								<option value="{{ $utama->id }}">{{ $utama->nama }}</option>
							@endforeach
						</select>
					</div>

					<div>
						<label for="idKomponen" class="block text-sm font-medium text-gray-700">Detail Kegiatan (Komponen)</label>
						<select id="idKomponen" name="idKomponen" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm">
							<!-- jika belum termuat dari server isinya  maka isi ini dengan default value-->
							<option value="">Pilih Kategori Terlebih Dahulu</option>
						</select>
					</div>
<!-- 
					<div>
						<label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
						<input type="date" name="tanggal_mulai" id="tanggal_mulai" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 p-2 border">
					</div>

					<div>
						<label for="file_bukti" class="block text-sm font-medium text-gray-700">Unggah File Bukti (PDF/Gambar)</label>
						<input type="text" name="file_bukti" id="file_bukti" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 p-2 border" placeholder="contoh bitly/MK23_pengajuan_rangga">
					</div> -->

					<!-- Footer Tombol -->
					<div class="mt-6 flex justify-end gap-3">
						<button type="button" onclick="closeModal()" class="py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
							Batal
						</button>
						<button type="submit" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-900 hover:bg-blue-950 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
							Simpan Kegiatan
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	/**
	 * Mengatur fungsi untuk membuka modal.
	 */
	function openModal() {
		const modal = document.getElementById('add-kegiatan-modal');
		modal.classList.remove('hidden');
		document.body.style.overflow = 'hidden'; // Mencegah scroll pada body saat modal terbuka
	}

	/**
	 * Mengatur fungsi untuk menutup modal.
	 */
	function closeModal() {
		const modal = document.getElementById('add-kegiatan-modal');
		modal.classList.add('hidden');
		document.body.style.overflow = ''; // Mengembalikan scroll pada body
	}

	/**
	 * Data dari server untuk dropdown dinamis.
	 */
	const kegiatanUtamaData = @json($kegiatanUtama);

	/**
	 * ID Pengajuan yang sedang aktif diambil dari props.
	 */
	const currentPengajuanId = "{{ $pengajuanId }}";

	/**
	 * Handle perubahan kategori utama untuk memperbarui list komponen (detail kegiatan).
	 */
	document.getElementById('kategori').addEventListener('change', function() {
		const selectedUtamaId = this.value;
		const komponenSelect = document.getElementById('idKomponen');
		
		// Reset opsi komponen
		komponenSelect.innerHTML = '<option value="">Pilih Komponen</option>';

		if (selectedUtamaId) {
			const utama = kegiatanUtamaData.find(item => item.id == selectedUtamaId);
			if (utama && utama.komponens) {
				utama.komponens.forEach(komp => {
					const option = document.createElement('option');
					option.value = komp.id;
					option.textContent = komp.nama;
					komponenSelect.appendChild(option);
				});
			}
		} else {
			komponenSelect.innerHTML = '<option value="">Pilih Kategori Terlebih Dahulu</option>';
		}
	});

	/**
	 * Menangani pengiriman form dan melakukan redirect berdasarkan kategori.
	 */
	document.getElementById('kegiatan-form').addEventListener('submit', function(e) {
		e.preventDefault();

		const kategoriId = document.getElementById('kategori').value;
		const komponenId = document.getElementById('idKomponen').value;
		const utama = kegiatanUtamaData.find(item => item.id == kategoriId);

		if (utama && currentPengajuanId) {
			const name = utama.nama.toLowerCase();
			let path = '';

			if (name.includes('pendidikan')) path = 'pendidikan';
			else if (name.includes('penelitian')) path = 'penelitian';
			else if (name.includes('pengabdian')) path = 'pengabdian';
			else if (name.includes('penunjang')) path = 'penunjang';

			if (path && komponenId) {
				// Redirect sesuai dengan struktur route di web.php
				window.location.href = `/dupak/detil_pengajuan/${path}/${currentPengajuanId}?komponen_id=${komponenId}`;
			} else {
				alert('ID Komponen tidak ditemukan. Silakan pilih komponen terlebih dahulu.');
			}
		} else if (!currentPengajuanId) {
			alert('ID Pengajuan tidak ditemukan. Silakan buat pengajuan terlebih dahulu.');
		}
	});

	// Menutup modal jika tombol ESC ditekan
	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			closeModal();
		}
	});
</script>