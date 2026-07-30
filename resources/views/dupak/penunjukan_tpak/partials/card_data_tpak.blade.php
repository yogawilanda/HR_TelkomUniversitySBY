<div id="card_data_tpak" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
	<div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs font-semibold text-gray-500 uppercase">Belum Ditunjuk</p>
				<p class="text-2xl font-bold text-blue-600">{{ $stats['belum_ditunjuk'] }}</p>
			</div>
			<div class="p-3 bg-blue-100 rounded-lg text-blue-600"><i
					class="fas fa-exclamation-circle fa-lg"></i></div>
		</div>
	</div>
	<div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs font-semibold text-gray-500 uppercase">Sedang Review</p>
				<p class="text-2xl font-bold text-blue-700">{{ $stats['sedang_review'] }}</p>
			</div>
			<div class="p-3 bg-blue-100 rounded-lg text-blue-700"><i class="fas fa-sync-alt fa-lg"></i></div>
		</div>
	</div>
	<div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs font-semibold text-gray-500 uppercase">Total Dosen</p>
				<p class="text-2xl font-bold text-blue-800">{{ $stats['total_dosen'] }}</p>
			</div>
			<div class="p-3 bg-blue-100 rounded-lg text-blue-800"><i class="fas fa-users fa-lg"></i></div>
		</div>
	</div>
	<div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700">
		<div class="flex items-center justify-between">
			<div>
				<p class="text-xs font-semibold text-gray-500 uppercase">Selesai Dinilai</p>
				<p class="text-2xl font-bold text-blue-500">{{ $stats['selesai'] }}</p>
			</div>
			<div class="p-3 bg-blue-100 rounded-lg text-blue-500"><i class="fas fa-check-double fa-lg"></i>
			</div>
		</div>
	</div>
</div>