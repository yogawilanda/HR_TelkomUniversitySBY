<!-- Menu Kelola Data -->

@php
    $sidebars = [
        [
            ['Manajemen Data Pegawai', 'Pegawai'],
            [
                ['Dashboard Pegawai', route('manage.pegawai.dashboard'), 'fa-solid fa-gauge'],
                ['Daftar Pegawai', route('manage.pegawai.list', ['destination' => 'Active']), 'fa-solid fa-users'],
                ['Tambah Pegawai Baru', route('manage.pegawai.new'), 'fa-solid fa-user-plus'],
                ['Tambah Dosen Baru', route('manage.pegawai.new', ['type' => 'Dosen']), 'fa-solid fa-chalkboard-user'],
                ['Tambah TPA Baru', route('manage.pegawai.new', ['type' => 'Tpa']), 'fa-solid fa-user-tie'],
                ['Import Pegawai', route('manage.pegawai.new'), 'fa-solid fa-file-import'],
            ],
        ],
        [
            ['Manajemen Fakultas', 'Fakultas'],
            [
                ['Daftar Fakultas', route('manage.fakultas.index'), 'fa-solid fa-building-columns'],
                ['Tambah Fakultas', route('manage.fakultas.create'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Manajemen Prodi', 'Prodi'],
            [
                ['Daftar Prodi', route('manage.prodi.index'), 'fa-solid fa-book-open'],
                ['Tambah Prodi', route('manage.prodi.create'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Dashboard Prodi', 'DashboardProdi'],
            [
                ['Dashboard Pendidikan', route('manage.dashboard-prodi.pendidikan'), 'fa-solid fa-graduation-cap'],
                ['Dashboard Jabatan Fungsional', route('manage.dashboard-prodi.fungsional'), 'fa-solid fa-award'],
                ['Dashboard Kepegawaian', route('manage.dashboard-prodi.kepegawaian'), 'fa-solid fa-id-card'],
            ],
        ],
        [
            ['Sertifikasi Dosen', 'Sertifikasi'],
            [
                ['Daftar Sertifikasi', route('manage.sertifikasi-dosen.list'), 'fa-solid fa-certificate'],
                ['Tambah Sertifikasi', route('manage.sertifikasi-dosen.input'), 'fa-solid fa-plus-circle'],
                ['Upload Sertifikasi', route('manage.sertifikasi-dosen.upload'), 'fa-solid fa-file-upload'],
            ],
        ],
        [
            ['Kelompok Keahlian', 'KelompokKeahlian'],
            [
                ['Daftar Kelompok Keahlian', route('manage.kelompok-keahlian.list'), 'fa-solid fa-users-gear'],
                ['Tambah Kelompok Keahlian', route('manage.kelompok-keahlian.input'), 'fa-solid fa-plus-circle'],
                ['Daftar Pegawai dengan KK', route('manage.kelompok-keahlian.pegawai-list'), 'fa-solid fa-users'],
            ],
        ],
        [
            ['Center Of Excellence', 'COE'],
            [
                ['Daftar COE', route('manage.coe.index'), 'fa-solid fa-star'],
                ['Tambah COE', route('manage.coe.create'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Studi Lanjut', 'StudiLanjut'],
            [
                ['Daftar Studi Lanjut', route('manage.studi-lanjut.list'), 'fa-solid fa-user-graduate'],
                ['Tambah Studi Lanjut', route('manage.studi-lanjut.input'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Kontrak Manajemen', 'KontrakManajemen'],
            [
                ['Daftar Kontrak Manajemen', route('manage.kontrak-manajemen.list'), 'fa-solid fa-file-contract'],
                ['Tambah Kontrak Manajemen', route('manage.kontrak-manajemen.input'), 'fa-solid fa-plus-circle'],
                ['Laporan Kontrak Manajemen', route('manage.kontrak-manajemen.laporan'), 'fa-solid fa-chart-bar'],
            ],
        ],
        [
            ['Kontrak Unit', 'KontrakUnit'],
            [
                ['Daftar Kontrak Unit', route('manage.kontrak-unit.list'), 'fa-solid fa-clipboard-list'],
                ['Tambah Kontrak Unit', route('manage.kontrak-unit.input'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Target Kinerja', 'TargetKinerja'],
            [
                ['Daftar Target Kinerja', route('manage.target-kinerja.list'), 'fa-solid fa-bullseye'],
                ['Tambah Target Kinerja', route('manage.target-kinerja.input'), 'fa-solid fa-plus-circle'],
                ['Laporan Target Kinerja', route('manage.target-kinerja.laporan'), 'fa-solid fa-chart-bar'],
            ],
        ],
        [
            ['Manajemen Level', 'Level'],
            [
                ['Daftar Level', route('manage.level.list'), 'fa-solid fa-layer-group'],
                ['Tambah Level', route('manage.level.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Manajemen Formasi', 'Formasi'],
            [
                ['Daftar Formasi', route('manage.formasi.list'), 'fa-solid fa-list-check'],
                ['Tambah Formasi', route('manage.formasi.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Pemetaan', 'Pemetaan'],
            [
                ['Daftar Pemetaan', route('manage.pengawakan.list'), 'fa-solid fa-users-gear'],
                ['Tambah Pemetaan', route('manage.pengawakan.new'), 'fa-solid fa-user-plus'],
                ['Struktur Jabatan', route('manage.pengawakan.list'), 'fa-solid fa-sitemap'],
            ],
        ],
        [
            ['Jabatan Fungsional Akademik', 'JFA'],
            [
                ['Daftar JFA', route('manage.jfa.list'), 'fa-solid fa-list-check'],
                ['Tambah JFA', route('manage.formasi.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Jabatan Fungsional Keahlian', 'JFK'],
            [
                ['Daftar JFK', route('manage.jfk.list'), 'fa-solid fa-list-check'],
                ['Tambah JFK', route('manage.jfk.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Jabatan Golongan', 'JG'],
            [
                ['Daftar Pangkat Golongan', route('manage.pangkat-golongan.list'), 'fa-solid fa-list-check'],
                ['Tambah Pangkat Golongan', route('manage.pangkat-golongan.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Jenjang Pendidikan', 'JP'],
            [
                ['Daftar Jenjang Pendidikan', route('manage.jenjang-pendidikan.list'), 'fa-solid fa-list-check'],
                ['Tambah Jenjang Pendidikan', route('manage.jenjang-pendidikan.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Riwayat Nip Pegawai', 'NIP'],
            [
                ['Daftar History NIP', route('manage.riwayat-nip.list'), 'fa-solid fa-list-check'],
                ['Tambah NIP', route('manage.formasi.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Manage Surat Keputusan', 'SK'],
            [
                ['Daftar SK', route('manage.sk.list'), 'fa-solid fa-list-check'],
                ['Tambah SK', route('manage.formasi.new'), 'fa-solid fa-plus-circle'],
            ],
        ],
        [
            ['Laporan', 'Laporan'],
            [
                [
                    'Laporan Pegawai Lengkap',
                    route('manage.pegawai.list', ['destination' => 'All']),
                    'fa-solid fa-file-lines',
                ],
            ],
        ],
    ];
@endphp

<div class="sticky top-0 w-70">
    <!-- Search Box -->
    {{-- <div class="flex items-center w-full px-2 py-1 bg-gray-200 rounded-md mx-3 my-3">
        <!-- Icon -->
        <svg class="w-4 h-4 text-[#806767] mr-2" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M10 4a6 6 0 104.472 10.472l4.528 4.528a1 1 0 001.414-1.414l-4.528-4.528A6 6 0 0010 4z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <!-- Input -->
        <input
            type="text"
            id="sidebarSearch"
            placeholder="search"
            class="w-full bg-transparent text-[#806767] text-xs placeholder-[#806767] focus:outline-none focus:ring-0 border-none py-0 px-1"
        />
    </div> --}}

    <!-- Scrollable Menu -->
    <div class="h-[calc(100vh-135px)] overflow-y-auto" id="sidebarMenu">
        @foreach ($sidebars as $sidebar)
            <div class="sidebar-group">
                <x-sidebar-group title="{{ $sidebar[0][0] }}" hide="{{ $sidebar[0][1] }}" icon="fa-users">
                    @foreach ($sidebar[1] as $i => $button)
                        <x-sidebar-button
                            :isactive="isset($active_sidebar) && $active_sidebar === $button[0] ? 'active-sidebar' : null"
                            href="{{ $button[1] }}"
                            icon="{{ $button[2] }}"
                            label="{{ $button[0] }}"
                        />
                    @endforeach
                </x-sidebar-group>
            </div>
        @endforeach
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="hidden p-4 text-center text-gray-500 text-sm">
        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 4a6 6 0 104.472 10.472l4.528 4.528a1 1 0 001.414-1.414l-4.528-4.528A6 6 0 0010 4z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <p>No menu items found</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('sidebarSearch');
    const sidebarMenu = document.getElementById('sidebarMenu');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        if (searchTerm === '') {
            showAllItems();
            return;
        }

        let hasVisibleItems = false;
        const groups = sidebarMenu.querySelectorAll('.sidebar-group');

        groups.forEach(group => {
            const groupText = group.textContent.toLowerCase();

            if (groupText.includes(searchTerm)) {
                group.style.display = '';
                hasVisibleItems = true;
            } else {
                group.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (hasVisibleItems) {
            sidebarMenu.style.display = '';
            noResults.classList.add('hidden');
        } else {
            sidebarMenu.style.display = 'none';
            noResults.classList.remove('hidden');
        }
    });

    function showAllItems() {
        sidebarMenu.style.display = '';
        noResults.classList.add('hidden');

        const groups = sidebarMenu.querySelectorAll('.sidebar-group');
        groups.forEach(group => {
            group.style.display = '';
        });
    }
});
</script>
