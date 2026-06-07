@php
    $active_sidebar = 'Kontrak Manajemen (KM) & Sasaran Mutu (SM)';
@endphp

@extends('kinerja_pegawai.base')

@section('header-base')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

@section('page-name', 'Tambah KM & Sasaran Mutu')

@section('content-base')
<div class="mb-4">
    <p class="text-sm text-gray-500 italic">Buat template KPI global baru untuk didistribusikan ke unit kerja.</p>
</div>
<div x-data="satuanCrud()" x-init="fetchSatuans()" class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 bg-white rounded-lg shadow-sm border border-gray-100">
    <form action="{{ route('manage.target-kinerja.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Section 1: Informasi Utama --}}
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700 mb-6 border-l-4 border-blue-600 pl-4">Informasi Utama</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Responsibility (Nama Indikator KPI)</label>
                    <input type="text" name="nama_kpi" value="{{ old('nama_kpi') }}" required
                        class="w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5"
                        placeholder="Contoh: Meningkatkan Publikasi Internasional Scopus Q1">
                    @error('nama_kpi') <p class="text-xs text-red-600 mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Unit Penanggung Jawab</label>
                    <select name="responsibility_id" required class="w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                        <option value="">-- Pilih Unit --</option>
                        @foreach(\App\Models\Unit::orderBy('nama_unit')->get() as $unit)
                            <option value="{{ $unit->id }}" {{ old('responsibility_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-600">Satuan Ukur</label>
                        <button type="button" @click="openModal()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fa-solid fa-gear"></i> Kelola Satuan
                        </button>
                    </div>
                    <select name="satuan" required class="w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                        <option value="">-- Pilih Satuan --</option>
                        <template x-for="s in satuans" :key="s.id">
                            <option :value="s.nama" x-text="s.nama" :selected="s.nama == '{{ old('satuan') }}'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Jenis Indikator</label>
                    <select name="jenis" required class="w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Kontrak Manajemen" {{ old('jenis') == 'Kontrak Manajemen' ? 'selected' : '' }}>Kontrak Manajemen (KM)</option>
                        <option value="Sasaran Mutu" {{ old('jenis') == 'Sasaran Mutu' ? 'selected' : '' }}>Sasaran Mutu (SM)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Tahun Anggaran</label>
                    <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" required
                        class="w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 py-2.5"
                        min="2000" max="2100">
                </div>
            </div>
        </div>

        {{-- Section 2: Target Triwulan --}}
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700 mb-2 border-l-4 border-emerald-600 pl-4">Target & Bobot Triwulan</h3>
            <p class="text-xs text-gray-400 italic mb-6">Tentukan target pencapaian dan bobot nilai untuk setiap triwulan (isi 0 jika tidak ada).</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach(['tw1' => 'Triwulan I', 'tw2' => 'Triwulan II', 'tw3' => 'Triwulan III', 'tw4' => 'Triwulan IV'] as $key => $label)
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm space-y-4">
                        <p class="text-xs font-black text-blue-600 uppercase tracking-widest">{{ $label }}</p>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Target Angka</label>
                            <input type="number" step="0.01" name="{{ $key }}_target" value="{{ old($key.'_target', 0) }}"
                                class="w-full text-sm text-gray-900 border-gray-300 rounded-md focus:ring-blue-500">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Bobot (%)</label>
                            <input type="number" step="0.01" name="{{ $key }}_bobot" value="{{ old($key.'_bobot', 0) }}"
                                class="w-full text-sm text-gray-900 border-gray-300 rounded-md focus:ring-blue-500">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section 3: Keterangan --}}
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Keterangan / Deskripsi KPI</label>
                <textarea name="keterangan" rows="4"
                    class="w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Tambahkan informasi detail atau kriteria keberhasilan indikator ini...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked 
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">
                    Aktifkan indikator KPI ini segera setelah disimpan
                </label>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex justify-end gap-3 pt-8 border-t border-gray-100">
            <a href="{{ route('manage.target-kinerja.list') }}" 
                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium py-2.5 px-6 rounded-md transition duration-150">
                Batalkan
            </a>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 px-8 rounded-md transition duration-150 shadow-sm">
                Simpan KM & Sasaran Mutu
            </button>
        </div>
    </form>

    {{-- Modal Kelola Satuan --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Kelola Satuan Ukur
                            </h3>
                            <div class="mt-4">
                                <template x-if="message">
                                    <div :class="messageType === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'" 
                                         class="p-3 rounded-md border text-xs mb-4 flex justify-between items-center">
                                        <span x-text="message"></span>
                                        <button @click="message = ''" class="text-lg">&times;</button>
                                    </div>
                                </template>

                                <div class="flex gap-2 mb-4">
                                    <input type="text" x-model="newSatuan" placeholder="Nama satuan baru..." class="flex-1 text-sm border-gray-300 rounded-md focus:ring-blue-500">
                                    <button type="button" @click="saveSatuan()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">Simpan</button>
                                </div>
                                <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="s in satuans" :key="s.id">
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-gray-900">
                                                        <span x-text="s.nama"></span>
                                                        <template x-if="s.is_hardcoded">
                                                            <span class="ml-2 px-1.5 py-0.5 text-[10px] bg-gray-100 text-gray-500 rounded border border-gray-200">Bawaan</span>
                                                        </template>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right">
                                                        <template x-if="!s.is_hardcoded">
                                                            <button type="button" @click="deleteSatuan(s.id)" class="text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i></button>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr x-show="satuans.length === 0">
                                                <td colspan="2" class="px-4 py-3 text-sm text-center text-gray-500">Belum ada satuan tersimpan.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function satuanCrud() {
        return {
            showModal: false,
            satuans: [],
            newSatuan: '',
            message: '',
            messageType: 'success',
            editingId: null,
            openModal() {
                this.showModal = true;
                this.newSatuan = '';
                this.message = '';
            },
            closeModal() {
                this.showModal = false;
            },
            fetchSatuans() {
                fetch('{{ route("manage.target-kinerja.ref-satuan.index") }}')
                    .then(res => res.json())
                    .then(data => {
                        this.satuans = data;
                    });
            },
            saveSatuan() {
                if (!this.newSatuan) {
                    this.message = 'Nama satuan tidak boleh kosong.';
                    this.messageType = 'error';
                    return;
                }
                
                let url = '{{ route("manage.target-kinerja.ref-satuan.store") }}';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ nama: this.newSatuan })
                })
                .then(async res => {
                    const data = await res.json();
                    if (res.ok) {
                        this.newSatuan = '';
                        this.message = 'Satuan berhasil ditambahkan.';
                        this.messageType = 'success';
                        this.fetchSatuans();
                    } else {
                        this.message = data.message || 'Gagal menyimpan satuan.';
                        this.messageType = 'error';
                    }
                })
                .catch(err => {
                    this.message = 'Terjadi kesalahan sistem.';
                    this.messageType = 'error';
                });
            },
            deleteSatuan(id) {
                if (!confirm('Hapus satuan ini?')) return;
                fetch('{{ route("manage.target-kinerja.ref-satuan.index") }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (res.ok) {
                        this.message = 'Satuan berhasil dihapus.';
                        this.messageType = 'success';
                        this.fetchSatuans();
                    } else {
                        this.message = data.message || 'Gagal menghapus satuan.';
                        this.messageType = 'error';
                    }
                });
            }
        }
    }
</script>
@endsection
