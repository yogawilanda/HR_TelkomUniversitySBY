<?php
// Mendefinisikan struktur data untuk timeline.
// Di lingkungan Laravel/Blade yang sesungguhnya, data ini biasanya diambil dari Controller.
$timelineData = [
	[
		'id' => 1,
		'title' => 'Pengajuan Dibuat',
		'date' => '01 Januari 2024',
		'content' => 'Anda telah berhasil membuat draf pengajuan DUPAK untuk kenaikan jabatan fungsional dari <strong>Asisten Ahli</strong> ke <strong>Lektor</strong>.',
		'border_color' => 'border-blue-600', // Warna untuk border kartu
		'is_expanded' => true, // Item pertama terbuka secara default
		'details' => null,
	],
	[
		'id' => 2,
		'title' => 'Input Kegiatan Pendidikan & Penelitian',
		'date' => '05 Januari 2024',
		'content' => 'Anda telah menambahkan kegiatan pendidikan dan penelitian.', // Konten utama null

		'border_color' => 'border-emerald-500',
		'is_expanded' => false,
		'details' => [
			'Rincian Kegiatan:',
			['Judul: "Peningkatan Kualitas Pembelajaran Melalui Metode XYZ"'],
			['Kategori: Penelitian'],
			['Bobot: <span class="font-bold text-emerald-600">3.5 KUM</span>'],
		],
		'evaluation' => [
			[
				'role' => 'TPAK 1',
				'status' => 'Verified',
				'comment' => 'Metodologi sudah sesuai dengan standar JFA Lektor.',
			],
			[
				'role' => 'TPAK 2',
				'status' => 'Verified',
				'comment' => 'Bobot sudah tepat, silakan lanjut ke tahap berikutnya.',
			],
			[
				'role' => 'Admin',
				'status' => 'Revision Needed',
				'comment' => 'Mohon lampirkan sertifikat pendukung yang lebih jelas pada bagian lampiran.',
			],
		],
	],
	[
		'id' => 3,
		'title' => 'Pengajuan Disetujui',
		'date' => '15 Januari 2024',
		'content' => 'Pengajuan telah divalidasi dan dinyatakan <strong class="text-green-600">DITERIMA</strong> oleh Tim Penilai Angka Kredit.',
		'border_color' => 'border-amber-500',
		'is_expanded' => false,
		'details' => null,
	],
	[
		'id' => 4,
		'title' => 'Kenaikan Jabatan Resmi',
		'date' => '01 Februari 2024',
		'content' => 'Selamat! Anda telah resmi naik jabatan menjadi Lektor. SK Digital Anda tersedia di sini:',
		'dot_color' => 'bg-green-500',
		'border_color' => 'border-purple-600',
		'is_expanded' => false,
		'details' => [
			'type' => 'button',
			'label' => 'Unduh SK Jabatan',
			'button_color' => 'bg-purple-600',
		],
	],
];
?>
@extends('layouts.app')

@section('content')
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
			<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
				Tambah Detil Pengajuan
			</button>
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