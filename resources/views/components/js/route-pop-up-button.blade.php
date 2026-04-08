<script>
    document.addEventListener("click", function(event) {

        const element = event.target.closest(".route_pop_up");
        if (!element) return;

        // ctrl click = tab baru
        if (event.ctrlKey || event.metaKey) {
            return; // biarkan browser handle
        }

        // shift click = window baru
        if (event.shiftKey) {
            return; // biarkan browser handle
        }

        // middle click
        if (event.button === 1) {
            return;
        }

        // klik biasa
        event.preventDefault();

        const link = element.getAttribute('href');
        if (!link || link === '#') return;

        Pop_message('Memproses!', 'Sedang Mengalihkan', true);

        window.location.href = link;


    });
</script>
