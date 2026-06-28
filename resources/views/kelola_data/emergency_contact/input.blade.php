@php
    use App\Helpers\PhoneHelper;
    $active_sidebar = 'Kontak Darurat';
@endphp

@extends('kelola_data.base-profile')

@section('content-profile')
    {{-- <div class="flex w-full flex-col gap-[2.93px] grow">
            <div class="flex items-center gap-[5.87px] self-stretch">
                <span class="font-medium font-bold text-2xl leading-[20.56px] text-[#101828]">Tambah Kontak Darurat Baru</span>
            </div>
            <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat
                melihat semua <span class="textlg font-bold" >kontak darurat</span> milik pegawai <span class="font-bold">{{ $user['nama_lengkap'] }}</span> yang
                terdaftar di sistem disini</span>
        </div> --}}
    <x-form route="{{ route('profile.emergency-contacts.new-data', ['id_User' => $user->id]) }}">

        {{-- <x-tb-td nama="nama" sorting=true>Nama Kontak Darurat</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>Hubungan Dengan User</x-tb-td>
                <x-tb-td nama="nip" sorting=true>Telepon</x-tb-td>
                <x-tb-td nama="nik" sorting=true>Email</x-tb-td>
                <x-tb-td nama="hp" sorting=true>Alamat</x-tb-td> --}}
        <div class="flex flex-col gap-8 w-full max-w-100 mx-auto rounded-md border p-3">
            <div class="text-center">
                <h2 class="text-lg font-semibold text-black text-center">Tambah Data Kontak Darurat</h2>
                <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat menambah data <span class="textlg font-bold" >kontak darurat</span> milik pegawai <span class="font-bold">{{ $user['nama_lengkap'] }}</span> disini</span>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- <div class="flex flex-col gap-4"> --}}
                <x-itxt lbl="Nama Kontak Darurat" plc="Sisca Shafira" nm="nama_lengkap" max="200"></x-itxt>
                <x-islc lbl="Hubungan Dengan {{ $user->nama_lengkap }}" nm="status_hubungan" required>
                    <option value="-" disabled>Pilih Hubungan</option>
                    <option value="Adik Laki-laki">Adik Laki-laki</option>
                    <option value="Adik Perempuan">Adik Perempuan</option>
                    <option value="Anak">Anak</option>
                    <option value="Ayah Angkat">Ayah Angkat</option>
                    <option value="Ayah Kandung">Ayah Kandung</option>
                    <option value="Ibu Angkat">Ibu Angkat</option>
                    <option value="Ibu Kandung">Ibu Kandung</option>
                    <option value="Istri">Istri</option>
                    <option value="Kakak Laki-laki">Kakak Laki-laki</option>
                    <option value="Kakak Perempuan">Kakak Perempuan</option>
                    <option value="Kakek">Kakek</option>
                    <option value="Nenek">Nenek</option>
                    <option value="Paman">Paman</option>
                    <option value="Sepupu">Sepupu</option>
                    <option value="Suami">Suami</option>
                    <option value="Tante">Tante</option>
                    <option value="Teman Dekat">Teman Dekat</option>



                </x-islc>
                <x-itxt lbl="Nomor Telepon" plc="08123456789" nm="telepon" max="15"></x-itxt>
                <x-itxt type="email" lbl="Email" plc="someone@gmail.com" nm="email" max="100"></x-itxt>
                <x-itxt type="textarea" lbl="Alamat Domisili"
                    plc="Jl. Mawar No. 84 RT.003/RW.004, Pasar Senen, Mawak, Jakarta" nm="alamat"
                    max="300"></x-itxt>

                {{-- </div> --}}
            </div>
        </div>
    </x-form>
@endsection
