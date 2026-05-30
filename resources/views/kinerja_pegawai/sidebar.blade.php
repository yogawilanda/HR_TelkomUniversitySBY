{{-- Sidebar Kinerja Pegawai --}}

@php
    $user = auth()->user();
    $isAdmin = $user && $user->is_admin;
    $role = $user ? $user->role : 'pegawai';

    if ($isAdmin) {
        $dashboardLinks = [
            ['Beranda Kinerja', route('manage.target-kinerja.index'), 'fa-solid fa-house'],
            ['Monitoring Aktivitas', route('manage.monitoring.index'), 'fa-solid fa-user-shield'],
            ['Presensi Keseluruhan', route('manage.presensi.index'), 'fa-solid fa-clock-rotate-left'],
            ['Laporan Keterlambatan', route('manage.presensi.tardiness'), 'fa-solid fa-user-clock'],
            ['Pengaturan Presensi', route('manage.presensi.settings'), 'fa-solid fa-gear'],
        ];

        $sidebars = [
            [
                ['Dashboard Kinerja', 'Dashboard', 'fa-solid fa-gauge'],
                $dashboardLinks,
            ],
            [
                ['KM & Sasaran Mutu', 'TargetKinerja', 'fa-solid fa-bullseye'],
                [
                    ['Daftar KM & SM', route('manage.target-kinerja.list'), 'fa-solid fa-list-check'],
                    ['Tambah KM & SM', route('manage.target-kinerja.input'), 'fa-solid fa-plus-circle'],
                    ['Laporan KM & SM', route('manage.target-kinerja.laporan'), 'fa-solid fa-chart-bar'],
                ],
            ],
            [
                ['Target Kinerja', 'TargetKinerjaHarian', 'fa-solid fa-calendar-day'],
                [
                    ['Daftar Target', route('manage.target-kinerja.harian.list'), 'fa-solid fa-list-check'],
                    ['Tambah Target', route('manage.target-kinerja.harian.input'), 'fa-solid fa-plus-circle'],
                    ['Kinerja Harian (Approval)', route('manage.target-kinerja.harian.reports'), 'fa-solid fa-check-double'],
                    ['Reporting Kinerja', route('manage.laporan.reporting'), 'fa-solid fa-file-invoice'],
                ],
            ],
        ];
    } else {
        if ($role === 'pimpinan') {
            $sidebars = [
                [
                    ['Menu Pimpinan', 'MenuPimpinan', 'fa-solid fa-briefcase'],
                    [
                        ['Executive Dashboard', route('manage.target-kinerja.index'), 'fa-solid fa-house'],
                        ['Monitoring Global', route('manage.monitoring.index'), 'fa-solid fa-globe'],
                        ['Laporan Kedisiplinan', route('manage.presensi.tardiness'), 'fa-solid fa-file-contract'],
                        ['Rekap Performa (KM & SM)', route('manage.target-kinerja.laporan'), 'fa-solid fa-chart-pie'],
                        ['Reporting Kinerja', route('manage.laporan.reporting'), 'fa-solid fa-file-invoice'],
                    ],
                ]
            ];
        } elseif ($role === 'atasan') {
            $sidebars = [
                [
                    ['Menu Atasan', 'MenuAtasan', 'fa-solid fa-users-gear'],
                    [
                        ['Dashboard Unit', route('manage.target-kinerja.index'), 'fa-solid fa-chart-pie'],
                        ['Kinerja Harian (Approval)', route('manage.target-kinerja.harian.reports'), 'fa-solid fa-check-double'],
                        ['Monitoring Unit', route('manage.monitoring.index'), 'fa-solid fa-desktop'],
                        ['Manajemen Target Bawahan', route('manage.target-kinerja.list'), 'fa-solid fa-chart-line'],
                        ['Reporting Kinerja', route('manage.laporan.reporting'), 'fa-solid fa-file-invoice'],
                    ],
                ]
            ];
        } else {
            // Pegawai (Hanya Laporan & Riwayat)
            $sidebars = [
                [
                    ['Menu Pribadi', 'MenuPribadi', 'fa-solid fa-user'],
                    [
                        ['Dashboard Utama', route('manage.target-kinerja.index'), 'fa-solid fa-house'],
                        ['Tambah Target', route('manage.target-kinerja.harian.input'), 'fa-solid fa-plus-circle'],
                        ['Laporan Kinerja Harian', route('manage.target-kinerja.harian.list'), 'fa-solid fa-file-signature'],
                        ['Riwayat Efektivitas', route('manage.laporan.efektivitas'), 'fa-solid fa-chart-line'],
                        ['Reporting Kinerja', route('manage.laporan.reporting'), 'fa-solid fa-file-invoice'],
                        ['Presensi', route('manage.presensi.index'), 'fa-solid fa-clock'],
                    ],
                ]
            ];
        }
    }
@endphp

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@foreach ($sidebars as $i => $sidebar)
    @php
        $isGroupActive = false;
        foreach ($sidebar[1] as $button) {
            if (request()->url() == $button[1]) {
                $isGroupActive = true;
                break;
            }
        }
    @endphp

    <x-sidebar-group :expanded="$isGroupActive ? 'true' : 'false'" :title="$sidebar[0][0]" :hide="$sidebar[0][1]"
        :icon="$sidebar[0][2]">
        @foreach ($sidebar[1] as $button)
            @php
                $isActive = request()->url() == $button[1] ? 'active' : '';
            @endphp
            <x-sidebar-button :isactive="$isActive" :href="$button[1]" :icon="$button[2]"
                :label="$button[0]" />
        @endforeach
    </x-sidebar-group>
@endforeach
