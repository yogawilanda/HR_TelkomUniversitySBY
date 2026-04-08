@php
    use App\Helpers\PhoneHelper;
    $active_sidebar = 'History Pendidikan';
@endphp

@extends('kelola_data.base-profile')

@section('title-the-page')
    {{ $active_sidebar }}
@endsection

@section('content-profile')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat
                melihat <span class="font-bold">semua pendidikan</span> yang pernah ditempuh oleh <span
                {{-- {{ dd((session('account')['is_admin'] && ($user['id'] == session('account')['id'])),session('account')['id'],$user['id'],session('account')['is_admin'],($user['id'] != session('account')['id'])) }} --}}
                @if((session('account')['is_admin'] && ($user['id'] == session('account')['id'])))
                pegawai 
                class="font-bold">
                        anda
                    @else 
                    {{ $user['nama_lengkap'] }}</span> 
                    @endif
                </span>
                yang terdaftar di sistem disini
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">

            {{-- <x-print-tb target_id="pegawaiTable"></x-print-tb> --}}
            <x-export-csv-tb target_id="pegawaiTable"></x-export-csv-tb>

            <a href="{{ session('account')['is_admin'] && $user['id'] != session('account')['id']
                ? route('manage.emergency-contact.emergency-contacts.new', ['id_User' => $user['id']])
                : route('profile.emergency-contacts.new', ['id_User' => session('account')['id']]) }}"
                class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </a>
        </div>

    </div>
    <div class="flex flex-grow-0 flex-col gap-2 max-w-100">

        <x-tb id="pegawaiTable">
            <x-slot:table_header>
                <x-tb-td nama="nama" type="select" sorting=true>JENJANG PENDIDIKAN</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>BIDANG PENDIDIKAN</x-tb-td>
                <x-tb-td nama="nip" sorting=true>JURUSAN/PRODI</x-tb-td>
                <x-tb-td nama="nik" sorting=true>KAMPUS</x-tb-td>
                <x-tb-td nama="hp" sorting=true>GELAR</x-tb-td>
                <x-tb-td nama="option">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach ($user['pendidikan'] as $study)
                    <x-tb-cl id="$i">
                        <x-tb-cl-fill>
                            <div class="text-center text-md font-bold">
                                {{ $study['refJenjangPendidikan']['jenjang_pendidikan'] }}
                            </div>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $study['bidang_pendidikan'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $study['jurusan'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="text-center text-md font-semibold">
                                {{ $study['nama_kampus'] }}
                            </div>
                            <div class="text-left">
                                {{ $study['alamat_kampus'] }}
                            </div>
                        </x-tb-cl-fill>

                        <x-tb-cl-fill>
                            <div class="text-center text-md font-semibold">
                                {{ $study['singkatan_gelar'] }}
                            </div>
                            <div class="text-left">
                                {{ $study['gelar'] }}
                            </div>
                        </x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a
                                    class="px-3 py-1.5 border cursor-pointer border-[#0070ff] text-[#0070ff] rounded-md text-xs font-medium hover:bg-[#0070ff] hover:text-white transition">
                                    Ubah Data
                                </a>
                            </div>
                        </x-tb-cl-fill>
                    </x-tb-cl>
                @endforeach
            </x-slot:table_column>
        </x-tb>




    </div>
@endsection
