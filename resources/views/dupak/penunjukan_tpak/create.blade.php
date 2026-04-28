{{-- Form penunjukan TPAK sudah tersedia inline di index.blade.php --}}
{{-- Redirect ke halaman index --}}
<script>window.location.href = "{{ route('dupak.penunjukan_tpak.index') }}";</script>
<noscript>
    <meta http-equiv="refresh" content="0;url={{ route('dupak.penunjukan_tpak.index') }}">
    <p>Halaman ini tidak digunakan. <a href="{{ route('dupak.penunjukan_tpak.index') }}">Kembali ke Penunjukan TPAK</a></p>
</noscript>
