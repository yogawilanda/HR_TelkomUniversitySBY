<div class="md:col-span-2 p-6 border rounded-lg bg-white shadow-sm flex flex-col">
    @if ($hasNoPengajuan == true)
    <div class="flex-1 flex flex-col justify-center" id="userHasSubmissionDialog">
        <h3 class="text-lg font-medium text-gray-900">Informasi KUM</h3>
        <p class="text-sm text-gray-600">Anda belum memiliki pengajuan DUPAK. Silakan buat pengajuan baru untuk melihat informasi KUM Anda.</p>
        <a href="{{ route('dupak.pengajuan.create', ['userId' => $user->id]) }}" class="mt-4 inline-block px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-950 w-max">
            Buat Pengajuan Baru
        </a>
    </div>
    @else
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Informasi KUM</h3>
            <p class="text-sm text-gray-600">Ringkasan KUM, jabatan, dan progress</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-500">Jabatan</span>
            <div class="text-sm font-semibold">{{ $jfa['current'] ?? 'Belum diisi' }}</div>
        </div>
    </div>

    {{-- KUM Numbers --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
        <div>
            <span class="text-xs text-gray-500">KUM Saat Ini</span>
            <div class="text-2xl font-bold text-blue-900">
                {{ $kum['current'] }}
            </div>
        </div>

        <div>
            <span class="text-xs text-gray-500">Target KUM ({{ $jfa['next'] ?? 'Belum diisi' }})</span>
            <div class="text-lg font-semibold">
                {{ $kum['target'] }}
            </div>
        </div>

        <div>
            <span class="text-xs text-gray-500">Tersisa</span>
            <div class="text-lg font-semibold">
                {{ $kum['remaining'] }}
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="mt-4">
        <div class="flex justify-between text-sm text-gray-600">
            <span>Progress menuju target</span>
            <span class="font-medium">{{ number_format($kum['percent'], 0) }}%</span>
        </div>

        <div class="w-full h-4 bg-gray-200 rounded-full mt-2 overflow-hidden">
            <div id="progress-bar" class="h-full {{ $kum['statusColor'] }}" data-percent="{{ $kum['percent'] }}"></div>
        </div>

        <div class="text-xs text-gray-500 mt-2">
            Terakhir diperbarui: {{ $kum['updatedAtFormatted'] ?? 'Tidak tersedia' }}
        </div>
    </div>

    {{-- Divider --}}
    <div class="border-t border-gray-100 my-6"></div>

    {{-- Rincian Angka Kredit Spesifik --}}
    <div class="mb-6">
        <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Rincian Per Komponen</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="text-xs text-gray-500 block">Pendidikan</span>
                <div class="text-lg font-bold text-gray-800">{{ $kum['pendidikan'] ?? '0' }}</div>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="text-xs text-gray-500 block">Pelaksanaan Pendidikan</span>
                <div class="text-lg font-bold text-gray-800">{{ $kum['pelaksanaan_pendidikan'] ?? '0' }}</div>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="text-xs text-gray-500 block">Penelitian</span>
                <div class="text-lg font-bold text-gray-800">{{ $kum['penelitian'] ?? '0' }}</div>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="text-xs text-gray-500 block">Pengabdian</span>
                <div class="text-lg font-bold text-gray-800">{{ $kum['pengabdian'] ?? '0' }}</div>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="text-xs text-gray-500 block">Penunjang</span>
                <div class="text-lg font-bold text-gray-800">{{ $kum['penunjang'] ?? '0' }}</div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-2 mt-auto pt-4">
        @if($submissions['latest'])
        <a href="{{ route('dupak.pengajuan.show', $submissions['latest']->id) }}"
            class="px-4 py-2 text-sm text-white bg-blue-900 rounded hover:bg-blue-950">Detail Kegiatan</a>
        @else
        <button disabled title="Anda belum memiliki pengajuan"
            class="px-4 py-2 text-sm text-white bg-gray-400 rounded cursor-not-allowed">Detail Kegiatan</button>
        @endif

        <a onclick="openModal()" class="px-4 py-2 text-sm text-blue-900 border border-blue-900 rounded hover:bg-indigo-50 cursor-pointer">Tambahkan Kegiatan</a>
    </div>
    @endif
</div>

