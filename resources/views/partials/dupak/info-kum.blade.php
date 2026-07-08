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
        {{-- Header Informasi --}}
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Informasi KUM</h3>
                <!-- <p class="text-xs text-gray-500 italic">Terakhir diperbarui: {{ $kum['updatedAtFormatted'] ?? 'Tidak tersedia' }}</p> -->
            </div>
            <div class="text-right bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                <span class="text-[10px] uppercase font-bold text-blue-600 block">Jabatan</span>
                <div class="text-sm font-bold text-blue-900">{{ $jfa['current'] ?? 'Belum diisi' }}</div>
            </div>
        </div>

        {{-- KUM Numbers (Sekarang 4 Kolom) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
            {{-- 1. KUM SAAT INI --}}
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">KUM Saat Ini</span>
                <div class="text-3xl font-black text-blue-900 mt-1 leading-none">
                    {{ $kum['current'] }}
                </div>
            </div>

            {{-- 2. STATUS PENDING (Ditaruh di sebelah kiri/setelah KUM Saat Ini) --}}
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sedang Diajukan</span>
                <div class="mt-1">
                    @if((float)($kum['pending_kum'] ?? 0) > 0)
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-blue-900">+{{ $kum['pending_kum'] }}</span>
                        </div>
                        @php
                            $subStatus = $submissions['latest'] ? $submissions['latest']->status : 'Draft';
                            $statusTexts = [
                                'Draft' => 'Belum dikirim (Draft)',
                                'Pending' => 'Belum dikirim (Draft)',
                                'Diajukan' => 'Menunggu evaluasi TPAK',
                                'Menunggu' => 'Menunggu evaluasi TPAK',
                                'Revisi' => 'Perlu perbaikan/revisi',
                            ];
                            $statusText = $statusTexts[$subStatus] ?? 'Dalam antrean';
                        @endphp
                        <p class="text-[10px] text-gray-400 leading-tight">{{ $statusText }}</p>
                    @else
                        <span class="text-2xl font-bold text-gray-300">0</span>
                        <p class="text-[10px] text-gray-400">Tidak ada antrean</p>
                    @endif
                </div>
            </div>

            {{-- 3. TARGET --}}
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Target ({{ $jfa['next'] ?? 'N/A' }})</span>
                <div class="text-2xl font-bold text-gray-800 mt-1">
                    {{ $kum['target'] }}
                </div>
            </div>

            {{-- 4. SISA --}}
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tersisa</span>
                <div class="text-2xl font-bold mt-1 {{ (float)$kum['remaining'] <= 0 ? 'text-green-600' : 'text-gray-800' }}">
                    @if((float)$kum['remaining'] <= 0)
                        <span class="flex items-center gap-1"><i class="fas fa-check-circle text-xs"></i> 0.00</span>
                    @else
                        {{ $kum['remaining'] }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mt-8 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <div class="flex justify-between text-sm font-bold text-gray-700 mb-2">
                <span class="flex items-center gap-2">
                    Progress Capaian
                </span>
                <span>{{ number_format($kum['percent'], 0) }}%</span>
            </div>
            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                <div id="progress-bar" class="h-full {{ $kum['statusColor'] }} transition-all duration-500" style="width: {{ $kum['percent'] }}%" data-percent="{{ $kum['percent'] }}"></div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-100 my-8"></div>

        {{-- Rincian Angka Kredit Spesifik --}}
        <!-- revisi: kolom pendidikan dan pelaksanaan pendidikan seakan dicampur menjadi satu untuk menjadi acuan di excel saat melihat rincian, ketika sudah memenuhi syarat yang berdasarkan acuan dari rincian maka menjadi centang hijau, ketika belum maka masih ongoing atau tidak ada simbol. -->         
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-4">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Rincian Per Komponen</h4>
                <div class="h-[1px] flex-1 bg-gray-100"></div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $komponen = [
                        ['label' => 'Pendidikan', 'key' => 'pendidikan', 'info' => 'Unsur ijazah dan pendidikan formal'],
                        ['label' => 'Pengajaran', 'key' => 'pelaksanaan_pendidikan', 'info' => 'Kegiatan belajar mengajar'],
                        ['label' => 'Penelitian', 'key' => 'penelitian', 'info' => 'Karya ilmiah dan publikasi'],
                        ['label' => 'Pengabdian', 'key' => 'pengabdian', 'info' => 'Kegiatan pengabdian masyarakat'],
                        ['label' => 'Penunjang', 'key' => 'penunjang', 'info' => 'Kegiatan pendukung lainnya'],
                    ];
                @endphp

                @foreach($komponen as $item)
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm group hover:border-blue-300 transition-colors">
                    <div class="flex items-center justify-between mb-1">
                        <!-- judul item komponen -->
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $item['label'] }}</span>
                        <!-- icon item komponen -->
                        <i class="fas fa-info-circle text-[10px] text-gray-300 cursor-help" title="{{ $item['info'] }}"></i>
                    </div>
                    <div class="flex text-xl font-black text-gray-800">
                        <!-- angka kredit yang muncul di rincian per komponen -->
                        {{ $kum[$item['key']]['approved'] ?? '0.00' }}
                        <!-- update request Pak Dahliar : Penambahan icon centang apabila sudah komponen sudah memenuhi syarat pengajuan per komponennya -->
                        <!-- tambahkan if statement : jika user sudah memenuhi maka munculkan centangnya. -->
                        @if(isset($kum[$item['key']]['approved']) && (float)$kum[$item['key']]['approved'] >= ($kum[$item['key']]['target'] ?? 999))
                        <div class="text-[10px] font-bold text-blue-600 mt-0.5" title="Sedang Diajukan">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        @endif
                    </div>
                    @if(isset($kum[$item['key']]['pending']) && (float)$kum[$item['key']]['pending'] > 0)
                        <div class="text-[10px] font-bold text-yellow-600 mt-0.5" title="Sedang Diajukan">
                            +{{ $kum[$item['key']]['pending'] }} pending
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-2 mt-auto pt-4">
            @if($submissions['latest'])
                <a href="{{ route('dupak.pengajuan.show', $submissions['latest']->id) }}"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-blue-900 rounded-lg hover:bg-blue-950 transition-all flex-1 text-center">
                    <i class="fas fa-eye mr-1"></i> Detail Kegiatan
                </a>
            @else
                <button disabled class="px-5 py-2.5 text-sm font-bold text-white bg-gray-400 rounded-lg cursor-not-allowed flex-1">
                    Detail Kegiatan
                </button>
            @endif

            @if((float)$kum['current'] >= (float)$kum['target'])
                <button disabled title="Target KUM sudah terpenuhi! Anda tidak perlu menambahkan kegiatan lagi." class="px-5 py-2.5 text-sm font-bold text-green-600 border border-green-300 bg-green-50 rounded-lg cursor-not-allowed flex-1 text-center">
                    <i class="fas fa-check-circle mr-1"></i> Target Terpenuhi
                </button>
            @elseif($submissions['latest'] && in_array($submissions['latest']->status, ['Draft', 'Pending', 'Revisi']))
                <a onclick="openModal()" class="px-5 py-2.5 text-sm font-bold text-blue-900 border border-blue-900 rounded-lg hover:bg-blue-50 cursor-pointer flex-1 text-center transition-colors">
                    <i class="fas fa-plus-circle mr-1"></i> Tambahkan Kegiatan
                </a>
            @else
                <button disabled title="Anda harus memiliki pengajuan berstatus Draft/Revisi untuk menambah kegiatan baru" class="px-5 py-2.5 text-sm font-bold text-gray-400 border border-gray-300 rounded-lg cursor-not-allowed flex-1 text-center opacity-60">
                    <i class="fas fa-plus-circle mr-1"></i> Tambahkan Kegiatan
                </button>
            @endif
        </div>
    @endif
</div>