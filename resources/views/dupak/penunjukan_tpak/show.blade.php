{{-- Halaman detail penunjukan TPAK --}}
{{-- Redirect ke halaman index karena detail pengajuan sudah tersedia di validasi.show --}}
<script>window.location.href = "{{ route('dupak.penunjukan_tpak.index') }}";</script>
<noscript>
    <meta http-equiv="refresh" content="0;url={{ route('dupak.penunjukan_tpak.index') }}">
    <p>Halaman ini tidak digunakan. <a href="{{ route('dupak.penunjukan_tpak.index') }}">Kembali ke Penunjukan TPAK</a></p>
</noscript>
