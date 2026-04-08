@php
    // Determine active route for each button
    $isList = request()->routeIs('manage.target-kinerja.list');
    $isInput = request()->routeIs('manage.target-kinerja.input');
    $isLaporan = request()->routeIs('manage.target-kinerja.laporan');
    $isHarianList = request()->routeIs('manage.target-kinerja.harian.*') && request()->routeIs('manage.target-kinerja.harian.list');
    $isHarianInput = request()->routeIs('manage.target-kinerja.harian.input');
    $isReports = request()->routeIs('manage.target-kinerja.harian.reports') || request()->routeIs('manage.target-kinerja.harian.reports.*');
    $btnBase = 'inline-flex items-center gap-2 text-xs font-medium px-3 py-1.5 rounded border transition';
    $btnInactive = $btnBase . ' bg-white text-gray-700 border-gray-200 hover:bg-gray-50';
    $btnActive = $btnBase . ' bg-[#0070ff] text-white border-[#0070ff]';
@endphp

<div class="flex items-center gap-2">
    <a href="{{ route('manage.target-kinerja.list') }}" class="{{ $isList ? $btnActive : $btnInactive }}">Daftar</a>
    {{-- <a href="{{ route('manage.target-kinerja.input') }}" class="{{ $isInput ? $btnActive : $btnInactive }}">Tambah</a> --}}
    <a href="{{ route('manage.target-kinerja.laporan') }}" class="{{ $isLaporan ? $btnActive : $btnInactive }}">Laporan</a>
    <a href="{{ route('manage.target-kinerja.harian.list') }}" class="{{ $isHarianList ? $btnActive : $btnInactive }}">Harian</a>
    {{-- <a href="{{ route('manage.target-kinerja.harian.input') }}" class="{{ $isHarianInput ? $btnActive : $btnInactive }}">Set Harian</a> --}}
    <a href="{{ route('manage.target-kinerja.harian.reports') }}" class="{{ $isReports ? $btnActive : $btnInactive }}">Approval</a>
</div>
