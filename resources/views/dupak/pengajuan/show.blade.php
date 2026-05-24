@extends('layouts.app')

@section('content')
<x-dupak.popup-tambah-kegiatan :kegiatanUtama="$kegiatanUtama" :pengajuanId="$pengajuan->id" />

<div class="min-h-screen bg-gray-50/50 dark:bg-gray-900/50 pt-20 pb-12 px-4 sm:px-6 lg:px-8">
	<div class="mx-auto max-w-7xl w-full space-y-8">
		
		<!-- Toast/Alert Notifications -->
		@if (session('success'))
		<div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 dark:bg-emerald-950/20 dark:border-emerald-500 rounded-xl shadow-sm flex items-start gap-3 animate-fade-in">
			<div class="text-emerald-600 dark:text-emerald-400">
				<i class="fas fa-check-circle text-lg"></i>
			</div>
			<div class="flex-1">
				<p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Berhasil</p>
				<p class="text-xs text-emerald-600 dark:text-emerald-400/80 mt-0.5">{{ session('success') }}</p>
			</div>
		</div>
		@endif

		@if (session('error'))
		<div class="p-4 bg-rose-50 border-l-4 border-rose-500 dark:bg-rose-950/20 dark:border-rose-500 rounded-xl shadow-sm flex items-start gap-3 animate-fade-in">
			<div class="text-rose-600 dark:text-rose-400">
				<i class="fas fa-exclamation-circle text-lg"></i>
			</div>
			<div class="flex-1">
				<p class="text-sm font-semibold text-rose-800 dark:text-rose-300">Kesalahan</p>
				<p class="text-xs text-rose-600 dark:text-rose-400/80 mt-0.5">{{ session('error') }}</p>
			</div>
		</div>
		@endif

		<!-- Premium Header Section -->
		<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6 md:p-8 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6 transition-all duration-300">
			<div class="space-y-3">
				<a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-indigo-600 dark:text-gray-500 dark:hover:text-indigo-400 transition-colors group mb-1">
					<i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i> Kembali ke Dasbor
				</a>
				<div class="flex flex-wrap items-center gap-3">
					<h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white">
						Detail Pengajuan DUPAK
					</h1>
					@php
						$statusColors = [
							'Draft' => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800/40 dark:text-gray-300 dark:border-gray-700/50',
							'Pending' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30',
							'Diajukan' => 'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900/30',
							'Revisi' => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-950/20 dark:text-sky-400 dark:border-sky-900/30',
							'Diterima' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30',
							'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/30'
						];
						$badgeClass = $statusColors[$pengajuan->status] ?? 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800/40 dark:text-gray-300 dark:border-gray-700/50';
					@endphp
					<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold border {{ $badgeClass }} uppercase tracking-wider">
						<span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-current"></span>
						{{ $pengajuan->status }}
					</span>
				</div>
				<p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
					Pantau progres pengajuan kenaikan jabatan akademik Anda. Kelola butir kegiatan pendidikan, penelitian, pengabdian, dan penunjang.
				</p>
			</div>

			<!-- Meta Summary Info -->
			<div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/40 rounded-2xl p-4 border border-gray-100 dark:border-gray-800">
				<div>
					<span class="block text-[10px] uppercase font-bold text-gray-400">Periode Awal</span>
					<span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($pengajuan->start)->format('d M Y') }}</span>
				</div>
				<div>
					<span class="block text-[10px] uppercase font-bold text-gray-400">Semester</span>
					<span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $pengajuan->semesterAjuan }}</span>
				</div>
				<div class="col-span-2 border-t border-gray-100 dark:border-gray-800/80 pt-2">
					<span class="block text-[10px] uppercase font-bold text-gray-400">Tahun Ajaran</span>
					<span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $pengajuan->TahunAjaranAjuanAwal }} - {{ $pengajuan->TahunAjaranAjuanAkhir }}</span>
				</div>
			</div>
		</div>

		<!-- Main 3-Column Layout -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start" x-data="{ activeTab: 'activities' }">
			
			<!-- Left/Center Column: Main Content (lg:col-span-2) -->
			<div class="lg:col-span-2 space-y-8">
				
				<!-- Tabbed Content Section -->
				<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden transition-all duration-300">
					<!-- Tab Headers -->
					<div class="border-b border-gray-100 dark:border-gray-700 flex px-6 py-2 bg-gray-50/50 dark:bg-gray-900/20">
						<button 
							@click="activeTab = 'activities'"
							:class="activeTab === 'activities' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300'"
							class="px-4 py-3 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
							<i class="fas fa-list-ol"></i>
							Daftar Aktivitas
							<span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full text-xs font-semibold">
								{{ count($pengajuan->details) }}
							</span>
						</button>
						<button 
							@click="activeTab = 'timeline'"
							:class="activeTab === 'timeline' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300'"
							class="px-4 py-3 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
							<i class="fas fa-history"></i>
							Riwayat & Progres
						</button>
					</div>

					<!-- Tab Content: Activities Table -->
					<div x-show="activeTab === 'activities'" class="p-6">
						<div class="flex justify-between items-center mb-4">
							<h3 class="text-lg font-bold text-gray-900 dark:text-white">Aktivitas Kegiatan DUPAK</h3>
							@if(in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi']))
							<button onclick="openModal()" class="inline-flex items-center px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 dark:text-indigo-400 font-semibold rounded-xl text-xs transition-colors gap-1.5 border border-indigo-100 dark:border-indigo-900/30">
								<i class="fas fa-plus"></i> Tambah Kegiatan
							</button>
							@endif
						</div>

						<div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700/80 shadow-inner">
							<table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
								<thead class="bg-gray-50 dark:bg-gray-900/40">
									<tr>
										<th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kegiatan</th>
										<th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Komponen</th>
										<th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">KUM</th>
										<th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Bukti</th>
										<th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
									</tr>
								</thead>
								<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
									@forelse ($pengajuan->details as $detail)
									<tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
										<td class="px-6 py-4">
											<div class="text-sm font-semibold text-gray-900 dark:text-gray-200 line-clamp-2 max-w-md">
												{{ $detail->deskripsi_kegiatan }}
											</div>
										</td>
										<td class="px-6 py-4">
											<div class="text-xs font-medium text-gray-400 dark:text-gray-500 max-w-xs truncate">
												{{ $detail->komponen->nama ?? 'N/A' }}
											</div>
										</td>
										<td class="px-6 py-4 text-center">
											<span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400 text-xs font-bold rounded-lg border border-indigo-100/50 dark:border-indigo-900/30">
												{{ number_format($detail->angka_kredit_total, 2) }}
											</span>
										</td>
										<td class="px-6 py-4">
											<a href="{{ $detail->link_bukti_pendukung }}" target="_blank" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors group">
												<i class="fas fa-external-link-alt mr-1.5 transform group-hover:scale-110 transition-transform"></i> Buka Bukti
											</a>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											@php
												$detailStatusColors = [
													'pending' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30',
													'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30',
													'rejected' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/30',
													'revision' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/20 dark:text-sky-400 dark:border-sky-900/30'
												];
												$detailClass = $detailStatusColors[strtolower($detail->status)] ?? 'bg-gray-50 text-gray-700 border-gray-200';
											@endphp
											<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $detailClass }} uppercase tracking-wider">
												{{ ucfirst($detail->status) }}
											</span>
										</td>
									</tr>
									@empty
									<tr>
										<td colspan="5" class="px-6 py-12 text-center">
											<div class="flex flex-col items-center justify-center max-w-sm mx-auto space-y-4">
												<div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center text-gray-400 dark:text-gray-600 border border-gray-100 dark:border-gray-800">
													<i class="fas fa-folder-open text-2xl"></i>
												</div>
												<div>
													<p class="text-sm font-semibold text-gray-900 dark:text-white">Belum Ada Aktivitas Kegiatan</p>
													<p class="text-xs text-gray-400 mt-1">Daftar butir kegiatan pengajuan Anda masih kosong. Silakan tambahkan butir baru untuk memulai.</p>
												</div>
												@if(in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi']))
												<button onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors gap-1.5 shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30">
													<i class="fas fa-plus"></i> Tambah Kegiatan Pertama
												</button>
												@endif
											</div>
										</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>

					<!-- Tab Content: Timeline -->
					<div x-show="activeTab === 'timeline'" class="p-6" x-cloak>
						<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Timeline Progres Pengajuan</h3>
						
						<div class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-4 space-y-6">
							@forelse ($timelineData as $item)
								<x-dupak.timeline-komponen-kegiatan :item="$item" />
							@empty
								<p class="text-center text-sm text-gray-400 dark:text-gray-500 py-6 italic">Belum ada progres terekam.</p>
							@endforelse
						</div>
					</div>
				</div>

			</div>

			<!-- Right Column: Stats & Actions (lg:col-span-1) -->
			<div class="space-y-8">
				
				<!-- Action Control Panel Card -->
				<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6 shadow-sm space-y-6">
					<h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
						<i class="fas fa-cog text-indigo-600 dark:text-indigo-400"></i>
						Kontrol Pengajuan
					</h3>

					<div class="space-y-3">
						@if(in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi']))
							<!-- Add Activity -->
							<button onclick="openModal()" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-all shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 text-sm gap-2">
								<i class="fas fa-plus-circle"></i> Tambah Kegiatan
							</button>

							<!-- Submit Submission -->
							<form action="{{ route('dupak.pengajuan.submit', $pengajuan->id) }}" method="POST" class="block w-full" onsubmit="return confirm('Kirim pengajuan DUPAK ini ke TPAK? Setelah dikirim, Anda tidak dapat mengubah butir kegiatan sampai dinilai.')">
								@csrf
								<button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-md shadow-emerald-500/20 hover:shadow-emerald-500/30 text-sm gap-2">
									<i class="fas fa-paper-plane"></i> Kirim Pengajuan
								</button>
							</form>

							<div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100 dark:border-gray-700/60">
								<!-- Edit Details -->
								<a href="{{ route('dupak.pengajuan.edit', $pengajuan->id) }}" class="inline-flex items-center justify-center px-3 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/40 dark:hover:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold rounded-xl border border-gray-100 dark:border-gray-800 transition-colors text-xs gap-1.5">
									<i class="fas fa-edit"></i> Edit Info
								</a>

								<!-- Delete Submission -->
								<form action="{{ route('dupak.pengajuan.destroy', $pengajuan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan DUPAK ini beserta seluruh kegiatannya? Tindakan ini tidak dapat dibatalkan.')">
									@csrf
									@method('DELETE')
									<button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-950 text-rose-600 dark:text-rose-400 font-semibold rounded-xl border border-rose-100 dark:border-rose-900/30 transition-colors text-xs gap-1.5">
										<i class="fas fa-trash-alt"></i> Hapus
									</button>
								</form>
							</div>
						@elseif($pengajuan->status === 'Diajukan')
							<!-- Submitted/Assessment State -->
							<div class="p-4 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 rounded-2xl flex flex-col items-center text-center space-y-3">
								<div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400 animate-pulse">
									<i class="fas fa-hourglass-half"></i>
								</div>
								<div>
									<h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-300">Sedang Dinilai TPAK</h4>
									<p class="text-xs text-indigo-600/80 dark:text-indigo-400/80 mt-1">Berkas pengajuan Anda telah dikirim dan sedang dalam proses verifikasi dan penilaian oleh Tim Penilai Angka Kredit.</p>
								</div>
							</div>
						@elseif($pengajuan->status === 'Diterima')
							<!-- Approved State -->
							<div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 rounded-2xl flex flex-col items-center text-center space-y-3">
								<div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400">
									<i class="fas fa-check-circle text-lg"></i>
								</div>
								<div>
									<h4 class="text-sm font-bold text-emerald-900 dark:text-emerald-300">Pengajuan Disetujui</h4>
									<p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 mt-1">Selamat! Pengajuan usulan kenaikan jabatan fungsional Anda telah selesai dinilai dan diterima.</p>
								</div>
							</div>
						@else
							<!-- Other Non-Editable States -->
							<div class="p-4 bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 rounded-2xl flex flex-col items-center text-center space-y-2">
								<div class="text-gray-400 dark:text-gray-600">
									<i class="fas fa-lock text-lg"></i>
								</div>
								<div>
									<h4 class="text-sm font-bold text-gray-900 dark:text-white">Pengajuan Terkunci</h4>
									<p class="text-xs text-gray-400 mt-1">Pengajuan dengan status <strong>{{ $pengajuan->status }}</strong> tidak dapat diubah kembali.</p>
								</div>
							</div>
						@endif
					</div>
				</div>

				<!-- KUM Career Progress Card -->
				<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6 shadow-sm space-y-6">
					<div>
						<h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Progres Karir Pengaju</h3>
						<p class="text-base font-extrabold text-gray-900 dark:text-white mt-1 flex items-center gap-2">
							<span>{{ $kumStats['jfa_asal'] }}</span>
							<i class="fas fa-arrow-right text-xs text-indigo-400"></i>
							<span class="text-indigo-600 dark:text-indigo-400">{{ $kumStats['jfa_tujuan'] }}</span>
						</p>
					</div>

					<!-- Custom Sleek Progress Bar -->
					<div class="space-y-2">
						<div class="flex justify-between items-center text-xs">
							<span class="font-semibold text-gray-400">Pencapaian Target</span>
							<span class="font-extrabold text-indigo-600 dark:text-indigo-400">{{ round($kumStats['percent']) }}%</span>
						</div>
						<div class="w-full bg-gray-100 dark:bg-gray-900 rounded-full h-3 overflow-hidden border border-gray-100/50 dark:border-gray-800/80 shadow-inner">
							<div class="bg-gradient-to-r from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-sm" style="width: {{ $kumStats['percent'] }}%"></div>
						</div>
					</div>

					<!-- KUM Numbers Grid -->
					<div class="grid grid-cols-2 gap-4">
						<div class="p-3 bg-gray-50/50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-800">
							<span class="block text-[10px] uppercase font-bold text-gray-400">KUM Saat Ini</span>
							<span class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ $kumStats['current_total'] }}</span>
							<span class="block text-[8px] text-gray-400 mt-1 italic">Profil: {{ $kumStats['base_kum'] }} + ACC: {{ $kumStats['approved_this_submission'] }}</span>
						</div>
						<div class="p-3 bg-gray-50/50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-800">
							<span class="block text-[10px] uppercase font-bold text-gray-400">Target KUM</span>
							<span class="text-lg font-black text-gray-900 dark:text-white">{{ $kumStats['target'] }}</span>
							<span class="block text-[8px] text-gray-400 mt-1 italic">Kekurangan: {{ $kumStats['remaining'] }}</span>
						</div>
					</div>

					<!-- KUM Pending Warning Card -->
					@if(floatval($kumStats['pending_this_submission']) > 0)
					<div class="p-3.5 bg-amber-50/40 dark:bg-amber-950/10 border border-dashed border-amber-200 dark:border-amber-900/30 rounded-2xl flex items-start gap-3">
						<div class="text-amber-500 mt-0.5">
							<i class="fas fa-hourglass-half text-sm"></i>
						</div>
						<div>
							<p class="text-xs font-bold text-amber-800 dark:text-amber-300">KUM Sedang Diajukan</p>
							<p class="text-sm font-black text-amber-600 dark:text-amber-400 mt-0.5">+ {{ $kumStats['pending_this_submission'] }} KUM</p>
						</div>
					</div>
					@endif
				</div>

				<!-- Category KUM Breakdown Card -->
				<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6 shadow-sm space-y-6">
					<div>
						<h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">KUM Pengajuan Ini</h3>
						<p class="text-xs text-gray-400 mt-0.5">Rincian nilai KUM per kategori tri dharma yang diajukan saat ini.</p>
					</div>

					<div class="space-y-2">
						@php
							$categories = [
								'Pendidikan' => ['icon' => 'fa-graduation-cap', 'bg' => 'bg-purple-50 dark:bg-purple-950/20', 'color' => 'text-purple-600 dark:text-purple-400'],
								'Pelaksanaan Pendidikan' => ['icon' => 'fa-chalkboard-teacher', 'bg' => 'bg-blue-50 dark:bg-blue-950/20', 'color' => 'text-blue-600 dark:text-blue-400'],
								'Pelaksanaan Penelitian' => ['icon' => 'fa-microscope', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/20', 'color' => 'text-emerald-600 dark:text-emerald-400'],
								'Pelaksanaan Pengabdian' => ['icon' => 'fa-hands-helping', 'bg' => 'bg-orange-50 dark:bg-orange-950/20', 'color' => 'text-orange-600 dark:text-orange-400'],
								'Pelaksanaan Penunjang' => ['icon' => 'fa-briefcase', 'bg' => 'bg-pink-50 dark:bg-pink-950/20', 'color' => 'text-pink-600 dark:text-pink-400'],
							];
						@endphp

						@foreach($categories as $label => $meta)
							@php $val = $kumStats['breakdown'][$label] ?? 0; @endphp
							<div class="flex items-center justify-between p-2.5 hover:bg-gray-50 dark:hover:bg-gray-900/40 rounded-xl transition-colors border border-transparent hover:border-gray-100 dark:hover:border-gray-800">
								<div class="flex items-center gap-3">
									<div class="w-8 h-8 rounded-lg {{ $meta['bg'] }} flex items-center justify-center {{ $meta['color'] }} shadow-sm">
										<i class="fas {{ $meta['icon'] }} text-xs"></i>
									</div>
									<span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</span>
								</div>
								<span class="text-xs font-extrabold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/80 px-2.5 py-1 rounded-lg border border-gray-100/50 dark:border-gray-800">
									{{ number_format($val, 2) }}
								</span>
							</div>
						@endforeach
					</div>

					<div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 flex justify-between items-center">
						<span class="text-xs font-bold text-gray-900 dark:text-white">Total KUM Diajukan</span>
						<span class="text-base font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/20 px-3 py-1.5 rounded-xl border border-indigo-100/50 dark:border-indigo-900/30">
							{{ number_format(floatval($kumStats['approved_this_submission']) + floatval($kumStats['pending_this_submission']), 2) }}
						</span>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>

<!-- Full Timeline Modal (Popup Mode for backwards compatibility/fallback) -->
<div id="timeline-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="timeline-modal-title" role="dialog" aria-modal="true">
	<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
		<div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeTimelineModal()"></div>
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
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