@extends('layouts.base-1')

@section('sidebar-menu')
    <style>
        #page-name {
            margin-bottom: 0px !important;
        }
    </style>
    @include('kelola_data.parts.sidebar-profile')
@endsection

@section('title-page')
    @yield('title-the-page')
@endsection

@section('content-base')
    <div
        class="sticky top-0 z-10 mb-4 -mx-4 border-b-2 border-gray-200/70 bg-white px-4 py-3 supports-[backdrop-filter]:bg-white/50  dark:border-gray-800 dark:bg-gray-950/60">

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

            <!-- LEFT SECTION -->
            <div class="flex items-start md:items-center w-full gap-3">

                <div
                    class="h-10 w-10 shrink-0 overflow-hidden rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white ring-2 ring-white dark:ring-gray-900">
                    <div class="flex h-full w-full items-center justify-center text-sm font-semibold">
                        TA
                    </div>
                </div>

                <div class="min-w-0">
                    <h1 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 truncate">
                        Profil {{ isset($user) ? $user['nama_lengkap'] : session('account')['nama_lengkap'] }}
                    </h1>

                    <!-- ROLE BADGES -->
                    <div class="flex flex-wrap gap-2 mt-1">

                        @if (isset($user) && $user['is_admin'] == 1)
                            <span
                                class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-medium text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-950/40 dark:text-red-200 dark:ring-red-900">
                                Super Admin
                            </span>
                        @elseif (!isset($user) && session('account')['is_admin'] == 1)
                            <span
                                class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-medium text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-950/40 dark:text-red-200 dark:ring-red-900">
                                Super Admin
                            </span>
                        @endif

                        @if (in_array('TPA', $user['role']))
                            <span
                                class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-[11px] font-medium text-green-700 ring-1 ring-inset ring-green-200 dark:bg-green-950/40 dark:text-green-200 dark:ring-green-900">
                                TPA
                            </span>
                        @else
                            <span
                                class="inline-flex items-center rounded-full bg-yellow-50 px-2.5 py-0.5 text-[11px] font-medium text-yellow-700 ring-1 ring-inset ring-yellow-200 dark:bg-yellow-950/40 dark:text-yellow-200 dark:ring-yellow-900">
                                Dosen
                            </span>
                        @endif

                        @foreach ($user['role'] as $role)
                            @if ($role != 'TPA' && $role != 'Dosen')
                                <span
                                    class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-0.5 text-[11px] font-medium text-purple-700 ring-1 ring-inset ring-purple-200 dark:bg-purple-950/40 dark:text-purple-200 dark:ring-purple-900">
                                    {{ $role }}
                                </span>
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION (optional button) -->
            {{-- 
        <div class="w-full md:w-auto flex justify-end">
            <a href="#"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-b from-blue-600 to-blue-500 px-3.5 py-2 text-xs font-medium text-white shadow-sm hover:from-blue-500 hover:to-blue-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                ✏️ <span>Ubah Data</span>
            </a>
        </div>
        --}}

        </div>
    </div>

    @yield('content-profile')

    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@section('script-base')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script>
        let toastCount = 0;

        function toast(text = null) {

            console.log('masuk toast', text)
            toastCount++;

            const toastHTML = `
            <div class="toast align-items-center text-bg-dark border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body">
                  ${text}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
              </div>
            </div>
          `;

            const container = document.getElementById('toastContainer');
            container.insertAdjacentHTML('beforeend', toastHTML);

            const toastEl = container.lastElementChild;
            const toast = new bootstrap.Toast(toastEl, {
                delay: 2000
            });

            toast.show();

            // hapus element setelah toast hilang
            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }
    </script>
    @include('kelola_data.pegawai.js.active-and-nonactive-pegawai')
    @include('kelola_data.pegawai.js.alert-success-from-controller')
@endsection
