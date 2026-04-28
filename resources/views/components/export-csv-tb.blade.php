@props(['target_id' => null])

<button id="exportCsvBtn" data-id-table="{{ $target_id }}" 
    class="group flex rounded-md h-full transition-all duration-200 ease-in-out hover:scale-105 active:scale-95">
    <div
        class="flex justify-center items-center gap-2 px-3 py-2.5 
        rounded-md border border-[#b6d4fe] bg-[#eef6ff] text-[#1e429f]
        hover:bg-[#dbeafe] hover:border-[#93c5fd] hover:text-[#1d4ed8] 
        hover:shadow-md active:scale-[0.97] transition-all duration-200 ease-in-out">
        <i class="bi bi-filetype-csv text-sm group-hover:text-[#1d4ed8]"></i>
        <span class="font-medium text-[11px] leading-[15px]">Export CSV</span>
    </div>
</button>


<script>
    document.getElementById("exportCsvBtn").addEventListener("click", function() {
        // Ambil ID tabel dari atribut data-id-table
        const tableId = this.getAttribute("data-id-table");
        const table = document.getElementById(tableId);

        if (!table) {
            alert("Tabel dengan ID '" + tableId + "' tidak ditemukan.");
            return;
        }

        let csvContent = "";

        // Ambil semua baris tabel
        const rows = table.querySelectorAll("tr");

        rows.forEach((row) => {
            const cols = row.querySelectorAll("th, td");
            const rowData = [];

            // Loop kolom tapi skip kolom terakhir (biasanya 'Action')
            cols.forEach((col, colIndex) => {
                if (colIndex !== cols.length - 1) {
                    const text = col.innerText.replace(/"/g, '""');
                    rowData.push(`"${text}"`);
                }
            });

            csvContent += rowData.join(",") + "\n";
        });

        // Buat blob CSV dan trigger download
        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;

        // Tambahkan nama file dengan tanggal
        const now = new Date();
        const formattedDate = now.toLocaleDateString("id-ID").replace(/\//g, "-");
        link.download = `Daftar_Pegawai_${formattedDate}.csv`;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
