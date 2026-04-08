@php
    $active_sidebar = 'Import Pegawai';
@endphp

@extends('kelola_data.base')

@section('title-page')
    {{ $active_sidebar }}
@endsection

@section('header-base')
@endsection

@section('page-name')
@endsection

@section('content-base')
    <div class="container-fluid py-4">
        <div class="mx-auto" style="max-width: 980px;">

            {{-- Header --}}
            <div class="mb-3">
                <h4 class="fw-bold mb-1">{{ $active_sidebar }}</h4>
                <div class="text-muted">Gunakan template Excel, lalu unggah untuk impor data pegawai.</div>
            </div>

            {{-- STEP 1 --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <span
                            class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary border border-primary-subtle"
                            style="width:40px;height:40px">
                            ℹ️
                        </span>

                        <div class="w-100">
                            <div class="d-flex justify-content-between flex-wrap gap-3">
                                <div>
                                    <div class="fw-semibold">Step 1 — Siapkan Template</div>
                                    <div class="text-muted mt-1">
                                        Gunakan <b>template Excel resmi</b> agar proses impor berjalan lancar.
                                    </div>
                                </div>

                                <a href="{{ asset('template/Template Import Pegawai.xlsx') }}" download
                                    class="btn btn-outline-primary text-center flex justify-center align-items-center">
                                    <p class="p-0 m-0 text-center">Unduh Template</p>
                                </a>
                            </div>

                            <div class="alert alert-light border mt-3 mb-0">
                                <div class="fw-semibold mb-2">Panduan singkat</div>
                                <ul class="list-unstyled mb-0 space-y-1 text-muted">
                                    <li class="d-flex gap-2"><span class="text-primary">•</span> Jangan ubah nama kolom</li>
                                    <li class="d-flex gap-2"><span class="text-primary">•</span> Pastikan format data sesuai
                                    </li>
                                    <li class="d-flex gap-2"><span class="text-primary">•</span> Simpan sebagai <b>.xlsx</b>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2 (FORM) --}}
            <form id="importForm" action="{{ route('manage.pegawai.import.validate-file') }}" method="POST"
                enctype="multipart/form-data" class="card shadow-sm">
                @csrf

                <div class="card-body">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div class="d-flex gap-3">
                            <span
                                class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success border border-success-subtle"
                                style="width:40px;height:40px">
                                ⬆️
                            </span>
                            <div>
                                <div class="fw-semibold">Step 2 — Upload File</div>
                                <div class="text-muted small">Hanya 1 file • Excel / CSV / JSON</div>
                            </div>
                        </div>
                    </div>

                    <div id="alertBox" class="alert alert-danger mt-3 d-none"></div>

                    {{-- Dropzone --}}
                    <div id="dropzone" tabindex="0" class="mt-3 p-4 rounded-4 border border-2 border-dashed bg-body"
                        style="border-style:dashed">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <div class="fw-semibold">Tarik & lepas file di sini</div>
                                <div class="text-muted small">atau klik "Pilih File" atau tekan F2</div>
                                <div class="text-muted small mt-1">1 file • Max 25 MB • Excel / CSV / JSON</div>
                            </div>

                            <button type="button" id="pickBtn" class="btn btn-primary">
                                Pilih File atau tekan F2
                            </button>
                        </div>

                        <input id="fileInput" name="file" type="file" class="d-none" accept=".xlsx,.xls,.csv,.json">
                    </div>

                    <div id="statusText" class="text-muted small mt-2"></div>

                    {{-- File List --}}
                    <div class="mt-3">
                        <div class="fw-semibold mb-2">File terpilih</div>
                        <ul id="fileList" class="list-group"></ul>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('script-base')
    @if (session('error'))
        <script>
            // Ambil pesan dari session, lalu kirim ke fungsi pop-up
            Pop_message('Gagal!', "{{ session('error') }}", false, 'warning');
        </script>
    @endif
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === "F2" || e.keyCode === 114) {
                e.preventDefault(); // cegah fungsi default (kalau ada)
                document.getElementById('pickBtn').click();
            }
        });
    </script>

    <script>
        function Proses_data() {
            Swal.fire({
                title: 'Mohon Tunggu',
                html: 'Sistem sedang memproses data. Proses ini mungkin memerlukan waktu beberapa detik. <br><b>Anda akan segera dialihkan.</b>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();

                    const loader = Swal.getHtmlContainer().querySelector('.swal2-loader');
                    if (loader) {
                        loader.style.borderColor = '#3085d6 transparent #3085d6 transparent';
                    }
                }
            });
        }


        /* ================= CONFIG ================= */
        const MAX_SIZE_MB = 25;
        const ALLOW_TYPES = [
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/vnd.ms-excel",
            "text/csv",
            "application/json"
        ];

        /* ================= ELEMENTS ================= */
        const form = document.getElementById("importForm");
        const dropzone = document.getElementById("dropzone");
        const fileInput = document.getElementById("fileInput");
        const pickBtn = document.getElementById("pickBtn");
        const fileListEl = document.getElementById("fileList");
        const statusText = document.getElementById("statusText");
        // const countBadge = document.getElementById("countBadge");
        const alertBox = document.getElementById("alertBox");

        /* ================= STATE ================= */
        let currentFile = null;

        /* ================= HELPERS ================= */
        function showError(msg) {
            alertBox.textContent = msg;
            alertBox.classList.remove("d-none");
        }

        function clearError() {
            alertBox.classList.add("d-none");
        }

        function formatBytes(bytes) {
            const mb = bytes / 1024 / 1024;
            return `${mb.toFixed(1)} MB`;
        }

        function validateFile(f) {
            if (!ALLOW_TYPES.includes(f.type)) {
                return "Format file tidak diizinkan.";
            }
            if (f.size > MAX_SIZE_MB * 1024 * 1024) {
                return "Ukuran file melebihi 25 MB.";
            }
            return null;
        }

        /* Set File into state AND into <input type="file"> so it will be posted */
        function setFile(file) {
            clearError();

            const err = validateFile(file);
            if (err) return showError(err);

            currentFile = file;

            // IMPORTANT: put the file back into fileInput so Laravel receives it
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            // countBadge.textContent = "1 file";
            statusText.textContent = "File siap diimpor.";
            renderFile();
        }

        function renderFile() {
            if (!currentFile) {
                fileListEl.innerHTML =
                    `<li class="list-group-item text-muted">Belum ada file.</li>`;
                // countBadge.textContent = "0 file";
                return;
            }

            fileListEl.innerHTML = `
        <li class="list-group-item">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <div class="fw-semibold">${currentFile.name}</div>
                    <div class="text-muted small">${formatBytes(currentFile.size)}</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="removeBtn">Hapus</button>
                    <button type="submit" class="btn btn-success btn-sm" id="submitBtn">Submit Or Press F4</button>
                </div>
            </div>
        </li>
    `;

            document.addEventListener('keydown', function(e) {
                if (e.key === "F4" || e.keyCode === 115) {
                    e.preventDefault(); // cegah fungsi default (kalau ada)
                    document.getElementById('submitBtn').click();
                }
            });

            document.getElementById("removeBtn").onclick = () => {
                currentFile = null;

                // clear file input so it won't post anything
                fileInput.value = "";

                renderFile();
                statusText.textContent = "File dihapus.";
            };
        }

        /* Prevent submit if no file / invalid */
        form.addEventListener("submit", (e) => {
            clearError();

            if (!currentFile || !fileInput.files.length) {
                e.preventDefault();
                showError("Pilih file terlebih dahulu.");
                return;
            }

            const err = validateFile(currentFile);
            if (err) {
                e.preventDefault();
                showError(err);
                return;
            }

            statusText.textContent = "🚀 Mengirim file...";
            Proses_data();
        });

        /* ================= EVENTS ================= */
        pickBtn.onclick = () => fileInput.click();

        fileInput.onchange = e => {
            if (e.target.files.length) setFile(e.target.files[0]);
            // jangan kosongkan di sini, karena kita butuh fileInput untuk POST
        };

        dropzone.ondragover = e => {
            e.preventDefault();
            dropzone.classList.add("border-primary", "bg-primary-subtle");
        };
        dropzone.ondragleave = () => {
            dropzone.classList.remove("border-primary", "bg-primary-subtle");
        };
        dropzone.ondrop = e => {
            e.preventDefault();
            dropzone.classList.remove("border-primary", "bg-primary-subtle");
            if (e.dataTransfer.files.length) setFile(e.dataTransfer.files[0]);
        };

        window.addEventListener("paste", e => {
            const item = [...e.clipboardData.items].find(i => i.kind === "file");
            if (item) setFile(item.getAsFile());
        });

        /* init */
        renderFile();
    </script>
@endsection
