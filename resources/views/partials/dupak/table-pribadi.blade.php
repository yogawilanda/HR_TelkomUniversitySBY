<div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-900 text-white text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Nama Dosen</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Periode</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($submissions['list'] as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $item->dosen->pegawai->nama_lengkap ?? 'N/A' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $item->created_at->format('d/m/Y') }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $item->start }} - {{ $item->end }}
                    </td>

                    <td class="px-6 py-4">
                        @php
                        $badgeColor = [
                            'Draft' => 'bg-gray-100 text-gray-800',
                            'Diajukan' => 'bg-yellow-100 text-yellow-800',
                            'Menunggu' => 'bg-indigo-100 text-indigo-800',
                            'Ditolak' => 'bg-red-100 text-red-800',
                            'Diterima' => 'bg-green-100 text-green-800',
                            'Revisi' => 'bg-yellow-100 text-yellow-800',
                        ][$item->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp

                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                            {{ $item->status }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                        <a href="{{ route('dupak.pengajuan.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors">Lihat</a>

                        @if (!$user->is_admin && in_array($item->status, ['Draft','Revisi']))
                        <a href="{{ route('dupak.pengajuan.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Edit</a>

                        <form action="{{ route('dupak.pengajuan.destroy', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-900 transition-colors font-medium"
                                onclick="return confirm('Hapus pengajuan ini?')">Hapus</button>
                        </form>
                        @endif

                        @if ($user->is_admin && $item->status === 'Diajukan')
                        <a href="{{ route('dupak.validasi.show', $item->id) }}" class="text-green-600 hover:text-green-900 transition-colors">Validasi</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-sm text-gray-500 bg-white">
                        Belum ada data pengajuan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($submissions['list']->hasPages())
<div class="mt-4">
    {{ $submissions['list']->links() }}
</div>
@endif