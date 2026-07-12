<!-- Menu Kelola Data -->


@php
    $sidebars = [
                    [
                        ['Profile User', 'Profile'],
                        [
                            [
                                'Personal Information',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.pegawai.view.personal-info', ['idUser' => $user['id']])
                                    : route('profile.personal-info', ['idUser' => session('account')['id']]),
                                'fa-solid fa-user-tie', // Lebih spesifik untuk profil profesional
                            ],
                            [
                                'Ubah Password',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.pegawai.view.change-password', ['idUser' => $user['id']])
                                    : route('profile.change-password', ['idUser' => session('account')['id']]),
                                'fa-solid fa-shield-halved', // Simbol keamanan/password
                            ],
                            [
                                'Kontak Darurat',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.emergency-contact.list', ['id_User' => $user['id']])
                                    : route('profile.emergency-contacts.list', ['id_User' => session('account')['id']]),
                                'fa-solid fa-house-medical-circle-exclamation', // Simbol darurat/keluarga
                            ],
                            [
                                'History Pemetaan Jabatan',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.pengawakan.history-pemetaan', ['id_user' => $user['id']])
                                    : route('profile.history.pemetaan', ['id_user' => $user['id']]),
                                'fa-solid fa-sitemap', // Lebih cocok untuk struktur organisasi/pemetaan
                            ],

                            [
                                'History Pendidikan',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.jenjang-pendidikan.index', ['idUser' => $user['id']])
                                    : route('profile.history.pendidikan.index', ['idUser' => session('account')['id']]),
                                'fa-solid fa-graduation-cap',
                            ],
                            [
                                'History NIP',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.riwayat-nip.history', ['id_pegawai' => $user['id']])
                                    : route('profile.history.nip', ['id_pegawai' => session('account')['id']]),
                                'fa-solid fa-file-signature', // Simbol dokumen resmi/SK
                            ],
                            [
                                'History Surat Keputusan dan Amandemen',
                                (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                                    ? route('manage.sk.history', ['id_user' => $user['id']])
                                    : route('profile.history.sk', ['id_user' => session('account')['id']]),
                                'fa-solid fa-file-signature', // Simbol dokumen resmi/SK
                            ],

                        ],
                    ],
                ];
    // dd($user['role'],in_array('Dosen', $user['role']));
    if(in_array('TPA', $user['role'])){
        $sidebars[0][1] = array_merge($sidebars[0][1],
            [
                [
                    'History Jabatan Fungsional Keahlian (JFK)',
                    (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                        ? route('manage.jfk.riwayat', ['id_user' => $user['id']])
                        : route('profile.history.jfk', ['id_user' => $user['id']]),
                    'fa-solid fa-sitemap', // Lebih cocok untuk struktur organisasi/pemetaan
                ]
            ]
        );
    }else if(in_array('Dosen', $user['role'])){
        $sidebars[0][1] = array_merge($sidebars[0][1],
            [
                [
                    'History Pangkat Golongan',
                    (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                        ? route('manage.pangkat-golongan.history', ['id_user' => $user['id']])
                        : route('profile.history.pangkat-golongan', ['id_user' => $user['id']]),
                    'fa-solid fa-sitemap', // Lebih cocok untuk struktur organisasi/pemetaan
                ],
                [
                    'History Jabatan Fungsional Akademik (JFA)',
                    (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                        ? route('manage.jfa.history', ['id_user' => $user['id']])
                        : route('profile.history.jfa', ['id_user' => $user['id']]),
                    'fa-solid fa-sitemap', // Lebih cocok untuk struktur organisasi/pemetaan
                ],
                [
                    'History Pemetaan CoE',
                    (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                        ? route('manage.coe.dosen.history', ['id_user' => $user['id']])
                        : route('profile.history.coe', ['id_user' => session('account')['id']]),
                    'fa-solid fa-file-signature', // Simbol dokumen resmi/SK
                ],
                [
                    'History Kelompok Keahlian',
                    (session('account')['is_admin'] || isset(session('account')['role']['sumber daya manusia'])) && $user['id'] != session('account')['id']
                        ? route('manage.kelompok-keahlian.dosen-with-kk.riwayat', ['id_user' => $user['id']])
                        : route('profile.history.kelompok-keahlian', ['id_user' => session('account')['id']]),
                    'fa-solid fa-file-signature', // Simbol dokumen resmi/SK
                ]
            ]

        );
        // $sidebars[0][1] = $tambahan;

    }
    // dd($sidebars);
@endphp
{{-- {{ dd((session('account')['is_admin']&&($user['id']!=session('account')['id'])),$user['id'],session('account')['id']) }} --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

{{-- @foreach ($sidebars as $sidebar)
    <x-sidebar-group title="{{ $sidebar[0][0] }}" hide="{{ $sidebar[0][1] }}" icon="fa-users">
        @foreach ($sidebar[1] as $button)
            <x-sidebar-button :isactive="$active_sidebar === $button[0] ? 'active-sidebar' : null" href="{{ $button[1] }}" icon="{{ $button[2] }}"
                label="{{ $button[0] }}" />
        @endforeach
    </x-sidebar-group>
@endforeach --}}

@foreach ($sidebars as $sidebar)
    @php
        // Cek apakah ada salah satu menu di dalam grup ini yang sedang aktif
        $isGroupActive = false;
        foreach ($sidebar[1] as $button) {
            if (request()->url() == $button[1]) {
                $isGroupActive = true;
                break;
            }
        }
    @endphp

    <x-sidebar-group :expanded="$isGroupActive ? 'true' : 'false'" title="{{ $sidebar[0][0] }}" hide="{{ $sidebar[0][1] }}" icon="fa-users">
        @foreach ($sidebar[1] as $button)
            @php
                $isActive = request()->url() == $button[1] ? 'active' : '';
            @endphp
            <x-sidebar-button :isactive="$isActive" href="{{ $button[1] }}" icon="{{ $button[2] }}"
                label="{{ $button[0] }}" />
        @endforeach
    </x-sidebar-group>
@endforeach
