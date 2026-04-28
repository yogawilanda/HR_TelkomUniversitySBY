@props(['id_button' => null])

@if($id_button)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('{{ $id_button }}');
    if (!btn) return;

    // Ganti handler onclick default agar tidak double submit
    const originalOnclick = btn.getAttribute('onclick');
    btn.removeAttribute('onclick');

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = btn.closest('form');

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menyimpan data ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1C2762',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                if (originalOnclick) {
                    // eslint-disable-next-line no-eval
                    eval(originalOnclick);
                }
                if (form) {
                    form.submit();
                }
            }
        });
    });
});
</script>
@endif
