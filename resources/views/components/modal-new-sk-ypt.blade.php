@props([
    'keperluan' => null,
    'id_user' => null,
    'route' => route('manage.sk.new', ['YptOrDikti' => 'YPT']),
    'route_khusus' => null,
])
<!-- Modal -->
<div class="modal fade" id="upload-sk-ypt" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div
            class="modal-content bg-white rounded-lg shadow-2xl border-0 p-6 relative
                                            transition-all duration-300 scale-95 opacity-0 modal-animate">

            <!-- Close -->
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700" data-bs-dismiss="modal">
                ✕
            </button>

            <h2 class="text-xl font-semibold mb-5 border-b-2 border-blue-300 pb-2 w-fit">Input SK Pengakuan YPT - Jabatan
                Fungsional Keahlian</h2>
                
            <x-form  route="{{ $route_khusus ?? $route }}" base_route="{{ $route_khusus ?? '' }}">

                <!-- Text -->
                <x-itxt lbl="SK Pengakuan YPT" type="file" plc="Pilih Dokumen SK" nm='file_sk' :req=true></x-itxt>
                <x-itxt lbl="Nomor SK " plc="Nomor SK" nm='no_sk' max="50" :req=true></x-itxt>
                <x-itxt fill="hidden" lbl="Keperluan" plc="Keperluan" nm='keperluan' max="50" :req=true
                    val="{{ $keperluan }}"></x-itxt>
                {{-- <x-itxt lbl="Users" id="id-user" plc="ID User" nm='id_user' max="50" :req=true val="{{ $id_user }}"></x-itxt> --}}
                <x-itxt fill="hidden" lbl="Users" id="name_file" plc="ID User" nm='file_name' max="50" :req=true
                    val="{{ $id_user }}"></x-itxt>
                <x-itxt lbl="Terhitung Mulai Tanggal" type="date" plc="22/01/2023" nm='tmt_mulai' max="50"
                    :req=true></x-itxt>

                <!-- Preview -->
                <div id="previewBox" class="hidden p-4 bg-gray-100 rounded-lg text-sm">
                </div>

                <!-- Submit -->
                {{-- <button id="submitBtn" type="submit"
                    class="w-full py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition disabled:opacity-50">
                    Submit
                </button> --}}
            </x-form>

        </div>
    </div>
</div>
<style>
    /* custom animasi */
    .modal.show .modal-content.modal-animate {
        opacity: 1 !important;
        transform: scale(1) !important;
    }
</style>
<script>
    // function open_modal_ypt(iduser_wht, id_khusus) {
    //     document.querySelector("#upload-sk-ypt #name_file").value = iduser_wht;
    //     let form = document.querySelector("#upload-sk-ypt form");
    //     let route = form.getAttribute('action')
    //     let route_final = route + "/" + id_khusus;
    //     form.setAttribute('action', route_final)
    // }

    function open_modal_ypt(iduser_wht, id_khusus) {
        const modal = document.querySelector("#upload-sk-ypt");
        modal.querySelector("#name_file").value=iduser_wht;
        const form = modal.querySelector("form");

        const baseAction = form.getAttribute("base-route");
        form.dataset.baseAction = baseAction; // simpan action asli

        // form.setAttribute("action", `${baseAction}/${id_khusus}`);
        form.setAttribute("action", baseAction+"/"+id_khusus);
    }


    const titleInput = document.getElementById("titleInput");
    const fileInput = document.getElementById("fileInput");
    const previewBox = document.getElementById("previewBox");
    const submitBtn = document.getElementById("submitBtn");
    const uploadModal = document.getElementById("uploadModal");

    // Autofocus ke field pertama
    uploadModal.addEventListener("shown.bs.modal", () => {
        titleInput.focus();
    });

    // Preview file otomatis
    fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];

        if (!file) {
            previewBox.classList.add("hidden");
            return;
        }

        previewBox.innerHTML = `
      <div class="font-semibold">Preview File:</div>
      <p class="mt-1">Nama: ${file.name}</p>
      <p>Ukuran: ${(file.size / 1024).toFixed(2)} KB</p>
    `;
        previewBox.classList.remove("hidden");
    });

    // Tombol loading saat submit
    document.getElementById("uploadForm").addEventListener("submit", (e) => {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.textContent = "Memproses...";

        setTimeout(() => {
            alert("Upload selesai!");
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit";
        }, 1500);
    });
</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
