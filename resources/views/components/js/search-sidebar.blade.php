<script>
function searchInput(elemen) {
    const query = elemen.value.toLowerCase().trim();

    // ambil semua ul di dalam aside terdekat
    const allUL = elemen.closest('aside').querySelectorAll('ul');

    allUL.forEach(ul => {
        const liItems = ul.querySelectorAll('li');
        let visibleCount = 0;

        // filter li
        liItems.forEach(li => {
            const text = li.textContent.toLowerCase().trim();
            if (text.includes(query) && text !== "") {
                li.style.display = "flex"; // tampilkan li yang cocok
                visibleCount++;
            } else {
                li.style.display = "none"; // sembunyikan li yang tidak cocok
            }
        });

        // Tentukan apakah ul harus terlihat
        const isVisible = visibleCount > 0;
        ul.style.display = isVisible ? "block" : "none";

        // sembunyikan elemen p dua posisi sebelum ul
        let prev = ul.previousElementSibling;
        if (prev) prev = prev.previousElementSibling;
        if (prev && prev.tagName.toLowerCase() === "p") {
            prev.style.display = isVisible ? "block" : "none";
        }
    });
}
</script>