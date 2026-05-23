@php
    use App\Helpers\PhoneHelper;
    use Carbon\Carbon;
    $active_sidebar = 'History Jabatan Fungsional Akademik (JFA)';
@endphp

@extends('kelola_data.base-profile')

@section('title-the-page')
    {{ $active_sidebar }}
@endsection

@section('content-profile')
    <!-- SYSTEM CONTAINER (APPLE TIMELINE STYLE) -->
    <div class="w-full max-w-3xl mx-auto my-12 font-sans antialiased px-6 text-[#1d1d1f]">

        <!-- HEADER -->
        <div class="mb-12 pb-6 border-b border-[#e8e8ed]">
            <h2 class="text-3xl font-bold tracking-tight text-[#1d1d1f]">
                Riwayat Jabatan Fungsional
            </h2>
            <p class="text-base text-[#86868b] mt-2">
                Perjalanan garis waktu dan dokumentasi resmi karier akademik Anda.
            </p>
        </div>

        <!-- MAIN TIMELINE STREAM -->
        <!-- Garis vertikal tipis (#e8e8ed) mengikat seluruh history dari atas sampai bawah -->
        <div class="relative border-l-2 border-[#e8e8ed] ml-4 md:ml-6 space-y-12">

            {{-- {{ dd($history) }} --}}
            @forelse ($history as $data)

            <div class="relative pl-8 md:pl-10">
                @if ($data->is_active)
                        <span
                            class="absolute -left-[11px] top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white ring-4 ring-white shadow-[0_2px_8px_rgba(0,0,0,0.15)]">
                            <!-- Titik hijau kecil penanda AKTIF -->
                            <span class="h-2.5 w-2.5 rounded-full bg-[#2e7d32]"></span>
                        </span>
                    @endif

                    <div class="space-y-4">
                        <!-- Baris Judul & Status -->
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-2xl font-bold tracking-tight text-[#1d1d1f]">{{ $data->jfa->nama_jabatan }}</h3>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#e8f5e9] text-[#2e7d32] border border-[#c8e6c9]">
                                Sedang Berlangsung
                            </span>
                        </div>

                        <!-- Detail Tanggal Kerja -->
                        <div class="text-[15px] text-[#515154]">
                            <p class="font-medium">
                                Terhitung Mulai Tanggal (TMT): <span
                                    class="text-[#1d1d1f] font-semibold bg-[#f5f5f7] px-2 py-1 rounded ml-1">{{ $data->tmt_mulai }}</span>
                            </p>
                            <p>
                                TMT Berakhir: <span
                                    class="text-[#e3342f] font-semibold bg-[#fdf2f2] px-2 py-0.5 rounded border border-[#fde8e8]">{{ $data->tmt_mulai }}</span>
                            </p>
                        </div>

                        <!-- Lampiran Dokumen SK (Gaya Apple Button: Bulat, tipis, bersih) -->
                        <div class="flex flex-wrap gap-3 pt-1">
                            <a href="{{ route('manage.sk.view', ['id_sk_or_sk_number' => $data->sk_dikti->id]) }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#0066cc] bg-white border border-[#d2d2d7] rounded-full hover:bg-[#f5f5f7] hover:border-[#86868b] transition-all shadow-xs group">
                                <span>SK DIKTI: {{$data->sk_dikti->no_sk}}</span>
                                <svg class="w-3.5 h-3.5 ml-1.5 text-[#86868b] group-hover:text-[#0066cc] transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>

                            <a href="{{ route('manage.sk.view', ['id_sk_or_sk_number' => $data->sk_ypt->id]) }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#0066cc] bg-white border border-[#d2d2d7] rounded-full hover:bg-[#f5f5f7] hover:border-[#86868b] transition-all shadow-xs group">
                                <span>SK YPT: {{$data->sk_ypt->no_sk}}</span>
                                <svg class="w-3.5 h-3.5 ml-1.5 text-[#86868b] group-hover:text-[#0066cc] transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
            </div>
            @empty
                Belum Ada Data
            @endforelse
            <!-- ========================================================================= -->
            <!-- 1. JABATAN AKTIF (Titik Teratas Garis Waktu) -->
            <!-- ========================================================================= -->
            {{-- <div class="relative pl-8 md:pl-10">
                <!-- Indikator Titik Timeline Premium (Khas Apple: Bulatan putih bersih dengan shadow lembut) -->
                <span
                    class="absolute -left-[11px] top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white ring-4 ring-white shadow-[0_2px_8px_rgba(0,0,0,0.15)]">
                    <!-- Titik hijau kecil penanda AKTIF -->
                    <span class="h-2.5 w-2.5 rounded-full bg-[#2e7d32]"></span>
                </span>

                <!-- Konten Histori -->
                <div class="space-y-4">
                    <!-- Baris Judul & Status -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <h3 class="text-2xl font-bold tracking-tight text-[#1d1d1f]">Lektor Kepala</h3>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#e8f5e9] text-[#2e7d32] border border-[#c8e6c9]">
                            Jabatan Sekarang
                        </span>
                    </div>

                    <!-- Detail Tanggal Kerja -->
                    <div class="text-[15px] text-[#515154]">
                        <p class="font-medium">
                            Terhitung Mulai Tanggal (TMT): <span
                                class="text-[#1d1d1f] font-semibold bg-[#f5f5f7] px-2 py-1 rounded ml-1">01 September
                                2023</span>
                        </p>
                        <!-- Tanggal berakhir kosong/aktif, disembunyikan sesuai request -->
                    </div>

                    <!-- Lampiran Dokumen SK (Gaya Apple Button: Bulat, tipis, bersih) -->
                    <div class="flex flex-wrap gap-3 pt-1">
                        <a href="https://example.com/sk-dikti-1.pdf" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#0066cc] bg-white border border-[#d2d2d7] rounded-full hover:bg-[#f5f5f7] hover:border-[#86868b] transition-all shadow-xs group">
                            <span>SK DIKTI</span>
                            <svg class="w-3.5 h-3.5 ml-1.5 text-[#86868b] group-hover:text-[#0066cc] transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>

                        <a href="https://example.com/sk-ypt-1.pdf" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#0066cc] bg-white border border-[#d2d2d7] rounded-full hover:bg-[#f5f5f7] hover:border-[#86868b] transition-all shadow-xs group">
                            <span>SK YPT</span>
                            <svg class="w-3.5 h-3.5 ml-1.5 text-[#86868b] group-hover:text-[#0066cc] transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. JABATAN MASA LALU (Alur Tengah Garis Waktu) -->
            <!-- ========================================================================= -->
            <div class="relative pl-8 md:pl-10">
                <!-- Indikator Titik Kosong / Selesai -->
                <span
                    class="absolute -left-[9px] top-2 flex h-4 w-4 items-center justify-center rounded-full bg-[#d2d2d7] ring-4 ring-white"></span>

                <div class="space-y-3">
                    <!-- Baris Judul -->
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold tracking-tight text-[#515154]">Lektor</h3>
                    </div>

                    <!-- Detail Tanggal Kerja (Wajib menampilkan tanggal berakhir karena sudah tidak aktif) -->
                    <div class="text-[15px] text-[#86868b] space-y-1.5">
                        <p>TMT Mulai: <span class="text-[#1d1d1f] font-medium">01 Agustus 2020</span></p>
                        <p>
                            TMT Berakhir: <span
                                class="text-[#e3342f] font-semibold bg-[#fdf2f2] px-2 py-0.5 rounded border border-[#fde8e8]">31
                                Agustus 2023</span>
                        </p>
                    </div>

                    <!-- Lampiran Dokumen SK (Contoh SK DIKTI ada, SK YPT Kosong) -->
                    <div class="flex flex-wrap gap-3 pt-1">
                        <a href="https://example.com/sk-dikti-2.pdf" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#0066cc] bg-white border border-[#d2d2d7] rounded-full hover:bg-[#f5f5f7] transition-all shadow-xs group">
                            <span>SK DIKTI</span>
                            <svg class="w-3.5 h-3.5 ml-1.5 text-[#86868b] group-hover:text-[#0066cc] transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>

                        <!-- SK Kosong Bergaya Apple Placeholder (Abu-abu putus-putus samar) -->
                        <span
                            class="inline-flex items-center px-4 py-2 text-sm font-normal text-[#86868b] bg-transparent border border-dashed border-[#d2d2d7] rounded-full italic select-none">
                            SK YPT Belum terisi
                        </span>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. JABATAN MASA LALU LAINNYA (Titik Terbawah Garis Waktu) -->
            <!-- ========================================================================= -->
            <div class="relative pl-8 md:pl-10">
                <!-- Indikator Titik -->
                <span
                    class="absolute -left-[9px] top-2 flex h-4 w-4 items-center justify-center rounded-full bg-[#d2d2d7] ring-4 ring-white"></span>

                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold tracking-tight text-[#515154]">Asisten Ahli</h3>
                    </div>

                    <div class="text-[15px] text-[#86868b] space-y-1.5">
                        <p>TMT Mulai: <span class="text-[#1d1d1f] font-medium">01 Mei 2018</span></p>
                        <p>
                            TMT Berakhir: <span
                                class="text-[#e3342f] font-semibold bg-[#fdf2f2] px-2 py-0.5 rounded border border-[#fde8e8]">31
                                Juli 2020</span>
                        </p>
                    </div>

                    <!-- Kedua SK Kosong -->
                    <div class="flex flex-wrap gap-3 pt-1">
                        <span
                            class="inline-flex items-center px-4 py-2 text-sm font-normal text-[#86868b] bg-transparent border border-dashed border-[#d2d2d7] rounded-full italic select-none">
                            SK DIKTI Belum terisi
                        </span>
                        <span
                            class="inline-flex items-center px-4 py-2 text-sm font-normal text-[#86868b] bg-transparent border border-dashed border-[#d2d2d7] rounded-full italic select-none">
                            SK YPT Belum terisi
                        </span>
                    </div>
                </div>
            </div> --}}

        </div>
    </div>
@endsection
