@props([
    'id_button'=>null,
])

<script>
    function form_loading(elemen) {
        console.log(elemen.checkValidity())
        if (!elemen.closest('form').checkValidity()) {
            console.log('masuk', 'cek')
            Pop_message('Validasi Data', 'Silakan periksa kembali dan lengkapi semua field yang bertanda *.', false,
                'warning');
            return;
        } {
            console.log('masuk', 'proses')
            Pop_message('Mohon Tunggu....', 'Sedang melakukan validasi data', true);
        }

    }

    document.addEventListener('keydown', function(e) {
        if (e.key === "F2" || e.keyCode === 114) {
            console.log('masuk f2')
            e.preventDefault(); // cegah fungsi default (kalau ada)
            document.getElementById('{{ $id_button }}').click();
            
        }
    });
</script>
