<div class="max-w-7xl mx-auto space-y-10">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Detail Surat Keputusan</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi SK dan dokumen yang terlampir</p>
        </div>

        {{-- <button
                class="px-6 py-3 rounded-xl bg-black text-white text-sm font-medium shadow hover:scale-[1.02] transition">
                Simpan Perubahan
            </button> --}}
    </div>

    <!-- MAIN GRID -->
    <div class="grid lg:grid-cols-5 gap-10">

        <!-- LEFT SIDE -->
        <div class="lg:col-span-2 space-y-8">

            <!-- INFORMASI SK -->
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100">

                <h2 class="text-sm font-semibold text-blue-600 tracking-wider mb-6">
                    INFORMASI SK
                </h2>

                <div class="grid sm:grid-cols-2 gap-y-7 gap-x-10">

                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 uppercase">Nama SK</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $sk->keterangan }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 uppercase">No SK</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $sk->no_sk }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 uppercase">TMT Mulai</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $sk->tmt_mulai }}

                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 uppercase">Tipe SK</p>
                        <span
                            class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-medium">
                            {{ $sk->tipe_sk }}

                        </span>
                    </div>

                </div>
            </div>

            @if (session('account')['is_admin'] == 1)
                <!-- USERS -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-semibold text-blue-600 tracking-wider">
                            USERS TERHUBUNG
                        </h2>
                        <span class="text-xs text-gray-400">3</span>
                    </div>

                    <div class="space-y-5">

                        <!-- USER -->
                        @forelse ($user_terkait as $user)
                            <div class="space-y-3">

                                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-semibold">
                                        A
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user['user_nama'] }}</p>
                                        <p class="text-xs text-gray-400">Staff</p>
                                    </div>
                                </div>

                                <div class="pl-12 flex flex-wrap gap-2">
                                    @forelse ($user['kategori'] as $terhubung)
                                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                                            {{ str_replace('_', ' ', $terhubung) }}
                                        </span>
                                    @empty
                                        Belum ada
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            Belum terhubung dengan user manapun
                        @endforelse
                    </div>

                </div>
            @endif

        </div>

        <!-- RIGHT SIDE PREVIEW -->
        <div class="lg:col-span-3 self-start sticky top-10">

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-sm font-semibold text-blue-600 tracking-wider">
                        PREVIEW DOKUMEN
                    </h2>
                    <span class="text-xs text-gray-400">PDF / DOCX</span>
                </div>

                <div class="w-full rounded-xl overflow-hidden border border-gray-200 bg-gray-100 flex justify-center">

                    <iframe src="{{ route('manage.sk.file', ['file_path' => $sk->file_sk, 'id_sk' => $sk->id]) }}"
                        class="w-full max-w-[900px] aspect-[210/297] bg-white">
                    </iframe>

                </div>

            </div>

        </div>

    </div>

</div>
