@extends('layouts.app')

@section('content')

<div class="mt-16 md:ml-64 sm:ml-12 lg:ml-64">
	<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
		<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				<a href="{{ route('dupak.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>

				<h1 class="mb-3 text-2xl font-semibold">Formulir Detil Pengajuan DUPAK</h1>
				<h2 class="text-xl">Daftar Usulan Penetapan Angka Kredit</h2>

				<form method="POST" action="{{ route('dupak.dupak.detil_pengajuan.store', [$category, $pengajuan->id]) }}" class="space-y-6"></form>
				@csrf
				<input type="hidden" name="id_komponen" value="{{ $komponen->id }}">

				<!-- Unsur Utama -->
				<div class="p-4 rounded-lg bg-gray-50">
					<h2 class="mb-2 text-2xl font-medium text-gray-900">{{ $komponen->nama }}</h2>
					<p class="mb-6 text-gray-600">Sub-kategori: {{ ucfirst(str_replace('-', ' ', $category)) }}</p>

					<div class="space-y-4 bg-white p-4 border rounded-lg shadow-sm">
						<div>
							<label class="block mb-2 text-sm font-semibold text-gray-700">Pilih Jenis Kegiatan / Jenjang</label>
							<select name="id_jenis_input" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
								<option value="">-- Pilih Salah Satu --</option>
								@foreach($jenisInputs as $input)
								@endforeach
							</select>
						</div>

						<div>
							<label class="block mb-2 text-sm font-semibold text-gray-700">Uraian / Deskripsi Kegiatan</label>
							<input type="text" name="deskripsi_kegiatan" required placeholder="Contoh: Nama Perguruan Tinggi, Judul Penelitian, atau Nama Mata Kuliah"
								class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<label class="block mb-2 text-sm font-semibold text-gray-700">Volume / Jumlah</label>
								<input type="number" step="0.01" name="volume" value="1" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
							</div>
							<div>
								<label class="block mb-2 text-sm font-semibold text-gray-700">Link Bukti Pendukung</label>
								<input type="url" name="link_bukti_pendukung" required placeholder="https://drive.google.com/..."
									class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
							</div>
						</div>
					</div>
				</div>

				<div class="flex justify-end pt-6">
					<!-- <button type="button" onclick="window.location='{{ route('dupak.pengajuan.show', $pengajuan->id) }}'" class="px-4 py-2 mr-4 text-white bg-gray-500 rounded-md hover:bg-gray-600">
						Batal
					</button> -->
					<button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
						Simpan Data {{ ucfirst($category) }}
					</button>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

{{-- Script untuk generate random value dari masing masing kolom isian (jika ada) --}}
@if ($category === 'pendidikan')
<script>
	function generateRandomPendidikan() {
		const jenjangOptions = ["101", "104", "105", "106", "107"];
		const randomJenjang = jenjangOptions[Math.floor(Math.random() * jenjangOptions.length)];
		document.querySelector('select[name="details[formal][idJenisInput]"]').value = randomJenjang;

		const randomDeskripsi = `PT Contoh, Program Studi Contoh, Gelar ${randomJenjang}`;
		document.querySelector('input[name="details[formal][deskripsi_kegiatan]"]').value = randomDeskripsi;

		const randomLink = `https://drive.google.com/ijazah-${randomJenjang}`;
		document.querySelector('input[name="details[formal][link_bukti_pendukung]"]').value = randomLink;
	}

	function generateRandomDiklat() {
		const randomDeskripsi = `Diklat Prajabatan Contoh ${Math.floor(Math.random() * 100)}`;
		document.querySelector('input[name="details[diklat][deskripsi_kegiatan]"]').value = randomDeskripsi;

		const randomLink = `https://drive.google.com/sertifikat-diklat-${Math.floor(Math.random() * 100)}`;
		document.querySelector('input[name="details[diklat][link_bukti_pendukung]"]').value = randomLink;
	}
</script>
@endif