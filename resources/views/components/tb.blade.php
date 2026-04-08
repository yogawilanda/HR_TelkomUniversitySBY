@props(['id' => null, 'cls' => null,'search_status'=>true])
@aware(['table_header', 'table_column', 'put_something','search_status'])

<style>
    .search-input {
        border: rgba(0, 0, 0, 0.097) 0.5px solid !important;
        border-radius: 4px !important;
        font-size: 10px !important;
    }

    th.sortable {
        cursor: pointer;
        user-select: none;
        transition: color 0.3s ease;
    }

    th.sortable:hover {
        color: #2563eb;
    }

    .sort-icon {
        font-size: 10px;
        margin-left: 5px;
        color: #9ca3af;
    }

    .fixed-table-loading {
        display: none !important;
    }
</style>

@if($search_status==true)

<div class="h-auto max-h-fit w-full flex flex-row justify-center items-center gap-2.5 rounded-[6px] mb-1">
    <div
        class="flex items-center gap-[6px] self-stretch bg-white px-[12px] py-[8px] rounded-lg border border-[#d0d5dd] flex-grow sm:w-1/3">
        <i class="fa-solid fa-magnifying-glass text-sm text-gray-500"></i>
        <!-- ⚡ Bootstrap Table akan otomatis deteksi input ini -->
        <input id="customSearchInput" type="text" placeholder="Search"
            class="font-medium border-none outline-none p-1 focus:ring-0 w-full text-sm leading-[14.6px] text-[#344054]">
    </div>
    {{ $put_something }}
</div>
@endif

<div class="overflow-hidden pb-2 pt-0 w-full">
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <div class="inline-block w-full align-middle">
            <div>
                <table id="{{ $id }}" data-toggle="table" data-search="true"
                    data-search-selector="#customSearchInput" data-filter-control="true" data-show-loading="false"
                    class="min-w-full table-auto border border-gray-200 rounded-lg text-sm text-blue-900 border-collapse {{ $cls }}">

                    {{-- <thead class="bg-blue-50 bg-[f4f4f5]  rounded-lg text-center align-middle"> --}}
                    <thead class="bg-[f4f4f5]  rounded-lg text-center align-middle">
                        <th data-formatter="indexFormatter" data-align="center" width="5%">No</th>
                        {{ $table_header }}
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 text-center align-middle">
                        {{ $table_column }}
                    </tbody>
                </table>
            </div>

            <script>
                $(function() {
                    const tableId = "{{ $id }}";
                    const $table = $('#' + tableId);

                    // ✅ Formatter untuk nomor urut
                    window.indexFormatter = function(value, row, index) {
                        const opts = $table.bootstrapTable('getOptions');
                        const offset = (opts.pageNumber - 1) * opts.pageSize;
                        return offset + index + 1;
                    };

                    // ✅ Inisialisasi tabel dengan fitur bawaan Bootstrap Table
                    $table.bootstrapTable({
                        onSort: function(name, order) {
                            updateSortIcons(name, order);
                        }
                    });

                    // ✅ Ganti ikon sort sesuai arah
                    function updateSortIcons(columnName, order) {
                        const $thead = $(`#${tableId}`).closest('.bootstrap-table').find(
                        '.fixed-table-header thead, thead');
                        $thead.find('th i.sort-icon')
                            .removeClass('bi-sort-up bi-sort-down text-blue-500')
                            .addClass('bi-filter text-gray-400');

                        const $activeTh = $thead.find(`th[data-field="${columnName}"]`);
                        const $icon = $activeTh.find('i.sort-icon');

                        if ($icon.length) {
                            $icon.removeClass('bi-filter text-gray-400');
                            if (order === 'asc') $icon.addClass('bi-sort-up text-blue-500');
                            else if (order === 'desc') $icon.addClass('bi-sort-down text-blue-500');
                            else $icon.addClass('bi-filter text-gray-400');
                        }
                    }
                });

                // ✅ Responsif wrapper
                $(function() {
                    function updateWrapperWidth() {
                        const sidebar = document.getElementById("sidebar");
                        const wrapper = document.getElementById("wrapper-table");
                        if (sidebar && wrapper) {
                            const sidebarWidth = sidebar.offsetWidth;
                            wrapper.style.width = `calc(100% - ${sidebarWidth}px)`;
                        }
                    }

                    window.addEventListener("scroll", updateWrapperWidth);
                    window.addEventListener("resize", updateWrapperWidth);
                    document.getElementById("wrapper-table")?.addEventListener("scroll", updateWrapperWidth);
                    updateWrapperWidth();
                });
            </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        </div>
    </div>
</div>
