@extends('layouts.app')

@section('content')
<x-dupak.popup-tambah-kegiatan :pengajuanId="$pengajuan->id" />
<div class="mt-16 px-4 pb-12">
	<div class="mx-auto max-w-3xl">

		<!-- Header Section -->
		<div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-4">
			<!-- button dengan icon kembali ke halaman sebelumnya -->
			<a href="{{ route('dupak.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
				<i class="fas fa-arrow-left mr-2"></i> Kembali
			</a>

			<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pengajuan DUPAK</h1>
			<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Riwayat dan progres aktivitas pengajuan kenaikan jabatan.</p>
		</div>

		<!-- tombol tambahkan detil pengajuan di sisi kanan -->
		<div class="mb-6 flex justify-end">
			<!-- fix the styling -->
			<a href="javascript:void(0)" onclick="openModal()" id="tambah-detil-btn" class="bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-900 text-white font-bold py-2 px-4 rounded hover:bg-blue-950 hover:text-white transition-colors">
				Tambah Detil Pengajuan
			</a>
		</div>

		<!-- Timeline Container -->
		<div class="relative border-l-2 border-gray-300 dark:border-gray-700 ml-3 md:ml-6 space-y-6">

			@foreach ($timelineData as $item)
			<x-dupak.timeline-komponen-kegiatan :item="$item"></x-dupak.timeline-komponen-kegiatan>
			@endforeach

		</div>
	</div>
</div>

<!-- Scripts for the Minimalist Accordion -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const headers = document.querySelectorAll('.accordion-header');

		headers.forEach(header => {
			const item = header.parentElement;
			const content = item.querySelector('.accordion-content');
			const icon = item.querySelector('.accordion-icon');

			// Dapatkan status awal dari atribut aria-expanded di HTML
			const isInitiallyExpanded = header.getAttribute('aria-expanded') === 'true';

			// 1. Inisialisasi: Atur status awal menggunakan inline style
			if (isInitiallyExpanded) {
				// Item terbuka: Atur tinggi ke scrollHeight dan pastikan opacity 100
				content.style.maxHeight = content.scrollHeight + "px";
				content.classList.add('opacity-100');
				// Ikon sudah diatur di Blade: icon.classList.add('rotate-180');
			} else {
				// Item tertutup: Pastikan tinggi 0px dan opacity 0
				content.style.maxHeight = "0px";
				content.classList.add('opacity-0');
			}

			header.addEventListener('click', function() {
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
					}, 10); // Memberi jeda mikro untuk memastikan transisi dipicu

					// Optional: Setelah transisi selesai, hapus maxHeight untuk membersihkan
					// Namun, kita akan biarkan 0px karena lebih aman untuk transisi.
				}
			});
		});
	});
</script>
@endsection