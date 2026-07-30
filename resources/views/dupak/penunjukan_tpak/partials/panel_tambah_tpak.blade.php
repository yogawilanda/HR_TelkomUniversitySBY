<div
    x-data="tpakManager({
        dosens: @js($dosens)
    })"
    @open-tpak-modal.window="quickAssignModal($event.detail)"
    class="p-6 bg-white border rounded-lg shadow-sm border-l-4 border-l-blue-900">

    <h3 class="text-lg font-medium">
        Pengelolaan TPAK
    </h3>

    <p class="mb-4 text-sm text-gray-600">
        Penunjukan TPAK
    </p>

    <button
        type="button"
        @click="openModal('step1')"
        class="inline-flex items-center px-4 py-2 text-sm text-white transition bg-blue-900 rounded hover:bg-blue-950">
        Tambahkan TPAK Baru
    </button>

    <!-- ===========================
            STEP 1
    ============================ -->
    <div
        x-show="activeModal==='step1'"
        x-cloak
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="closeModal()">

        <div class="relative w-full max-w-md p-6 bg-white rounded-xl shadow-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">
                        Pilih Metode Penambahan
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Pilih metode penambahan TPAK.
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeModal()"
                    class="text-gray-400 hover:text-black">
                    ✕
                </button>
            </div>

            <div class="mt-6 space-y-3">
                <a
                    href="{{ route('dupak.penunjukan_tpak.create_new_tpak') }}"
                    class="flex items-center justify-between p-4 transition border rounded-lg hover:border-blue-700 hover:bg-blue-50">
                    <div>
                        <div class="font-semibold">
                            Buat Dosen / Pengguna Baru
                        </div>
                        <div class="text-xs text-gray-500">
                            Registrasi akun baru.
                        </div>
                    </div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <button
                    type="button"
                    @click="switchModal('step2')"
                    class="flex items-center justify-between w-full p-4 text-left transition border rounded-lg hover:border-blue-700 hover:bg-blue-50">
                    <div>
                        <div class="font-semibold">
                            Pilih dari Dosen yang Ada
                        </div>
                        <div class="text-xs text-gray-500">
                            Gunakan akun dosen yang telah terdaftar.
                        </div>
                    </div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ===========================
            STEP 2
    ============================ -->
    <div
        x-show="activeModal==='step2'"
        x-cloak
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="closeModal()">

        <!-- Modal Box dengan max-h aman untuk layar HP / Laptop kerdil -->
        <div class="relative flex flex-col w-full max-w-md bg-white rounded-xl shadow-2xl max-h-[90vh]">
            <!-- HEADER -->
            <div class="flex items-center justify-between px-5 py-4 border-b shrink-0">
                <h3 class="font-semibold text-gray-900">
                    Pilih Dosen Terdaftar
                </h3>

                <button
                    type="button"
                    @click="closeModal()"
                    class="text-gray-400 hover:text-black">
                    ✕
                </button>
            </div>

            <form
                action="{{ route('dupak.penunjukan_tpak.store') }}"
                method="POST"
                class="flex flex-col flex-1 overflow-hidden"
                @submit="validateAndSubmit($event)">

                @csrf

                <input type="hidden" name="pengajuan_id" :value="pengajuanId">
                <input type="hidden" name="idDosenTpak" :value="selectedDosen">

                <div class="flex-1 px-5 py-4 overflow-y-auto space-y-4 pb-28">
                    
                    <!-- INPUT DROPDOWN FIELD -->
                    <div class="relative" @click.outside="isDropdownOpen = false">
                        <label class="block mb-1 text-xs font-bold uppercase text-gray-700">
                            Dosen <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <input
                                type="text"
                                x-model="searchQuery"
                                @focus="onFocusInput()"
                                @input="onInputSearch()"
                                @keydown.arrow-down.prevent="moveDown()"
                                @keydown.arrow-up.prevent="moveUp()"
                                @keydown.enter.prevent="chooseHighlighted()"
                                @keydown.escape="isDropdownOpen=false"
                                autocomplete="off"
                                placeholder="Ketik nama dosen..."
                                class="w-full px-3 py-2 pr-8 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900">

                            <button
                                type="button"
                                x-show="searchQuery"
                                @click="clearSearch()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                                ✕
                            </button>
                        </div>

                        <!-- DROPDOWN LIST (Diatur max-h-44 agar ringkas) -->
                        <div
                            x-show="isDropdownOpen"
                            x-transition.opacity.duration.150ms
                            x-cloak
                            x-ref="dropdownList"
                            class="absolute left-0 right-0 z-50 mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-xl max-h-44 text-sm">

                            <template x-for="(dosen, index) in filteredDosens" :key="dosen.id">
                                <button
                                    type="button"
                                    @mousedown.prevent="selectDosen(dosen)"
                                    @mouseenter="highlightedIndex = index"
                                    :class="{
                                        'bg-blue-50 text-blue-900 font-medium': highlightedIndex === index,
                                        'text-gray-700': highlightedIndex !== index
                                    }"
                                    class="flex items-center justify-between w-full px-3 py-2 text-left transition">

                                    <span x-text="dosen.nama_lengkap"></span>
                                    <span x-show="selectedDosen == dosen.id" class="text-blue-900 font-bold">✓</span>
                                </button>
                            </template>

                            <div
                                x-show="filteredDosens.length === 0"
                                class="px-3 py-3 text-sm italic text-center text-gray-400">
                                Dosen tidak ditemukan.
                            </div>
                        </div>
                    </div>

			  <!-- Tim Penugasan TPAK -->
                    <div>
                        <label class="block mb-1 text-xs font-bold uppercase text-gray-700">
                            SK Penugasan Tim PAK
                        </label>

                        <textarea
                            name="bukti_penunjukan"
                            rows="3"
                            class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-blue-900"
                            placeholder="Nomor SK atau catatan..."></textarea>
                    </div>

                    <!-- CATATAN -->
                    <div>
                        <label class="block mb-1 text-xs font-bold uppercase text-gray-700">
                            Catatan / SK
                        </label>

                        <textarea
                            name="catatan"
                            rows="3"
                            class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-blue-900"
                            placeholder="Nomor SK atau catatan..."></textarea>
                    </div>
                </div>

                <!-- FOOTER (Selalu menempel di bawah / shrink-0) -->
                <div class="flex items-center justify-between px-5 py-4 border-t bg-gray-50 rounded-b-xl shrink-0">
                    <button
                        type="button"
                        @click="switchModal('step1')"
                        class="text-sm font-medium text-blue-700 hover:underline">
                        ← Kembali
                    </button>

                    <div class="space-x-2">
                        <button
                            type="button"
                            @click="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                            Batal
                        </button>

                        <button
                            type="submit"
                            :disabled="!selectedDosen"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-900 rounded-lg hover:bg-blue-950 disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tpakManager', (config) => ({

        /* STATE */
        activeModal: null,
        searchQuery: '',
        selectedDosen: '',
        pengajuanId: null,
        dosenPengajuId: null,
        isDropdownOpen: false,
        highlightedIndex: 0,
        dosensList: config.dosens ?? [],

        /* MODAL */
        openModal(step = 'step1') {
            this.activeModal = step;
        },

        switchModal(step) {
            this.activeModal = step;
        },

        closeModal() {
            this.activeModal = null;
            this.resetForm();
        },

        quickAssignModal(detail) {
            this.pengajuanId = detail.pengajuanId;
            this.dosenPengajuId = detail.idDosen;
            this.activeModal = 'step2';
        },

        /* FORM & SEARCH */
        resetForm() {
            this.searchQuery = '';
            this.selectedDosen = '';
            this.pengajuanId = null;
            this.dosenPengajuId = null;
            this.isDropdownOpen = false;
            this.highlightedIndex = 0;
        },

        clearSearch() {
            this.searchQuery = '';
            this.selectedDosen = '';
            this.highlightedIndex = 0;
            this.isDropdownOpen = true;
        },

        selectDosen(dosen) {
            if (!dosen) return;
            this.selectedDosen = dosen.id;
            this.searchQuery = dosen.nama_lengkap;
            this.isDropdownOpen = false;
        },

        onFocusInput() {
            this.isDropdownOpen = true;
        },

        onInputSearch() {
            this.isDropdownOpen = true;
            this.selectedDosen = '';
            this.highlightedIndex = 0;
        },

        /* KEYBOARD NAVIGATION */
        moveDown() {
            if (!this.isDropdownOpen) {
                this.isDropdownOpen = true;
                return;
            }
            if (this.highlightedIndex < this.filteredDosens.length - 1) {
                this.highlightedIndex++;
                this.scrollToHighlighted();
            }
        },

        moveUp() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
                this.scrollToHighlighted();
            }
        },

        chooseHighlighted() {
            if (!this.filteredDosens.length) return;
            this.selectDosen(this.filteredDosens[this.highlightedIndex]);
        },

        scrollToHighlighted() {
            this.$nextTick(() => {
                const container = this.$refs.dropdownList;
                if (!container) return;
                const activeItem = container.children[this.highlightedIndex];
                if (activeItem) {
                    activeItem.scrollIntoView({ block: 'nearest' });
                }
            });
        },

        /* COMPUTED */
        get filteredDosens() {
            let data = [...this.dosensList];

            if (this.dosenPengajuId) {
                data = data.filter(item => item.id != this.dosenPengajuId);
            }

            if (!this.searchQuery.trim()) {
                return data;
            }

            const keyword = this.searchQuery.toLowerCase();
            return data.filter(item =>
                item.nama_lengkap.toLowerCase().includes(keyword)
            );
        },

        /* VALIDATION */
        validateAndSubmit(event) {
            if (!this.selectedDosen) {
                event.preventDefault();
                alert('Silakan pilih dosen terlebih dahulu.');
            }
        }
    }));
});

/* QUICK ASSIGN TRIGGER */
function quickAssign(pengajuanId, idDosen) {
    window.dispatchEvent(
        new CustomEvent('open-tpak-modal', {
            detail: { pengajuanId, idDosen }
        })
    );
}
</script>