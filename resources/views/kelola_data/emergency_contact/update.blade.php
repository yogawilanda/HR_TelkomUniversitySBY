@php
    use App\Helpers\PhoneHelper;
    $active_sidebar = 'Kontak Darurat';
@endphp

@extends('kelola_data.base-profile')

@section('content-profile')
    <x-form
        route="{{ session('account')['is_admin'] && $user['id'] != session('account')['id']
            ? route('manage.emergency-contact.updateData', ['id_User' => $user['id'], 'id_emergency_contact' => $data['id']])
            : route('profile.emergency-contacts.updateData', [
                'id_User' => session('account')['id'],
                'id_emergency_contact' => $data['id'],
            ]) }}">

        {{-- <x-tb-td nama="nama" sorting=true>Nama Kontak Darurat</x-tb-td>
                <x-tb-td type="select" nama="gender" sorting=true>Hubungan Dengan User</x-tb-td>
                <x-tb-td nama="nip" sorting=true>Telepon</x-tb-td>
                <x-tb-td nama="nik" sorting=true>Email</x-tb-td>
                <x-tb-td nama="hp" sorting=true>Alamat</x-tb-td> --}}
        {{-- {{ Dd($data) }} --}}
        <div class="flex flex-col gap-8 w-full max-w-100 mx-auto rounded-md border p-3">
            <h2 class="text-lg font-semibold text-black text-center">Data Kontak Darurat</h2>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- <div class="flex flex-col gap-4"> --}}
                <x-itxt lbl="Nama Kontak Darurat" plc="Sisca Shafira" nm="nama_lengkap" max="200"
                    val="{{ old('nama_lengkap') ?? $data['nama_lengkap'] }}"></x-itxt>
                <x-islc lbl="Hubungan Dengan {{ $user->nama_lengkap }}" nm="status_hubungan" required>
                    <option value="-" disabled>Pilih Hubungan</option>
                    <x-option-islc value="Suami" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Istri" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Anak" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Adik Laki-laki" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Adik Perempuan" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Kakak Laki-laki" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Kakak Perempuan" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Ayah Angkat" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Ayah Kandung" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Ibu Angkat" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Ibu Kandung" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Teman Dekat" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Kakek" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Nenek" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Paman" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Tante" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Sepupu" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>
                    <x-option-islc value="Lainnya" old="{{ old('status_hubungan') }}"
                        data="{{ $data['status_hubungan'] }}"></x-option-islc>



                </x-islc>
                <x-itxt lbl="Nomor Telepon" plc="08123456789" nm="telepon" max="15"
                    val="{{ old('telepon') ?? $data['telepon'] }}"></x-itxt>
                <x-itxt type="email" lbl="Email" plc="someone@gmail.com" nm="email" max="100"
                    val="{{ old('email') ?? $data['email'] }}"></x-itxt>
                <x-itxt type="textarea" lbl="Alamat Domisili"
                    plc="Jl. Mawar No. 84 RT.003/RW.004, Pasar Senen, Mawak, Jakarta" nm="alamat" max="300"
                    val="{{ old('alamat') ?? $data['alamat'] }}"></x-itxt>

                {{-- </div> --}}
            </div>
        </div>
    </x-form>

    @if (session('message'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'fail',
                    title: 'Gagal!',
                    text: @json(session('message')),
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif
@endsection
