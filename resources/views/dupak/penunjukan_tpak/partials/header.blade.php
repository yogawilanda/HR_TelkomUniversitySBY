<a href="{{ route('dupak.dashboard') }}"
	class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">&larr; Kembali ke Dashboard DUPAK</a>
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
	<div>
		<h1 class="text-2xl font-bold text-gray-900 dark:text-white text-left">Penunjukan TPAK - DUPAK</h1>
		<p class="text-sm text-gray-600 dark:text-gray-400 text-left">Monitoring pengajuan, penugasan TPAK, dan
			beban kerja penilai.</p>
	</div>
	<div class="flex gap-2">
		<span
			class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
			<i class="fas fa-calendar-alt mr-2"></i> Periode {{ date('Y') }}
		</span>
	</div>
</div>

@if (session('success'))
<div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
	{{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
	{{ session('error') }}
</div>
@endif