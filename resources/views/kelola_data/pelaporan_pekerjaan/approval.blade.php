@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
        <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <h2 class="text-2xl font-medium">Approval Laporan</h2>
            </div>
            <p class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Review dan setujui laporan</p>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.75px]">
            <div class="hidden sm:flex items-center gap-2">
                @include('kelola_data.parts.target_kinerja_toolbar')
            </div>
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white p-4 rounded shadow max-w-2xl">
        <div class="mb-4">
            <strong>Target Harian:</strong>
            <div>{{ $item->targetHarian->pekerjaan ?? '-' }}</div>
        </div>
        <div class="mb-4">
            <strong>Target Pribadi:</strong>
            <div>
                @php $tk = $item->targetHarian->targetKinerja ?? null; @endphp
                @if($tk && $tk->status === 'pribadi')
                    <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800">Ya</span>
                    <div class="text-sm text-gray-700 mt-1">Penanggung:
                        @if($tk->pegawai && $tk->pegawai->isNotEmpty())
                            {{ $tk->pegawai->pluck('nama_lengkap')->join(', ') }}
                        @else
                            -
                        @endif
                    </div>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700">Tidak</span>
                @endif
            </div>
        </div>
        <div class="mb-4">
            <strong>Realisasi:</strong>
            <div>{{ $item->realisasi ?? '-' }}</div>
        </div>
        <div class="mb-4">
            <strong>Realisasi Jumlah (efektif):</strong>
            <div>{{ $item->effective_jumlah ?? '-' }}</div>
            @if($item->approved_jumlah !== null && $item->approved_jumlah != $item->realisasi_jumlah)
                <div class="text-sm text-gray-500">(Original: {{ $item->realisasi_jumlah ?? '-' }})</div>
            @endif
        </div>
        <div class="mb-4">
            <strong>Realisasi Waktu (menit, efektif):</strong>
            <div>{{ $item->effective_waktu_minutes ?? '-' }}</div>
            @if($item->approved_waktu_minutes !== null && $item->approved_waktu_minutes != $item->realisasi_waktu_minutes)
                <div class="text-sm text-gray-500">(Original: {{ $item->realisasi_waktu_minutes ?? '-' }})</div>
            @endif
        </div>

        <form action="{{ route('manage.target-kinerja.harian.reports.approve', $item->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Approved Jumlah</label>
                    <input type="number" name="approved_jumlah" value="{{ old('approved_jumlah', $item->approved_jumlah) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Approved Waktu (menit)</label>
                    <input type="number" name="approved_waktu_minutes" value="{{ old('approved_waktu_minutes', $item->approved_waktu_minutes) }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Pencapaian (%)</label>
                    <input type="number" name="pencapaian_percent" value="{{ old('pencapaian_percent', $item->pencapaian_percent) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Evidence (link)</label>
                    <input type="text" name="evidence" value="{{ old('evidence', $item->evidence) }}" class="w-full border rounded px-3 py-2" placeholder="https://...">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Set Status Penugasan</label>
                <select name="assignment_status" class="w-full border rounded px-3 py-2">
                    <option value="in_progress" {{ ((old('assignment_status') ?? $item->status) === 'in_progress') ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ ((old('assignment_status') ?? $item->status) === 'completed') ? 'selected' : '' }}>Approved</option>
                    <option value="cancelled" {{ ((old('assignment_status') ?? $item->status) === 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Mengubah status ini akan memperbarui status assignment pada target terkait untuk pengirim laporan.</p>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <i class="bi bi-check-lg"></i>
                    <span>Setujui</span>
                </button>
                <a href="{{ route('manage.target-kinerja.harian.reports') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    <i class="bi bi-x-lg"></i>
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
@endsection
