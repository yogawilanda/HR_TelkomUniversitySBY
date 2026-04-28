<script>
    function Pop_message(title = null, message = null, is_load = false, type = 'save') {
        if (is_load) {

            // Swal.fire({
            //     title: title == null ? 'Loading...' : title,
            //     text: message != null ? message : 'Sedang memproses data',
            //     allowOutsideClick: false,
            //     allowEscapeKey: false,
            //     showConfirmButton: false,
            //     showCancelButton: false,
            //     didOpen: () => {
            //         Swal.showLoading()
            //     }
            // });
            Swal.fire({
                title: title == null ? 'Memproses...' : title,
                html: 'Mohon tunggu ' + message + '<span class="loading-dots"></span>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: type == 'save' ? 'success' : 'warning',
                title: title,
                html: message,
                confirmButtonText: 'OK'
            });
        }

    }
</script>
