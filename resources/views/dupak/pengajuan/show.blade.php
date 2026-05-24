@extends('layouts.app')

@section('content')
<x-dupak.popup-tambah-kegiatan :pengajuanId="$pengajuan->id" />
<div class="min-h-screen flex flex-col pt-16 px-4 pb-12">
	<div class="mx-auto max-w-3xl w-full flex-grow">

		@if (session('success'))
		<div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
			{{ session('success') }}
		</div>
		@endif

		@if (session('error'))
		<div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
			{{ session('error') }}
		</div>
		@endif

		<!-- Header Section -->
		<div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-4">
			<!-- button dengan icon kembali ke halaman sebelumnya -->
			<a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
				<i class="fas fa-arrow-left mr-2"></i> Kembali
			</a>

			<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pengajuan DUPAK</h1>
			<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Riwayat dan progres aktivitas pengajuan kenaikan jabatan.</p>
		</div>

		<!-- Statistik Angka Kredit (Visualisasi Serupa Dashboard) -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
			<!-- Card 1: Progress Utama -->
			<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
				<div class="flex justify-between items-start mb-4">
					<div>
						<h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progres Karir Pengaju</h3>
						<p class="text-lg font-bold text-gray-900 dark:text-white">{{ $kumStats['jfa_asal'] }} <i class="fas fa-arrow-right mx-2 text-xs text-gray-400"></i> {{ $kumStats['jfa_tujuan'] }}</p>
					</div>
					<div class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full text-xs font-bold">
						{{ round($kumStats['percent']) }}%
					</div>
				</div>

				<!-- Progress Bar -->
				<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-6 overflow-hidden">
					<div class="bg-blue-600 h-4 rounded-full transition-all duration-1000" style="width: {{ $kumStats['percent'] }}%"></div>
				</div>

				<div class="grid grid-cols-2 gap-4">
					<div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
						<p class="text-xs text-gray-500 dark:text-gray-400 mb-1">KUM Saat Ini</p>
						<p class="text-xl font-black text-blue-600 dark:text-blue-400">{{ $kumStats['current_total'] }}</p>
						<p class="text-[10px] text-gray-400 mt-1 italic">(Profil: {{ $kumStats['base_kum'] }} + ACC: {{ $kumStats['approved_this_submission'] }})</p>
					</div>
					<div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
						<p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Target KUM</p>
						<p class="text-xl font-black text-gray-900 dark:text-white">{{ $kumStats['target'] }}</p>
						<p class="text-[10px] text-gray-400 mt-1 italic">Sisa: {{ $kumStats['remaining'] }}</p>
					</div>
				</div>

				<div class="mt-4 p-3 border border-dashed border-yellow-300 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl flex items-center">
					<div class="mr-3 text-yellow-600 dark:text-yellow-400">
						<i class="fas fa-hourglass-half"></i>
					</div>
					<div>
						<p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200">KUM Pending (Sedang Diajukan)</p>
						<p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">+ {{ $kumStats['pending_this_submission'] }}</p>
					</div>
				</div>
			</div>

			<!-- Card 2: Breakdown per Kategori (Hanya dari pengajuan ini) -->
			<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
				<h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Rincian KUM Pengajuan Ini</h3>
				<div class="space-y-3">
					@php
						$categories = [
							'Pendidikan' => ['icon' => 'fa-graduation-cap', 'color' => 'text-purple-500'],
							'Pelaksanaan Pendidikan' => ['icon' => 'fa-chalkboard-teacher', 'color' => 'text-blue-500'],
							'Pelaksanaan Penelitian' => ['icon' => 'fa-microscope', 'color' => 'text-emerald-500'],
							'Pelaksanaan Pengabdian' => ['icon' => 'fa-hands-helping', 'color' => 'text-orange-500'],
							'Pelaksanaan Penunjang' => ['icon' => 'fa-briefcase', 'color' => 'text-pink-500'],
						];
					@endphp

					@foreach($categories as $label => $meta)
						@php
							// Cari data di breakdown dengan pencocokan string parsial
							$val = 0;
							foreach($kumStats['breakdown'] as $catName => $total) {
								if(str_contains(strtolower($catName), strtolower(str_replace('Pelaksanaan ', '', $label))) || str_contains(strtolower($catName), strtolower($label))) {
									$val = $total;
									break;
								}
							}
						@endphp
						<div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg transition-colors">
							<div class="flex items-center">
								<div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3 {{ $meta['color'] }}">
									<i class="fas {{ $meta['icon'] }}"></i>
								</div>
								<span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
							</div>
							<span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($val, 2) }}</span>
						</div>
					@endforeach
				</div>
				
				<div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
					<span class="text-sm font-bold text-gray-900 dark:text-white">Total KUM Diajukan</span>
					<span class="text-lg font-black text-indigo-600 dark:text-indigo-400">
						{{ number_format(floatval($kumStats['approved_this_submission']) + floatval($kumStats['pending_this_submission']), 2) }}
					</span>
				</div>
			</div>
		</div>

		<!-- Action Buttons -->
		<div class="mb-8 flex justify-end gap-3">
			<!-- Tombol Lihat Timeline (Pop up) -->
			<button onclick="openTimelineModal()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
				<i class="fas fa-history mr-2"></i> Lihat Progres
			</button>

			<!-- Tombol Kirim Pengajuan (hanya jika status Draft/Pending/Revisi) -->
			@if(in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi']))
			<form action="{{ route('dupak.pengajuan.submit', $pengajuan->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin mengirim pengajuan ini? Setelah dikirim, Anda tidak bisa mengubah detail kegiatan.');">
				@csrf
				<button type="submit" class="bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 hover:text-white transition-colors text-xs uppercase tracking-widest flex items-center">
					<i class="fas fa-paper-plane mr-2"></i> Kirim Pengajuan
				</button>
			</form>
			@endif

			<!-- Tombol Tambah Detil -->
			<a href="javascript:void(0)" onclick="openModal()" id="tambah-detil-btn" class="bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-900 text-white font-bold py-2 px-4 rounded hover:bg-blue-950 hover:text-white transition-colors text-xs uppercase tracking-widest flex items-center">
				<i class="fas fa-plus mr-2"></i> Tambah Detil
			</a>
		</div>

		<!-- Tabel Detail Pengajuan -->
		<div class="mt-10">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar Aktivitas Kegiatan</h3>
			<div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
					<thead class="bg-gray-50 dark:bg-gray-700">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kegiatan</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Komponen</th>
							<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">KUM</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bukti</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
						</tr>
					</thead>
					<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($pengajuan->details as $detail)
						<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
							<td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
								{{ $detail->deskripsi_kegiatan }}
							</td>
							<td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
								{{ $detail->komponen->nama ?? 'N/A' }}
							</td>
							<td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 text-center font-semibold">
								{{ number_format($detail->angka_kredit_total, 2) }}
							</td>
							<td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
								<a href="{{ $detail->link_bukti_pendukung }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
									<i class="fas fa-external-link-alt mr-1"></i> Buka
								</a>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								@php
								$statusClasses = [
								'pending' => 'bg-yellow-100 text-yellow-800',
								'approved' => 'bg-green-100 text-green-800',
								'rejected' => 'bg-red-100 text-red-800',
								'revision' => 'bg-blue-100 text-blue-800'
								];
								$class = $statusClasses[strtolower($detail->status)] ?? 'bg-gray-100 text-gray-800';
								@endphp
								<span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
									{{ ucfirst($detail->status) }}
								</span>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
								Belum ada detail kegiatan yang ditambahkan. Gunakan tombol di atas untuk menambah.
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Full Timeline Modal (Popup Mode) -->
<div id="timeline-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="timeline-modal-title" role="dialog" aria-modal="true">
	<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
		<div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeTimelineModal()"></div>
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
			<div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white" id="timeline-modal-title">Riwayat Progres Pengajuan</h3>
				<button onclick="closeTimelineModal()" class="text-gray-400 hover:text-gray-500">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="px-6 py-8 max-h-[70vh] overflow-y-auto">
				<!-- Timeline inside the Modal -->
				<div id="timeline-container" class="relative border-l-2 border-gray-300 dark:border-gray-700 ml-4 space-y-6">
					@forelse ($timelineData as $item)
					<x-dupak.timeline-komponen-kegiatan :item="$item" />
					@empty
					<p class="text-center text-gray-500 py-4">Belum ada aktivitas kegiatan.</p>
					@endforelse
				</div>
			</div>
			<div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t dark:border-gray-700 text-right">
				<button type="button" onclick="closeTimelineModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-500 text-sm font-medium">
					Tutup
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Generic Detail Modal for Popup Mode -->
<div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
	<div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
		<div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeDetailModal()"></div>
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
			<div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4 border-b dark:border-gray-700">
				<div class="flex justify-between items-start">
					<h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">Detail Kegiatan</h3>
					<button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
						<i class="fas fa-times text-xl"></i>
					</button>
				</div>
			</div>
			<div id="modal-content-body" class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-h-96 overflow-y-auto">
				<!-- Content injected via JS -->
			</div>
			<div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
				<button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-900 text-base font-medium text-white hover:bg-blue-950 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
					Tutup
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Scripts for Timeline View Adjustments -->
<script>
	function openTimelineModal() {
		document.getElementById('timeline-modal').classList.remove('hidden');
	}

	function closeTimelineModal() {
		document.getElementById('timeline-modal').classList.add('hidden');
	}

	function closeDetailModal() {
		document.getElementById('detail-modal').classList.add('hidden');
	}

	document.addEventListener('DOMContentLoaded', function() {
		const headers = document.querySelectorAll('.accordion-header');

		headers.forEach(header => {
			const content = header.parentElement.querySelector('.accordion-content');
			const icon = header.parentElement.querySelector('.accordion-icon');

			// Setup initial accordion state
			const isInitiallyExpanded = header.getAttribute('aria-expanded') === 'true';
			if (isInitiallyExpanded) {
				content.style.maxHeight = content.scrollHeight + "px";
				content.classList.add('opacity-100');
			} else {
				content.style.maxHeight = "0px";
				content.classList.add('opacity-0');
			}

			header.addEventListener('click', function() {
				// Regular Accordion Logic
				const item = this.parentElement;
				const content = item.querySelector('.accordion-content');
				const icon = item.querySelector('.accordion-icon');

				const isCurrentlyExpanded = this.getAttribute('aria-expanded') === 'true';

				// Toggle atribut aria
				this.setAttribute('aria-expanded', !isCurrentlyExpanded);

				if (!isCurrentlyExpanded) {
					// Expanding (Membuka)
					icon.classList.add('rotate-180');

					// Setel maxHeight ke nilai penuh scrollHeight untuk memulai transisi buka
					content.style.maxHeight = content.scrollHeight + "px";
					content.classList.remove('opacity-0');
					content.classList.add('opacity-100');

				} else {
					// Collapsing (Menciut)
					icon.classList.remove('rotate-180');

					// Langkah 1: Atur tinggi saat ini agar transisi CSS berfungsi
					content.style.maxHeight = content.scrollHeight + "px";

					// Langkah 2: Setelah browser mendaftarkan tinggi penuh, segera setel ke "0px"
					// Perbaikan Kritis: Menggunakan "0px" untuk transisi collapse yang mulus
					setTimeout(() => {
						content.style.maxHeight = "0px";
						content.classList.remove('opacity-100');
						content.classList.add('opacity-0');
					}, 10);
				}
			});
		});
	});
</script>
@endsection