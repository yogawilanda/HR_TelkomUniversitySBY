<!-- Menu Kelola Data -->


@php
    $sidebars = [
        [
            ['Profile User','Profile'],
            [
                ['Personal Information',
                    (session('account')['is_admin'] && ($user['id'] != session('account')['id']))
                        ? route('manage.pegawai.view.personal-info', ['idUser' => $user['id']])
                        : route('profile.personal-info', ['idUser' => session('account')['id']]),
                    'fa-solid fa-id-card'
                ],

                ['Ubah Password',
                    (session('account')['is_admin'] && ($user['id'] != session('account')['id']))
                        ? route('manage.pegawai.view.change-password', ['idUser' => $user['id']])
                        : route('profile.change-password', ['idUser' => session('account')['id']]),
                    'fa-solid fa-key'
                ],
                // ['History Kepegawaian', (session('account')['is_admin'] && ($user['id'] != session('account')['id']))
                //         ? route('manage.pegawai.view.personal-info', ['idUser' => $user['id']])
                //         : route('profile.personal-info', ['idUser' => session('account')['id']]), 'fa-solid fa-briefcase'],        // riwayat kerja / kepegawaian
                ['History Pemetaan Jabatan',  
                    (session('account')['is_admin'] && ($user['id'] != session('account')['id']))
                    ? route('manage.pengawakan.history-pemetaan', ['id_user' => $user['id']])
                    : route('profile.history.pemetaan', ['id_user' => $user['id']])
                , 'fa-solid fa-network-wired'], // pemetaan posisi
                ['History Pendidikan', (session('account')['is_admin'] && ($user['id'] != session('account')['id']))
                        ? route('manage.jenjang-pendidikan.index', ['idUser' => $user['id']])
                        : route('profile.history.pendidikan.index', ['idUser' => session('account')['id']]), 'fa-solid fa-graduation-cap'], // riwayat pendidikan
                ['Kontak Darurat', (session('account')['is_admin'] && ($user['id'] != session('account')['id']))
                    ? route('manage.emergency-contact.list', ['id_User' => $user['id']])
                    : route('profile.emergency-contacts.list', ['id_User' => session('account')['id']]),
                    'fa-solid fa-phone-volume'
                ],

            ],
        ]
    ];
@endphp
{{-- {{ dd((session('account')['is_admin']&&($user['id']!=session('account')['id'])),$user['id'],session('account')['id']) }} --}}
@foreach ($sidebars as $sidebar)
    <x-sidebar-group title="{{ $sidebar[0][0] }}" hide="{{$sidebar[0][1]}}" icon="fa-users">
        @foreach ($sidebar[1] as $button)
            <x-sidebar-button :isactive="$active_sidebar === $button[0]  ? 'active-sidebar' : null" href="{{ $button[1] }}" icon="{{ $button[2] }}" label="{{ $button[0] }}" />
        @endforeach
    </x-sidebar-group>
@endforeach
