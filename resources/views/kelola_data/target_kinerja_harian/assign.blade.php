<!-- blade: assign for target_kinerja_harian -->
@php
    $active_sidebar = 'Target Kinerja';
@endphp

@extends('kelola_data.base')

@section('page-name')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-medium">Assign Target Kinerja Harian</h2>
            <p class="text-sm text-gray-600">{{ $harian->pekerjaan }} @if($harian->targetKinerja) - {{ $harian->targetKinerja->nama }} @endif</p>
        </div>
        <div class="flex items-center gap-2">
            @include('kelola_data.parts.target_kinerja_toolbar')
        </div>
    </div>
@endsection

@section('content-base')
    <div class="bg-white p-4 rounded-lg shadow max-w-4xl">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <!-- Form Tambah Pegawai ke Target Harian -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold mb-4">Tambah Pegawai</h3>
            <form action="{{ route('manage.target-kinerja.harian.store-assignment', $harian->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Pegawai</label>
                        <select name="user_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawai as $p)
                                @if(!$assignedPegawai->contains($p->id))
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('user_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="w-full border rounded px-3 py-2" required>
                        @error('tanggal_mulai')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="w-full border rounded px-3 py-2" required>
                        @error('tanggal_selesai')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button>
                    <a href="{{ route('manage.target-kinerja.harian.list') }}" class="px-4 py-2 bg-gray-300 rounded text-gray-700">Kembali</a>
                </div>
            </form>
        </div>

        <!-- Daftar Pegawai yang Sudah Ditugaskan ke Target Harian -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Pegawai yang Ditugaskan ({{ $assignedPegawai->count() }})</h3>

            @if($assignedPegawai->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium">Nama</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Status</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Tanggal Mulai</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Tanggal Selesai</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Catatan</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($assignedPegawai as $p)
                                <tr>
                                    <td class="px-4 py-2">{{ $p->nama_lengkap }}</td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('manage.target-kinerja.harian.update-assignment-status', [$harian->id, $p->id]) }}" method="POST">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                                <option value="pending" {{ $p->pivot->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $p->pivot->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $p->pivot->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $p->pivot->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2">{{ $p->pivot->tanggal_mulai ? \Carbon\Carbon::parse($p->pivot->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-2">{{ $p->pivot->tanggal_selesai ? \Carbon\Carbon::parse($p->pivot->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-2">{{ $p->pivot->catatan ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('manage.target-kinerja.harian.detach-pegawai', [$harian->id, $p->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus pegawai dari target harian ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Belum ada pegawai yang ditugaskan</p>
            @endif
        </div>
    </div>
@endsection
