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
                Riwayat Jabatan Fungsional Keahlian
            </h2>
            <p class="text-base text-[#86868b] mt-2">
                Perjalanan garis waktu dan dokumentasi resmi karier keahlian Anda.
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
                            <h3 class="text-2xl font-bold tracking-tight text-[#1d1d1f]">{{ $data->data_tpa->nama_jfk }}</h3>
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
                                    class="text-[#e3342f] font-semibold bg-[#fdf2f2] px-2 py-0.5 rounded border border-[#fde8e8]">{{ $data->tmt_selesai }}</span>
                            </p>
                        </div>

                        <!-- Lampiran Dokumen SK (Gaya Apple Button: Bulat, tipis, bersih) -->
                        @if(isset($data->sk_ypt->no_sk))
                        <div class="flex flex-wrap gap-3 pt-1">
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
                        @endif
                    </div>
            </div>
            @empty
                Belum Ada Data
            @endforelse
        </div>
    </div>
@endsection
