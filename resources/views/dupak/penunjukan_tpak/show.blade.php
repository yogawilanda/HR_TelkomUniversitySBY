@extends('layouts.app')

@section('content')
<!-- </x-dupak.sidebar /> -->
<div class="">
	<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
		<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				<div class="flex items-center justify-between mb-6">
					<h1 class="text-2xl font-semibold">
						Daftar Dosen untuk Penunjukan TPAK
						@if ($user->is_admin)
						(Admin View)
						@endif
					</h1>


				</div>

				@if (session('success'))
				<div class="px-4 py-3 my-4 text-green-700 bg-green-100 border border-green-400 rounded relative"
					role="alert">
					<span class="block sm:inline">{{ session('success') }}</span>
				</div>
				@endif

				<!-- List Pengajuan -->
				<div class="overflow-hidden bg-white rounded-lg shadow">
					<!-- search bar -->
					<div class="justify-between items-center flex px-6 py-4 bg-white">
						<form action="{{ route('dupak.penunjukan-tpak.index') }}" method="GET" class="flex gap-2">
							<input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dosen..." class="px-4 py-2 border border-gray-300 bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
							<button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950">Cari</button>
							@if (request('search'))
							<a href="{{ route('dupak.penunjukan-tpak.index') }}" class="px-4 py-2 bg-white text-white rounded hover:bg-gray-600">Reset</a>
							@endif
						</form>

						<div class="flex space-x-2">
							<!-- add user id to the link so it can be used -->
							<a href="{{ route('dupak.penunjukan-tpak.create', $user->id) }}" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-950">
								Tambah Penunjukan TPAK
							</a>
							@if ($user->is_admin)
							<a href="{{ route('dupak.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
								Kembali ke Dashboard
							</a>
							@endif
						</div>
					</div>

					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-blue-900">
							<tr>
								<th scope="col"
									class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
									ID
								</th>
								<th scope="col"
									class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
									Nama Dosen
								</th>
								<th scope="col"
									class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
									TPAK untuk Pengajuan ID
								</th>

								<th scope="col"
									class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
									Tanggal Penunjukan
								</th>
								<th scope="col"
									class="px-6 py-3 text-xs font-medium tracking-wider text-left text-white uppercase">
									Aksi
								</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							@forelse ($dosens as $item)
							<tr>
								<td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
									{{ $dosens->firstItem() + $loop->index }}
								</td>
								<td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
									{{ $item->nama_lengkap ?? 'N/A' }}
								</td>
								<td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
								</td>
								<td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<!-- select action dropdown : tambahkan, ubah, hapus -->

									<div class="flex space-x-2">
										<a href="{{ route('dupak.penunjukan-tpak.create', ['user_id' => $user->id, 'dosen_id' => $item->id]) }}" class="text-blue-600 hover:text-blue-900">
											<!-- icon add -->
											<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
											</svg>
										</a>
										<a href="#" class="text-yellow-600 hover:text-yellow-900">
											<!-- icon edit -->
											<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
											</svg>
										</a>
										<form action="#" method="POST" class="inline">
											@csrf
											@method('DELETE')
											<button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
												<!-- icon delete -->
												<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
												</svg>
											</button>
										</form>
									</div>

								</td>

							</tr>
							@empty
							<tr>
								<td colspan="5" class="px-6 py-10 text-sm text-center text-gray-500">
									Belum ada data pengajuan yang tersedia.
								</td>
							</tr>
							@endforelse
						</tbody>
						<!-- Pagination -->
						<tfoot>
							<tr>
								<td colspan="5" class="px-6 py-4">
									{{ $dosens->links() }}
								</td>
							</tr>
						</tfoot>
					</table>
					<div class="px-6 py-4 bg-white border-t">

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection