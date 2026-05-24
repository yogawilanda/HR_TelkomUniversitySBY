@extends('layouts.app')

@section('content')
<x-dupak.popup-tambah-kegiatan :kegiatanUtama="$kegiatanUtama" :pengajuanId="$pengajuan->id" />

<div class="py-6" x-data="{ tab: 'activities' }">
	<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
		
		<!-- Back Button -->
		<a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
			<i class="fas fa-arrow-left mr-2"></i> Kembali
		</a>

		<!-- Toast/Alert Notifications -->
		@if (session('success'))
		<div class="px-4 py-3 my-4 text-green-700 bg-green-100 border border-green-400 rounded relative" role="alert">
			<span class="block sm:inline">{{ session('success') }}</span>
		</div>
		@endif

		@if (session('error'))
		<div class="px-4 py-3 my-4 text-red-700 bg-red-100 border border-red-400 rounded relative" role="alert">
			<span class="block sm:inline">{{ session('error') }}</span>
		</div>
		@endif

		<!-- Header Card (Matching dupak/dashboard layout) -->
		<div class="bg-white shadow rounded-t-lg p-6 pb-6 mb-6">
			<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
				<div>
					<h1 class="text-2xl font-semibold text-gray-900">Detail Pengajuan DUPAK</h1>
					<p class="text-xs text-gray-500 italic mt-1">
						Periode: {{ \Carbon\Carbon::parse($pengajuan->start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($pengajuan->end)->format('d/m/Y') }}
						({{ $pengajuan->semesterAjuan }} - TA {{ $pengajuan->TahunAjaranAjuanAwal }})
					</p>
				</div>
				
				@php
				$badgeColor = [
					'Draft' => 'bg-gray-100 text-gray-800',
					'Diajukan' => 'bg-yellow-100 text-yellow-800',
					'Menunggu' => 'bg-indigo-100 text-indigo-800',
					'Ditolak' => 'bg-red-100 text-red-800',
					'Diterima' => 'bg-green-100 text-green-800',
					'Revisi' => 'bg-yellow-100 text-yellow-800',
				][$pengajuan->status] ?? 'bg-gray-100 text-gray-800';
				@endphp
				<span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
					{{ $pengajuan->status }}
				</span>
			</div>
		</div>

		<!-- Main 3-Column Layout (Matching dupak/dashboard) -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
			
			<!-- Left Column: Details & Timeline (lg:col-span-2) -->
			<div class="lg:col-span-2 space-y-6">
				
				<!-- Tabbed Content Card (Matching layout style and padding) -->
				<div class="bg-white shadow rounded-lg p-6">
					
					<!-- Tab Switcher (Matching style of dashboard) -->
					<div class="flex space-x-4 border-b border-gray-100 pb-3 mb-6">
						<button @click="tab = 'activities'"
							:class="tab === 'activities' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500'"
							class="px-4 py-2 font-semibold border-b-2 transition-colors flex items-center gap-2 focus:outline-none">
							<i class="fas fa-list-ol text-xs"></i>
							Daftar Aktivitas
							<span class="ml-1 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
								{{ count($pengajuan->details) }}
							</span>
						</button>
						<button @click="tab = 'timeline'"
							:class="tab === 'timeline' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500'"
							class="px-4 py-2 font-semibold border-b-2 transition-colors flex items-center gap-2 focus:outline-none">
							<i class="fas fa-history text-xs"></i>
							Riwayat & Progres
						</button>
					</div>

					<!-- Tab Content: Activities Table -->
					<div x-show="tab === 'activities'">
						<div class="flex justify-between items-center mb-4">
							<h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">Daftar Butir Kegiatan</h4>
							@if(in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi']))
							<a onclick="openModal()" class="px-3 py-1.5 text-xs font-bold text-blue-900 border border-blue-900 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors flex items-center gap-1">
								<i class="fas fa-plus"></i> Tambah Kegiatan
							</a>
							@endif
						</div>

						<div class="overflow-x-auto border border-gray-200 rounded-lg">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-blue-900 text-white text-xs uppercase">
									<tr>
										<th scope="col" class="px-6 py-3 text-left">Kegiatan</th>
										<th scope="col" class="px-6 py-3 text-left">Komponen</th>
										<th scope="col" class="px-6 py-3 text-center">KUM</th>
										<th scope="col" class="px-6 py-3 text-left">Bukti</th>
										<th scope="col" class="px-6 py-3 class=text-left">Status</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 bg-white">
									@forelse ($pengajuan->details as $detail)
									<tr class="hover:bg-gray-50 transition-colors">
										<td class="px-6 py-4 text-sm text-gray-900">
											{{ $detail->deskripsi_kegiatan }}
										</td>
										<td class="px-6 py-4 text-sm text-gray-500">
											{{ $detail->komponen->nama ?? 'N/A' }}
										</td>
										<td class="px-6 py-4 text-sm text-gray-900 text-center font-bold">
											{{ number_format($detail->angka_kredit_total, 2) }}
										</td>
										<td class="px-6 py-4 text-sm">
											<a href="{{ $detail->link_bukti_pendukung }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center font-semibold">
												<i class="fas fa-external-link-alt mr-1"></i> Buka
											</a>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											@php
												$detailStatusColors = [
													'pending' => 'bg-yellow-100 text-yellow-800',
													'approved' => 'bg-green-100 text-green-800',
													'rejected' => 'bg-red-100 text-red-800',
													'revision' => 'bg-blue-100 text-blue-800'
												];
												$detailClass = $detailStatusColors[strtolower($detail->status)] ?? 'bg-gray-100 text-gray-800';
											@endphp
											<span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $detailClass }}">
												{{ ucfirst($detail->status) }}
											</span>
										</td>
									</tr>
									@empty
									<tr>
										<td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">
											Belum ada detail kegiatan yang ditambahkan. Gunakan tombol Tambah Kegiatan untuk memulai.
										</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>

					<!-- Tab Content: Timeline -->
					<div x-show="tab === 'timeline'" x-cloak>
						<h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Timeline Progres Pengajuan</h4>
						
						<div class="relative border-l-2 border-gray-200 ml-4 space-y-6">
							@forelse ($timelineData as $item)
								<x-dupak.timeline-komponen-kegiatan :item="$item" />
							@empty
								<p class="text-center text-sm text-gray-400 py-6 italic">Belum ada aktivitas tercatat.</p>
							@endforelse
						</div>
					</div>
				</div>

			</div>

			<!-- Right Column: Sidebar (lg:col-span-1) -->
			<div id="containerRightSide" class="space-y-6">
				
				<!-- Action Control Card (Matching dashboard style) -->
				<div class="p-6 border rounded-lg bg-white shadow-sm flex flex-col space-y-4">
					<h3 class="text-sm font-bold text-gray-900 flex items-center">
						<i class="fas fa-user-circle mr-2 text-blue-900"></i> Kontrol Aksi Pengajuan
					</h3>
					
					<div class="flex flex-col gap-3">
						@if(in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi']))
							<!-- Add Activity -->
							<a onclick="openModal()" class="px-5 py-2.5 text-sm font-bold text-blue-900 border border-blue-900 rounded-lg hover:bg-blue-50 cursor-pointer flex-1 text-center transition-colors">
								<i class="fas fa-plus-circle mr-1"></i> Tambahkan Kegiatan
							</a>

							<!-- Submit DUPAK -->
							<form action="{{ route('dupak.pengajuan.submit', $pengajuan->id) }}" method="POST" class="w-full flex" onsubmit="return confirm('Kirim pengajuan DUPAK ini ke TPAK? Setelah dikirim, Anda tidak dapat mengubah butir kegiatan sampai dinilai.')">
								@csrf
								<button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-900 rounded-lg hover:bg-blue-950 transition-all flex-1 text-center">
									<i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
								</button>
							</form>

							<div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
								<!-- Edit Details -->
								<a href="{{ route('dupak.pengajuan.edit', $pengajuan->id) }}" class="px-3 py-2 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 text-center transition-colors">
									<i class="fas fa-edit mr-1"></i> Edit Info
								</a>

								<!-- Delete Submission -->
								<form action="{{ route('dupak.pengajuan.destroy', $pengajuan->id) }}" method="POST" class="w-full flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan DUPAK ini beserta seluruh kegiatannya?')">
									@csrf
									@method('DELETE')
									<button type="submit" class="w-full px-3 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 text-center transition-colors">
										<i class="fas fa-trash-alt mr-1"></i> Hapus
									</button>
								</form>
							</div>
						@elseif($pengajuan->status === 'Diajukan')
							<!-- Submitted Assessment State -->
							<div class="p-4 border-l-4 border-yellow-500 bg-yellow-50 rounded text-center">
								<p class="text-xs text-yellow-800 font-semibold flex items-center justify-center gap-1.5 animate-pulse">
									<i class="fas fa-hourglass-half"></i> Sedang Dinilai TPAK
								</p>
								<p class="text-[10px] text-gray-500 mt-1">Berkas pengajuan DUPAK sedang dalam tahap verifikasi dan validasi oleh TPAK.</p>
							</div>
						@elseif($pengajuan->status === 'Diterima')
							<!-- Approved State -->
							<div class="p-4 border-l-4 border-green-500 bg-green-50 rounded text-center">
								<p class="text-xs text-green-800 font-semibold flex items-center justify-center gap-1.5">
									<i class="fas fa-check-circle"></i> Pengajuan Disetujui
								</p>
								<p class="text-[10px] text-gray-500 mt-1">Selamat! Pengajuan usulan kenaikan jabatan fungsional telah selesai dinilai dan diterima.</p>
							</div>
						@else
							<!-- Locked State -->
							<div class="p-4 border-l-4 border-gray-500 bg-gray-50 rounded text-center">
								<p class="text-xs text-gray-800 font-semibold flex items-center justify-center gap-1.5">
									<i class="fas fa-lock"></i> Pengajuan Terkunci
								</p>
								<p class="text-[10px] text-gray-500 mt-1">Pengajuan dengan status {{ $pengajuan->status }} tidak dapat diubah kembali.</p>
							</div>
						@endif
					</div>
				</div>

				<!-- KUM Career Progress Card (Matching info-kum layout and spacing) -->
				<div class="p-6 border rounded-lg bg-white shadow-sm flex flex-col">
					<div class="flex justify-between items-start mb-6">
						<div>
							<h3 class="text-lg font-bold text-gray-900">Progres Karir</h3>
							<p class="text-xs text-gray-500 italic">Target Kenaikan Jabatan</p>
						</div>
					</div>

					<div class="text-sm font-bold text-blue-900 bg-blue-50 px-3 py-2 rounded-lg border border-blue-100 mb-6 flex justify-between items-center">
						<span>{{ $kumStats['jfa_asal'] }}</span>
						<i class="fas fa-arrow-right text-xs text-gray-400"></i>
						<span>{{ $kumStats['jfa_tujuan'] }}</span>
					</div>

					<!-- Progress Bar (Exactly matching info-kum) -->
					<div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6">
						<div class="flex justify-between text-sm font-bold text-gray-700 mb-2">
							<span>Progress Capaian</span>
							<span>{{ number_format($kumStats['percent'], 0) }}%</span>
						</div>
						<div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden shadow-inner">
							<div class="h-full bg-blue-900 transition-all duration-500" style="width: {{ $kumStats['percent'] }}%"></div>
						</div>
					</div>

					<!-- KUM Numbers (Matching info-kum styling) -->
					<div class="grid grid-cols-2 gap-4">
						<div class="flex flex-col p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
							<span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">KUM Saat Ini</span>
							<div class="text-2xl font-black text-blue-900 mt-1 leading-none">
								{{ $kumStats['current_total'] }}
							</div>
							<span class="text-[8px] text-gray-400 mt-1 italic leading-tight">(Profil: {{ $kumStats['base_kum'] }} + ACC: {{ $kumStats['approved_this_submission'] }})</span>
						</div>
						<div class="flex flex-col p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
							<span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Target KUM</span>
							<div class="text-2xl font-bold text-gray-800 mt-1 leading-none">
								{{ $kumStats['target'] }}
							</div>
							<span class="text-[8px] text-gray-400 mt-1 italic leading-tight">Sisa: {{ $kumStats['remaining'] }}</span>
						</div>
					</div>

					<!-- KUM Pending Warning Card -->
					@if(floatval($kumStats['pending_this_submission']) > 0)
					<div class="mt-4 p-3 border border-dashed border-yellow-300 bg-yellow-50 rounded-xl flex items-center">
						<div class="mr-3 text-yellow-600">
							<i class="fas fa-hourglass-half"></i>
						</div>
						<div>
							<p class="text-xs font-semibold text-yellow-800 leading-tight">KUM Pending (Diajukan)</p>
							<p class="text-sm font-bold text-yellow-600 mt-0.5">+ {{ $kumStats['pending_this_submission'] }}</p>
						</div>
					</div>
					@endif
				</div>

				<!-- Category KUM Breakdown Card (Matching info-kum breakdown layout) -->
				<div class="p-6 border rounded-lg bg-white shadow-sm flex flex-col">
					<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Rincian KUM Pengajuan Ini</h3>
					
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
							@php $val = $kumStats['breakdown'][$label] ?? 0; @endphp
							<div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition-colors border border-transparent">
								<div class="flex items-center">
									<div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3 {{ $meta['color'] }}">
										<i class="fas {{ $meta['icon'] }} text-xs"></i>
									</div>
									<span class="text-xs font-medium text-gray-700">{{ $label }}</span>
								</div>
								<span class="text-xs font-bold text-gray-900 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
									{{ number_format($val, 2) }}
								</span>
							</div>
						@endforeach
					</div>

					<div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center">
						<span class="text-xs font-bold text-gray-900">Total KUM Diajukan</span>
						<span class="text-sm font-black text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
							{{ number_format(floatval($kumStats['approved_this_submission']) + floatval($kumStats['pending_this_submission']), 2) }}
						</span>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>

<!-- Full Timeline Modal (Popup Mode for fallback) -->
<div id="timeline-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="timeline-modal-title" role="dialog" aria-modal="true">
	<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
		<div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeTimelineModal()"></div>
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
			<div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
				<h3 class="text-base font-bold text-gray-900 dark:text-white" id="timeline-modal-title">Riwayat Progres Pengajuan</h3>
				<button onclick="closeTimelineModal()" class="text-gray-400 hover:text-gray-500">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="px-6 py-8 max-h-[70vh] overflow-y-auto">
				<div id="timeline-container" class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-4 space-y-6">
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

<!-- Scripts for Accordion and Modal Control -->
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
				const item = this.parentElement;
				const content = item.querySelector('.accordion-content');
				const icon = item.querySelector('.accordion-icon');

				const isCurrentlyExpanded = this.getAttribute('aria-expanded') === 'true';

				// Toggle aria-expanded
				this.setAttribute('aria-expanded', !isCurrentlyExpanded);

				if (!isCurrentlyExpanded) {
					// Expanding
					icon.classList.add('rotate-180');
					content.style.maxHeight = content.scrollHeight + "px";
					content.classList.remove('opacity-0');
					content.classList.add('opacity-100');
				} else {
					// Collapsing
					icon.classList.remove('rotate-180');
					content.style.maxHeight = content.scrollHeight + "px";

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