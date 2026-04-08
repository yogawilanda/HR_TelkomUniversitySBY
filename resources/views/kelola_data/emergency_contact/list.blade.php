@php
    use App\Helpers\PhoneHelper;
    $active_sidebar = 'Kontak Darurat';
@endphp

@extends('kelola_data.base-profile')

@section('content-profile')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat
                melihat semua kontak darurat milik pegawai <span class="font-bold">{{ $user['nama_lengkap'] }}</span> yang
                terdaftar di sistem disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">

            {{-- <x-print-tb target_id="pegawaiTable"></x-print-tb> --}}
            <x-export-csv-tb target_id="pegawaiTable"></x-export-csv-tb>

            <a href="{{ session('account')['is_admin'] && $user['id'] != session('account')['id']
                ? route('manage.emergency-contact.new', ['id_User' => $user['id']])
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
                <x-tb-td nama="nama" sorting=true>Nama Kontak Darurat</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>Hubungan Dengan User</x-tb-td>
                <x-tb-td nama="nip" sorting=true>Telepon</x-tb-td>
                <x-tb-td nama="nik" sorting=true>Email</x-tb-td>
                <x-tb-td nama="hp" sorting=true>Alamat</x-tb-td>
                <x-tb-td nama="option">Action</x-tb-td>
            </x-slot:table_header>
            <x-slot:table_column>
                @foreach ($kontaks as $contact)
                {{-- {{ dd() }} --}}
                    <x-tb-cl id="{{ $contact['id'] }}">
                        {{-- <x-tb-cl-fill>jskhjdasljkhDkj</x-tb-cl-fill> --}}
                        {{-- <x-tb-cl-fill>Ortu</x-tb-cl-fill> --}}
                        <x-tb-cl-fill>{{ $contact['nama_lengkap']==''||$contact['nama_lengkap']==null?'Belum Diisi':$contact['nama_lengkap'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $contact['status_hubungan']==''||$contact['status_hubungan']==null?'Belum Diisi':$contact['status_hubungan'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $contact['telepon']==''||$contact['telepon']==null?'Belum Diisi':$contact['telepon'] }}</x-tb-cl-fill>
                        <x-tb-cl-fill>{{ $contact['email']==''||$contact['email']==null?'Belum Diisi':$contact['email']}}</x-tb-cl-fill>
                        <x-tb-cl-fill>
                            <div class="text-wrap max-w-64">
                                <p class="text-wrap">{{ $contact['alamat']==''||$contact['alamat']==null?'Belum Diisi':$contact['alamat'] }}</p>
                            </div>


                        </x-tb-cl-fill>
                        {{-- {{ dd($contact['id']) }} --}}
                        <x-tb-cl-fill>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ session('account')['is_admin'] && $user['id'] != session('account')['id']
                                    ? route('manage.emergency-contact.updateView', ['id_User' => $user['id'],'id_emergency_contact'=>$contact['id']])
                                    : route('profile.emergency-contacts.updateView', ['id_User' => session('account')['id'],'id_emergency_contact'=>$contact['id']]) }}"
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
