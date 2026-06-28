@php
    $active_sidebar = 'Daftar Level';
@endphp
@extends('kelola_data.base')
@section('header-base')
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- OrgChart.js -->
    <script src="https://balkan.app/js/OrgChart.js" defer></script>
    <style>


        .max-w-100 {
            max-width: 100% !important;
        }



        .nav-active {
            background-color: #0070ff;

            span {
                color: white;
            }
        }

        .radius-bottom-none {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
    </style>


@endsection
@section('page-name')
    <!-- Overlay Loading -->
    <x-page-freeze></x-page-freeze>
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch"><span
                    class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Daftar Level</span>
            </div><span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">Anda
                dapat melihat semua level yang terdaftar di sistem disini</span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">


            <x-print-tb target_id="LevelTable"></x-print-tb>
            <x-export-csv-tb target_id="LevelTable"></x-export-csv-tb>

            <button class="flex rounded-[5.874740123748779px]" onclick="window.location='{{ route('manage.level.new') }}'">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-[#0070ff] px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-[#0070ff] hover:bg-[#005fe0] transition">
                    <i class="bi bi-plus text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Tambah</span>
                </div>
            </button>
        </div>

    </div>
@endsection
@section('content-base')
    <x-modal-view :footer="false" :head="false" id="level-update" title="Level Details">
        <div class="flex flex-col gap-3 px-8 py-8">
            <!-- Header -->
            <div class="flex items-center gap-5">
                <span class="font-semibold text-xl text-[#101828]">Data Level</span>
                <button onclick="window.location=''" id="ubah-data-button"
                    class="flex items-center justify-center gap-1 bg-[#0070ff] text-white font-medium text-xs px-3 py-1 rounded border border-[#0070ff] hover:bg-[#005bd4] transition-all">
                    Ubah Data
                </button>
            </div>

            <!-- Data Grid -->
            <div class="flex gap-12 w-full">
                <div class="flex flex-col gap-2 w-1/2">
                    <span class="font-light text-sm text-black">Nama Level</span>
                    <span class="font-light text-sm text-black">Singkatan</span>
                    <span class="font-light text-sm text-black">Atasan</span>
                </div>
                <div class="flex flex-col gap-2 w-1/2">
                    <span class="font-normal text-sm text-black" id="nama-level">Directur</span>
                    <span class="font-normal text-sm text-black" id="singkatan">DIR</span>
                    <span class="font-normal text-sm text-black" id="atasan">1</span>
                </div>
            </div>
        </div>


    </x-modal-view>


    <div class="flex flex-col xl:flex-row gap-2 h-fit">
        <div class="">
            <x-tb id="LevelTable">
                <x-slot:table_header>
                    <x-tb-td nama="nama_level">Nama Level</x-tb-td>
                    <x-tb-td nama="singkatan">Singkatan</x-tb-td>
                    <x-tb-td nama="atasan">Atasan</x-tb-td>
                    <x-tb-td sorting="true" nama="urut">Urut</x-tb-td>
                    <x-tb-td>Action</x-tb-td>
                </x-slot:table_header>


                <x-slot:table_column>
                    @foreach ($levels as $level)
                        {{-- <x-tb-cl idTargetModal="level-update"> --}}

                        <x-tb-cl id="{{ $level->id }}" idTargetModal="level-update">
                            <x-tb-cl-fill id="nama-level">{{ $level['nama_level'] }}</x-tb-cl-fill>
                            <x-tb-cl-fill id="singkatan" clsText="text-center">{{ $level['singkatan_level'] }}</x-tb-cl-fill>
                            <x-tb-cl-fill id="atasan">{{ $level['atasan']['nama_level']??'-'}}</x-tb-cl-fill>
                            <x-tb-cl-fill id="urut" clsText="text-center">{{ $level['urut']??'-'}}</x-tb-cl-fill>
                            <x-tb-cl-fill>
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('manage.level.update', ['idLevel' => $level->id]) }}"
                                        onclick="return overlayArahkan(event)"
                                        class="px-3 py-1.5 border border-[#0070ff] text-[#0070ff] rounded-md text-xs font-medium hover:bg-[#0070ff] hover:text-white transition duration-200">
                                        Ubah Data
                                    </a>
                                </div>
                            </x-tb-cl-fill>
                        </x-tb-cl>
                    @endforeach
                </x-slot:table_column>

            </x-tb>
        </div>
        {{-- <div class="self-stretch flex border-l-2 border-gray-500 mx-6" style="border: 0.3px solid !immportant;"></div> --}}

        <div id="chart-container" class="flex-grow hidden flex flex-col justify-center items-center">
            <div class="w-full flex md:hidden mt-3 mb-2">
                <h1 class="text-2xl text-start font-medium">Preview Struktur</h1>
            </div>
            {{-- <div id="tree" class="h-fit sm:h-3/4"></div> --}}
            <div id="loading"
                class="flex text-center items-center h-full flex-grow-1 justify-center text-gray-600 font-semibold">
                Loading struktur organisasi...
            </div>
            <div id="tree" class="h-fit sm:h-3/4"></div>

            <span class="text-sm text-center font-normal">Klik icon di kanan bawah untuk mengembalikan ke semula</span>
        </div>

    </div>



    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('tr');

        document.addEventListener('click', function(e) {
            const row = e.target.closest('.x-tb-cl');
            if (row) {
                open_modal(row);
            }
        });

        function open_modal(elemen) {

            console.log('tes :>> ', elemen);
            const modal = document.querySelector('.modal#level-update');
            if (!modal) return;

            // Ambil data dari baris yang diklik
            const namaLevel = elemen.querySelector('#nama-level')
                ?.textContent || '';
            const singkatan = elemen.querySelector('#singkatan')
                ?.textContent || '';
            const atasan = elemen.querySelector('#atasan')?.textContent || '';
            const icode = elemen.getAttribute('id') || '';
            console.log('icode :>> ', icode);
            console.log('namaLevel,singkatan,atasan :>> ', namaLevel, singkatan, atasan, icode);
            // Masukkan data ke dalam modal
            modal.querySelector('#ubah-data-button').setAttribute('onclick',
                `window.location.href='/manage/level/update/${icode}'`);

            modal.querySelector('#nama-level').textContent = namaLevel;
            modal.querySelector('#singkatan').textContent = singkatan;
            modal.querySelector('#atasan').textContent = atasan;
        }
        // });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            [...popoverTriggerList].map(el => new bootstrap.Popover(el));
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Tampilkan loading
            const loadingEl = document.getElementById("loading");
            const treeEl = document.getElementById("tree");

            // Tunggu 1 detik sebelum menjalankan logika chart
            setTimeout(() => {
                // 🔹 Ambil data dari tabel
                const rows = document.querySelectorAll("#LevelTable .x-tb-cl");
                const nodes = [];

                rows.forEach((row, index) => {
                    const cols = row.querySelectorAll(".fill-table-row");
                    const id = (index + 1);
                    const division = cols[1]?.textContent.trim() || "";
                    const singkatan = cols[2]?.textContent.trim() || "";
                    const pid = cols[3]?.textContent.trim() || "0";

                    nodes.push({
                        id: id,
                        pid: pid !== "0" ? parseInt(pid) : null,
                        division: `${division} (${singkatan})`,
                        photo: `https://randomuser.me/api/portraits/${id % 2 === 0 ? "men" : "women"}/${20 + parseInt(id)}.jpg`
                    });
                });

                console.log("Nodes dari tabel:", nodes);

                // 🔹 Inisialisasi OrgChart
                const chart = new OrgChart(treeEl, {
                    template: "olivia",
                    enableSearch: false,
                    menu: {
                        pdf: {
                            text: "Export PDF",
                            filename: "struktur-organisasi"
                        },
                        png: {
                            text: "Export PNG",
                            filename: "struktur-organisasi"
                        },
                    },
                    toolbar: {
                        fit: true
                    },
                    mouseScrool: OrgChart.action.none,
                    scaleInitial: 0.3,
                    scaleMin: 0.3,
                    scaleMax: 6,
                    pan: true,
                    collapse: {
                        level: 7
                    },
                    nodeBinding: {
                        field_0: "division",
                        img_0: "photo",
                    },
                    nodes: nodes,
                });

                // 🌈 Template kustom
                OrgChart.templates.olivia.size = [270, 100];
                OrgChart.templates.olivia.node =
                    '<rect x="0" y="0" width="270" height="100" rx="18" ry="18" fill="#1C2762" stroke="#1C2762" stroke-width="1.5"></rect>';
                OrgChart.templates.olivia.img_0 =
                    '<defs>' +
                    '<radialGradient id="photoRing_{randId}" cx="50%" cy="50%" r="50%">' +
                    '<stop offset="60%" stop-color="#0070FF"/>' +
                    '<stop offset="100%" stop-color="#1C2762"/>' +
                    '</radialGradient>' +
                    '</defs>' +
                    '<circle cx="45" cy="50" r="28" fill="url(#photoRing_{randId})"></circle>' +
                    '<clipPath id="cp_{randId}">' +
                    '<circle cx="45" cy="50" r="24"></circle>' +
                    '</clipPath>' +
                    '<image preserveAspectRatio="xMidYMid slice" clip-path="url(#cp_{randId})" xlink:href="{val}" x="21" y="26" width="48" height="48"></image>';
                OrgChart.templates.olivia.field_0 =
                    '<text style="font-size:14px;font-weight:700;" fill="#F5F5F5" x="85" y="56">{val}</text>';

                chart.fit();

                // 🔹 Sembunyikan loading, tampilkan chart
                loadingEl.style.display = "none";
                treeEl.style.display = "block";

            }, 1000); // delay 1 detik
        });
    </script>

    <div id="pageOverlay"
        class="hidden fixed inset-0 bg-black/50 text-white text-lg flex items-center justify-center z-[9999]">
        <div class="text-center">
            <div class="loader mx-auto mb-4"></div>
            <p>Sedang mengarahkan ke halaman...</p>
        </div>
    </div>
@endsection
