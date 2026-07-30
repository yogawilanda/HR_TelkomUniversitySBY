<div x-data="{ openStep1: false, openStep2: false }" class="p-6 border rounded-lg bg-white shadow-sm border-l-4 border-l-blue-900">
	<h3 class="text-lg font-medium">Pengelolaan TPAK</h3>
	<p class="text-gray-600 mb-4 text-sm">Penunjukan TPAK</p>

	<!-- Tombol Pemicu Utama -->
	<button type="button" @click="openStep1 = true" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950 text-sm inline-block">
		Tambahkan TPAK Baru
	</button>

	<!-- POPUP STEP 1: Pilihan Jenis Penambahan -->
	<div x-show="openStep1" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @keydown.escape.window="openStep1 = false">
		<div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6 relative">
			<h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Pilih Metode Penambahan</h4>
			<p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Pilih bagaimana Anda ingin menambahkan TPAK baru ke dalam sistem.</p>

			<div class="space-y-3">
				<!-- Pilihan 1: Tambah Pengguna/Dosen Baru (Redirect Ke Halaman Buat Akun) -->
				<a href="{{ route('dupak.penunjukan_tpak.create_new_tpak') }}"
					class="flex items-center justify-between p-4 border rounded-lg hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 transition">
					<div>
						<div class="font-semibold text-sm text-gray-800 dark:text-gray-200">Buat Dosen / Pengguna Baru</div>
						<div class="text-xs text-gray-500">Mengarahkan ke halaman registrasi akun baru</div>
					</div>
					<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</a>

				<!-- Pilihan 2: Pilih Dari Dosen Yang Ada (Lanjut ke Modal Step 2) -->
				<button type="button" @click="openStep1 = false; openStep2 = true"
					class="w-full text-left flex items-center justify-between p-4 border rounded-lg hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 transition">
					<div>
						<div class="font-semibold text-sm text-gray-800 dark:text-gray-200">Pilih dari Dosen yang Ada</div>
						<div class="text-xs text-gray-500">Tunjuk dosen terpilih yang sudah terdaftar di sistem</div>
					</div>
					<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
					</svg>
				</button>
			</div>

			<div class="mt-6 text-right">
				<button type="button" @click="openStep1 = false" class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-300">
					Batal
				</button>
			</div>
		</div>
	</div>
	<!-- POPUP STEP 2: Form Pilih Dosen yang Ada -->
	<div x-show="openStep2" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @keydown.escape.window="openStep2 = false">
		<!-- Mengubah max-w-lg ke max-w-sm agar tidak terlalu lebar -->
		<div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-sm p-5 relative">
			<h4 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Pilih Dosen Terdaftar</h4>
			<form action="{{ route('dupak.penunjukan_tpak.store') }}" method="POST">
				@csrf
				<div class="space-y-3 mb-4">
					<!-- Custom Dropdown Alpine.js -->
					<div x-data="{ 
                    open: false, 
                    search: '', 
                    selectedId: '', 
                    selectedName: '-- Pilih Dosen --',
                    dosens: @js($dosens)
                }" class="relative">

						<!-- Input Hidden untuk Form Submission -->
						<input type="hidden" name="idDosenTpak" :value="selectedId" required>

						<label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">
							Dosen
						</label>

						<!-- Tombol Trigger Dropdown -->
						<button type="button"
							@click="open = !open"
							class="w-full text-left text-xs bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-blue-900">
							<span x-text="selectedName" :class="{'text-gray-400': !selectedId}"></span>
							<svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
							</svg>
						</button>

						<!-- List Floating Menu (Terbatas Max Height) -->
						<div x-show="open"
							@click.outside="open = false"
							x-cloak
							class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 text-xs">

							<!-- Input Search di Dalam Dropdown -->
							<div class="p-1.5 border-b border-gray-100 dark:border-gray-700">
								<input type="text"
									x-model="search"
									placeholder="Cari dosen..."
									class="w-full px-2 py-1 text-xs border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded focus:outline-none focus:ring-1 focus:ring-blue-900">
							</div>

							<!-- Daftar Opsi yang Dibatasi Tinggi (max-h-48 ~ 12rem / 192px) -->
							<div class="max-h-48 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700/50">
								<template x-for="dosen in dosens.filter(d => d.nama_lengkap.toLowerCase().includes(search.toLowerCase()))" :key="dosen.id">
									<div @click="selectedId = dosen.id; selectedName = dosen.nama_lengkap; open = false; search = ''"
										class="px-3 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 flex items-center justify-between">
										<span x-text="dosen.nama_lengkap"></span>
										<svg x-show="selectedId == dosen.id" class="w-3.5 h-3.5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
										</svg>
									</div>
								</template>

								<!-- Jika Pencarian Tidak Ditemukan -->
								<div x-show="dosens.filter(d => d.nama_lengkap.toLowerCase().includes(search.toLowerCase())).length === 0"
									class="px-3 py-2 text-gray-400 text-center italic">
									Dosen tidak ditemukan
								</div>
							</div>
						</div>
					</div>

					<div>
						<label for="catatan" class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">
							Catatan / SK (Opsional)
						</label>
						<textarea name="catatan" id="catatan" rows="2" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500" placeholder="Nomor SK atau catatan..."></textarea>
					</div>
				</div>

				<div class="flex items-center justify-between border-t pt-3">
					<button type="button" @click="openStep2 = false; openStep1 = true" class="text-xs text-blue-600 hover:underline">
						&larr; Kembali
					</button>
					<div class="space-x-1">
						<button type="button" @click="openStep2 = false" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-300">
							Batal
						</button>
						<button type="submit" class="px-3 py-1.5 bg-blue-900 text-white text-xs font-medium rounded-md hover:bg-blue-950">
							Simpan
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>