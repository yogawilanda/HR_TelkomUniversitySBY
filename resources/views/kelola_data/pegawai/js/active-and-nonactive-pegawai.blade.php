{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    function konfirmasiNonaktif(id) {
        Swal.fire({
            title: 'Yakin ingin menonaktifkan user ini?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Nonaktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-nonaktif-' + id).submit();
            }
        });
    }

    function konfirmasiAktif(id) {
        Swal.fire({
            title: 'Yakin ingin mengaktifkan user ini?',
            text: "User akan kembali bisa login dan mengakses sistem.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Aktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-aktif-' + id).submit();
            }
        });
    }
</script>
