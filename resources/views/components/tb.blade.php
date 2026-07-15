@props(['id' => null, 'cls' => null, 'search_status' => true])
@aware(['table_header', 'table_column', 'put_something'])

<style>
    /* Elevasi Apple: Memberi kontras di atas background putih */
    .apple-wrapper {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        border: 1px solid #f2f2f7;
        padding: 4px;
        /* Tambahan: Pastikan wrapper mengatur overflow agar sticky bekerja di dalamnya */
        position: relative;
    }

    .table-container-sticky {
        min-height: 50vh !important;
        max-height: 80vh; /* Sesuaikan tinggi maksimal table sebelum scroll muncul */
        overflow-y: auto;
        border-radius: 16px;
    }

    .search-input-wrapper {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1.5px solid #f2f2f7 !important;
        background-color: #f9f9fb;
        height: 44px;
        display: flex;
        align-items: center;
    }

    .search-input-wrapper:hover {
        background-color: #f2f2f7;
    }

    .search-input-wrapper:focus-within {
        background-color: #ffffff;
        border-color: #007AFF !important;
        box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.08);
    }

    /* Global Filter Component Styling */
    .filter-group select, .filter-group input, .filter-group button {
        height: 44px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .filter-select {
        background-color: #f9f9fb;
        border: 1.5px solid #f2f2f7;
        color: #1d1d1f;
        padding-left: 1rem;
        padding-right: 2.5rem;
        outline: none;
    }

    .filter-select:hover {
        background-color: #f2f2f7;
    }

    .filter-select:focus {
        background-color: #ffffff;
        border-color: #007AFF;
        box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.08);
    }

    .filter-btn-primary {
        background-color: #0070ff;
        color: #ffffff;
        padding: 0 1.5rem;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 112, 255, 0.15);
    }

    .filter-btn-primary:hover {
        background-color: #005fe0;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 112, 255, 0.25);
    }

    .filter-btn-primary:active {
        transform: translateY(0);
    }

    /* Bootstrap Table Customization */
    .bootstrap-table .fixed-table-container {
        border: none !important;
        background: transparent !important;
    }

    .th-inner {
        color: #1d1d1f !important;
        font-weight: 700 !important;
        font-size: 13px;
    }

    /* Styling Header: Sticky Header Logic */
    thead.apple-header {
        background-color: #fbfbfd;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 1.5px solid #f2f2f7;
    }

    /* Memastikan background header tidak transparan saat scroll */
    thead.apple-header th {
        background-color: #fbfbfd !important;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: inset 0 -1.5px 0 #f2f2f7; /* Pengganti border agar tidak hilang saat sticky */
    }

    .fixed-table-loading {
        display: none !important;
    }

    .bootstrap-table .fixed-table-toolbar {
        display: none;
    }
</style>
<div class="min-h-[50vh] h-[77vh] max-h-fit pb-4 sticky mb-10 top-0 z-10">
    @if ($search_status == true)
        <div id="cekser" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4 mb-6 px-1">
            <div class="search-input-wrapper px-4 rounded-[14px] flex-grow shadow-sm">
                <i class="fa-solid fa-magnifying-glass text-[#86868b] text-sm flex-shrink-0"></i>
                <input id="customSearchInput" type="text" placeholder="Cari data..."
                    class="bg-transparent border-none outline-none w-full text-[14px] text-[#1d1d1f] placeholder-[#86868b] focus:ring-0 leading-none ml-1">
            </div>
            @if (isset($put_something))
                <div class="filter-group flex-shrink-0">
                    {{ $put_something }}
                </div>
            @endif
        </div>
    @endif

    <div class="w-full flex-1 min-h-[50vh] h-[70vh] mb-10 max-h-fit">
        <div class="apple-wrapper overflow-hidden flex-1 bg-white">
            <div class="overflow-x-auto min-h-max h-max table-container-sticky">
                <table id="{{ $id }}" data-toggle="table" data-search="true" data-filter-control="true"
                    data-show-loading="false" data-visible-search="false"
                    @if (request()->has('sort') && request()->has('order'))
                        data-sort-name="{{ request('sort') }}"
                        data-sort-order="{{ request('order') }}" @endif
                        class="min-w-full pb-4 flex-1 min-h-max max-h-fit align-middle {{ $cls }}">

                    <thead class="sticky top-0 z-10">
                        <tr>
                            <th data-formatter="indexFormatter" data-align="center" class="py-4 text-[#86868b]"
                                width="65px">No</th>
                            {{ $table_header }}
                        </tr>
                    </thead>
                    <tbody class="divide-y flex-1   divide-[#f2f2f7]">
                        {{ $table_column }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('script-under-base')
    <script src="jquery.js"></script>
    <script src="bootstrap.bundle.min.js"></script>
    <script src="bootstrap-table.min.js"></script>
    <script>
        window.indexFormatter = function(value, row, index) {
            const tableId = "{{ $id }}";
            const $table = $('#' + tableId);
            if ($table.data('bootstrap.table')) {
                const opts = $table.bootstrapTable('getOptions');
                return ((opts.pageNumber - 1) * opts.pageSize) + index + 1;
            }
            return index + 1;
        };

        $(function() {
            const tableId = "{{ $id }}";
            const $table = $('#' + tableId);
            const $searchInput = $('#customSearchInput');

            $searchInput.on('input', function() {
                $table.bootstrapTable('resetSearch', $(this).val());
            });

            $table.on('sort.bs.table', function(e, name, order) {
                const $thead = $table.closest('.bootstrap-table').find('thead');
                $thead.find('i.sort-icon').removeClass('bi-sort-up bi-sort-down text-[#007AFF]').addClass(
                    'bi-filter text-[#aeaeb2]');

                const $activeTh = $thead.find(`th[data-field="${name}"]`);
                const $icon = $activeTh.find('i.sort-icon');

                if ($icon.length) {
                    $icon.removeClass('bi-filter text-[#aeaeb2]');
                    $icon.addClass(order === 'asc' ? 'bi-sort-up text-[#007AFF]' :
                        'bi-sort-down text-[#007AFF]');
                }
            });
        });
    </script>
@endpush
