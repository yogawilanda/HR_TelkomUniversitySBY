@props(['target_id' => null])
<button id="printTableBtn" data-id-table="{{$target_id}}" 
    class="group flex h-full rounded-md transition-all duration-200 ease-in-out hover:scale-105 active:scale-95">
    <div
        class="flex justify-center items-center gap-2 bg-[#eef6ff] px-3 py-2.5 
        rounded-md border border-[#b6d4fe] text-[#1e429f]
        hover:bg-[#dbeafe] hover:border-[#93c5fd] hover:text-[#1d4ed8] 
        hover:shadow-md active:scale-[0.97] transition-all duration-200 ease-in-out">
        <i class="bi bi-printer text-sm group-hover:text-[#1d4ed8]"></i>
        <span class="font-medium text-[11px] leading-[15px]">Print Table</span>
    </div>
</button>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const printBtn = document.getElementById("printTableBtn");

        printBtn.addEventListener("click", function() {
            // Ambil ID tabel dari atribut data-id-table
            const targetTableId = printBtn.getAttribute("data-id-table");
            const table = document.getElementById(targetTableId);

            if (!table) {
                alert("Tabel dengan ID '" + targetTableId + "' tidak ditemukan!");
                return;
            }

            // Clone tabel agar tidak mengubah aslinya
            const clonedTable = table.cloneNode(true);

            // Hapus kolom terakhir (biasanya kolom 'Action')
            clonedTable.querySelectorAll("tr").forEach(tr => {
                const lastCell = tr.lastElementChild;
                if (lastCell) lastCell.remove();
            });

            // Hapus input dan select di header (filter)
            clonedTable.querySelectorAll("thead input, thead select").forEach(el => el.remove());

            // Ambil tanggal & waktu saat ini
            const now = new Date();
            const options = {
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                timeZone: "Asia/Jakarta",
            };
            const formattedDate = now.toLocaleString("id-ID", options) + " WIB";

            // Nama halaman
            const pageTitle = document.title || "Daftar Data";

            // Buat jendela print baru
            const newWin = window.open("", "_blank");
            newWin.document.write(`
            <html>
                <head>
                    <title>${pageTitle}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                            color: #000;
                        }
                        h1 {
                            text-align: center;
                            font-size: 18px;
                            margin-bottom: 5px;
                            color: #1c2762;
                        }
                        p.print-date {
                            text-align: center;
                            font-size: 12px;
                            margin-bottom: 20px;
                            color: #555;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        th, td {
                            border: 1px solid #ccc;
                            padding: 8px;
                            text-align: center;
                            font-size: 12px;
                        }
                        th {
                            background-color: #f0f4ff;
                            color: #1c2762;
                        }
                        @media print {
                            body { -webkit-print-color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>
                    <h1>${pageTitle}</h1>
                    <p class="print-date">Dicetak pada: ${formattedDate}</p>
                    ${clonedTable.outerHTML}
                </body>
            </html>
        `);
            newWin.document.close();
            newWin.focus();

            // Tunggu sebentar agar tabel ter-render penuh
            setTimeout(() => {
                newWin.print();
                newWin.close();
            }, 500);
        });
    });
</script>
