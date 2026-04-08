@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex flex-col md:flex-row items-center gap-3 self-stretch px-1 pt-4 pb-3">
        <div class="flex w-full flex-col gap-1 grow">
            <div class="flex items-center gap-2">
                <span class="font-medium text-2xl text-[#101828]">Laporan Target Kinerja</span>
            </div>
            <span class="font-normal text-sm text-[#1f2028]">Rekap data target kinerja pegawai/dosen</span>
        </div>
        <div class="flex items-center gap-2">
            @include('kelola_data.parts.target_kinerja_toolbar')
        </div>
    </div>
@endsection
    @section('page-name')
        <div class="flex flex-col md:flex-row items-center gap-[11.75px] self-stretch px-1 pt-[14.68px] pb-[13.95px]">
            <div class="flex w-full flex-col gap-[2.93px] grow">
                <div class="flex items-center gap-[5.87px] self-stretch">
                    <span class="font-medium text-2xl leading-[20.56px] text-[#101828]">Laporan Target Kinerja</span>
                </div>
                <span class="font-normal text-[10.28px] leading-[14.68px] text-[#1f2028]">Rekap data target kinerja pegawai/dosen</span>
            </div>
            <div class="flex items-center w-full justify-end gap-[11.75px]">
                <div class="hidden sm:flex items-center gap-2">
                    @include('kelola_data.parts.target_kinerja_toolbar')
                </div>
            </div>
        </div>
    @endsection

@section('content-base')
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">
        @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>@endif
        <form method="GET" class="flex flex-wrap gap-4 mb-4">
            <div>
                <label class="block text-xs mb-1">Pegawai</label>
                <select name="user_id" class="border rounded px-2 py-1 min-w-[180px]">
                    <option value="">Semua</option>
                    @foreach($allUsers as $user)
                        <option value="{{ $user->id }}" @if(request('user_id') == $user->id) selected @endif>{{ $user->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1">Target</label>
                <select name="target_id" class="border rounded px-2 py-1 min-w-[180px]">
                    <option value="">Semua</option>
                    @foreach($allTargets as $target)
                        <option value="{{ $target->id }}" @if(request('target_id') == $target->id) selected @endif>{{ $target->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1">Status</label>
                <select name="status" class="border rounded px-2 py-1 min-w-[120px]">
                    <option value="">Semua</option>
                    <option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
                    <option value="in_progress" @if(request('status')=='in_progress') selected @endif>In Progress</option>
                    <option value="completed" @if(request('status')=='completed') selected @endif>Completed</option>
                    <option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
            </div>
        </form>

        @if($targetKinerjaList->isEmpty())
            <div class="px-4 py-8 text-center text-gray-500">Tidak ada data laporan target kinerja</div>
        @else
            <x-tb id="laporanTargetTable">
                <x-slot:table_header>
                    <x-tb-td nama="pegawai" sorting=true>Pegawai/Dosen</x-tb-td>
                    <x-tb-td nama="target_kinerja" sorting=true>Target Kinerja</x-tb-td>
                    <x-tb-td nama="bobot" sorting=true>Bobot</x-tb-td>
                    <x-tb-td nama="tanggal_mulai" sorting=true>Tanggal Mulai</x-tb-td>
                    <x-tb-td nama="tanggal_selesai" sorting=true>Tanggal Selesai</x-tb-td>
                    <x-tb-td nama="status" sorting=true>Status</x-tb-td>
                    <x-tb-td nama="catatan" sorting=false>Catatan</x-tb-td>
                </x-slot:table_header>
                <x-slot:table_column>
                    @foreach($targetKinerjaList as $target)
                        @foreach($target->pegawai as $pegawai)
                            <x-tb-cl id="{{ $target->id }}-{{ $pegawai->id }}">
                                <x-tb-cl-fill>{{ $pegawai->nama_lengkap }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $target->nama }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $target->bobot }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $pegawai->pivot->tanggal_mulai }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $pegawai->pivot->tanggal_selesai }}</x-tb-cl-fill>
                                <x-tb-cl-fill class="capitalize">{{ $pegawai->pivot->status }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $pegawai->pivot->catatan }}</x-tb-cl-fill>
                            </x-tb-cl>
                        @endforeach
                    @endforeach
                </x-slot:table_column>
            </x-tb>
        @endif

        {{-- Laporan terkini (submitted reports) --}}
        <div class="mt-6">
            <h3 class="text-lg font-medium mb-2">Laporan Terkini</h3>
            @if(isset($pelaporanItems) && $pelaporanItems->isNotEmpty())
                <x-tb id="laporanTerkiniTable">
                    <x-slot:table_header>
                        {{-- <x-tb-td nama="no" sorting=false>No</x-tb-td> --}}
                        <x-tb-td nama="pekerjaan" sorting=true>Pekerjaan</x-tb-td>
                        <x-tb-td nama="realisasi" sorting=false>Realisasi</x-tb-td>
                        <x-tb-td nama="jumlah" sorting=true>Jumlah</x-tb-td>
                        <x-tb-td nama="waktu" sorting=true>Waktu (menit)</x-tb-td>
                        <x-tb-td nama="tanggal" sorting=true>Tanggal</x-tb-td>
                        <x-tb-td nama="status" sorting=true>Status</x-tb-td>
                    </x-slot:table_header>
                    <x-slot:table_column>
                        @foreach($pelaporanItems as $i => $it)
                            <x-tb-cl id="pel-{{ $it->id }}">
                                {{-- <x-tb-cl-fill>{{ $i+1 }}</x-tb-cl-fill> --}}
                                <x-tb-cl-fill>{{ $it->targetHarian->pekerjaan ?? '-' }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ Str::limit($it->realisasi, 60) }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $it->effective_jumlah ?? '-' }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $it->effective_waktu_minutes ?? '-' }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ $it->created_at->format('d/m/Y H:i') }}</x-tb-cl-fill>
                                <x-tb-cl-fill>{{ ucfirst($it->status ?? 'pending') }}</x-tb-cl-fill>
                            </x-tb-cl>
                        @endforeach
                    </x-slot:table_column>
                </x-tb>
            @else
                <div class="px-4 py-4 text-sm text-gray-500">Belum ada laporan yang dikirim.</div>
            @endif
        </div>
    </div>
@endsection
