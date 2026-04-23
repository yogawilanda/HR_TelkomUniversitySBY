@extends('layouts.app')

@section('content')

<div class="mt-16 md:ml-64 sm:ml-12 lg:ml-64">
	<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
		<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				<a href="{{ route('dupak.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>

				<h1 class="mb-3 text-2xl font-semibold">Formulir Detil Pengajuan DUPAK</h1>
				<h2 class="text-xl">Daftar Usulan Penetapan Angka Kredit</h2>

				{{-- Fixed: Form tag was self-closing and route name adjusted to match web.php --}}
				<form method="POST" action="{{ route('dupak.detil_pengajuan.store', [$category, $pengajuan->id]) }}" class="space-y-6">
					@csrf
					<input type="hidden" name="id_komponen" value="{{ $komponen->id }}">

					<!-- Unsur Utama -->
					<div class="p-4 rounded-lg bg-gray-50">
						<h2 class="mb-2 text-2xl font-medium text-gray-900">{{ $komponen->nama }}</h2>
						<p class="mb-2 text-gray-600">Sub-kategori dari: {{ ucfirst(str_replace('-', ' ', $category)) }}</p>
						<p class="mb-6 text-xs text-indigo-600 font-medium">Satuan Hasil: {{ $komponen->satuanHasil ?: 'Butir Kegiatan' }}</p>

						<div class="space-y-4 bg-white p-4 border rounded-lg shadow-sm">
							<div>
								<label class="block mb-2 text-sm font-semibold text-gray-700">Detail Butir Kegiatan</label>
								<select name="id_jenis_input" id="id_jenis_input" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
									<option value="">-- Pilih Salah Satu --</option>
									@foreach($jenisInputs as $input)
										<option value="{{ $input->id }}" data-nilai="{{ $input->nilai_baku }}">
											{{ $input->nama }} (AK: {{ $input->nilai_baku }})
										</option>
									@endforeach
								</select>
								<p class="text-xs text-gray-500 mt-1">Pilih butir kegiatan untuk melihat Angka Kredit (AK) baku.</p>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div>
									<label class="block mb-2 text-sm font-semibold text-gray-700">Volume / Jumlah</label>
									<input type="number" name="volume" id="volume" value="1" min="1" step="1" required
										class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
									<p class="text-xs text-gray-500 mt-1">Jumlah satuan hasil (default: 1 {{ $komponen->satuanHasil ?: 'butir' }}).</p>
								</div>

								<div>
									<label class="block mb-2 text-sm font-semibold text-gray-700">Angka Kredit Total (Preview)</label>
									<input type="text" id="ak_preview" readonly
										class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm sm:text-sm text-gray-700"
										value="0.00">
									<p class="text-xs text-gray-500 mt-1">Dihitung otomatis dari AK Baku × Volume.</p>
								</div>
							</div>

							<div>
								<label class="block mb-2 text-sm font-semibold text-gray-700">Uraian / Deskripsi Kegiatan</label>
								<input type="text" name="deskripsi_kegiatan" required placeholder="Contoh: Nama Perguruan Tinggi, Judul Penelitian, atau Nama Mata Kuliah"
									class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
							</div>

							<div>
								<label class="block mb-2 text-sm font-semibold text-gray-700">Link Bukti Pendukung</label>
								<input type="url" name="link_bukti_pendukung" required placeholder="https://drive.google.com/..."
									class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
							</div>
						</div>
					</div>

					<div class="flex justify-end pt-6">
						<button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
							Simpan Data {{ ucfirst(str_replace('-', ' ', $category)) }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const jenisSelect = document.getElementById('id_jenis_input');
		const volumeInput = document.getElementById('volume');
		const akPreview = document.getElementById('ak_preview');

		function updateAkPreview() {
			const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
			const nilaiBaku = parseFloat(selectedOption.getAttribute('data-nilai')) || 0;
			const volume = parseFloat(volumeInput.value) || 1;
			const total = nilaiBaku * volume;
			akPreview.value = total.toFixed(3);
		}

		if (jenisSelect) {
			jenisSelect.addEventListener('change', updateAkPreview);
		}
		if (volumeInput) {
			volumeInput.addEventListener('input', updateAkPreview);
		}
	});
</script>

{{-- Script untuk generate random value dari masing masing kolom isian (jika ada) --}}
@if ($category === 'pendidikan')
<script>
	function generateRandomPendidikan() {
		const jenjangOptions = ["101", "104", "105", "106", "107"];
		const randomJenjang = jenjangOptions[Math.floor(Math.random() * jenjangOptions.length)];
		document.querySelector('select[name="id_jenis_input"]').value = randomJenjang;

		const randomDeskripsi = `PT Contoh, Program Studi Contoh, Gelar ${randomJenjang}`;
		document.querySelector('input[name="deskripsi_kegiatan"]').value = randomDeskripsi;

		const randomLink = `https://drive.google.com/ijazah-${randomJenjang}`;
		document.querySelector('input[name="link_bukti_pendukung"]').value = randomLink;
	}

	function generateRandomDiklat() {
		const randomDeskripsi = `Diklat Prajabatan Contoh ${Math.floor(Math.random() * 100)}`;
		document.querySelector('input[name="details[diklat][deskripsi_kegiatan]"]').value = randomDeskripsi;

		const randomLink = `https://drive.google.com/sertifikat-diklat-${Math.floor(Math.random() * 100)}`;
		document.querySelector('input[name="details[diklat][link_bukti_pendukung]"]').value = randomLink;
	}
</script>
@endif
