@extends('kelola_data.base')

@section('header-base')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* No custom styles needed - using existing design system */
    </style>
@endsection

@section('page-name')
    <div
        class="flex flex-col md:flex-row items-center gap-[11.749480247497559px] self-stretch px-1 pt-[14.686850547790527px] pb-[13.952507972717285px]">
        <div class="flex w-full flex-col gap-[2.9373700618743896px] grow">
            <div class="flex items-center gap-[5.874740123748779px] self-stretch">
                <span class="font-medium text-2xl leading-[20.56159019470215px] text-[#101828]">Dashboard Pendidikan
                    {{ strtoupper($type) }}</span>
            </div>
            <span class="font-normal text-[10.280795097351074px] leading-[14.686850547790527px] text-[#1f2028]">
                Statistik jenjang pendidikan {{ $type == 'all' ? 'pegawai' : ($type == 'tpa' ? 'TPA' : 'dosen') }} per {{ $type == 'tpa' ? 'bagian' : 'unit' }} (D3 hingga S3)
            </span>
        </div>
        <div class="flex items-center w-full justify-end gap-[11.749480247497559px]">
            <button onclick="printTable()" class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-gray-600 px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-gray-600 hover:bg-gray-700 transition">
                    <i class="bi bi-printer text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Print</span>
                </div>
            </button>
            <button onclick="exportToCSV()" class="flex rounded-[5.874740123748779px]">
                <div
                    class="flex justify-center items-center gap-[5.874740123748779px] bg-green-600 px-[11.749480247497559px] py-[7.343425273895264px] rounded-[5.874740123748779px] border border-green-600 hover:bg-green-700 transition">
                    <i class="bi bi-file-earmark-excel text-sm text-white"></i>
                    <span class="font-medium text-[10.28px] leading-[14.68px] text-white">Export CSV</span>
                </div>
            </button>
        </div>
    </div>
@endsection

@section('content-base')
    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg">
        <a href="{{ route('manage.dashboard-prodi.pendidikan', ['type' => 'all']) }}" 
           class="px-6 py-3 text-sm font-medium {{ $type == 'all' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
            ALL
        </a>
        <a href="{{ route('manage.dashboard-prodi.pendidikan', ['type' => 'tpa']) }}" 
           class="px-6 py-3 text-sm font-medium {{ $type == 'tpa' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
            TPA
        </a>
        <a href="{{ route('manage.dashboard-prodi.pendidikan', ['type' => 'dosen']) }}" 
           class="px-6 py-3 text-sm font-medium {{ $type == 'dosen' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
            DOSEN
        </a>
    </div>

    {{-- Chart & Summary Combined --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
            <h4 class="text-md font-semibold text-gray-800 mb-4">Distribusi Pendidikan {{ $type == 'all' ? 'Pegawai' : ($type == 'tpa' ? 'TPA' : 'Dosen') }}</h4>
            <div class="flex justify-center">
                <canvas id="pendidikanChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
            <h4 class="text-md font-semibold text-gray-800 mb-4">Statistik Pendidikan</h4>
            <div class="grid grid-cols-2 gap-3 mb-4">
                @if($type != 'dosen')
                <div class="p-3 bg-gray-50 rounded border border-gray-200">
                    <p class="text-[10px] text-gray-500 uppercase font-bold">D3</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totals['d3'] }} <span class="text-xs font-normal text-gray-500">pegawai</span></p>
                </div>
                <div class="p-3 bg-gray-50 rounded border border-gray-200">
                    <p class="text-[10px] text-gray-500 uppercase font-bold">S1</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totals['s1'] }} <span class="text-xs font-normal text-gray-500">pegawai</span></p>
                </div>
                @endif
                <div class="p-3 bg-gray-50 rounded border border-gray-200 {{ $type == 'dosen' ? 'col-span-1' : '' }}">
                    <p class="text-[10px] text-gray-500 uppercase font-bold">S2</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totals['s2'] }} <span class="text-xs font-normal text-gray-500">pegawai</span></p>
                </div>
                <div class="p-3 bg-gray-50 rounded border border-gray-200">
                    <p class="text-[10px] text-gray-500 uppercase font-bold">S3</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totals['s3'] }} <span class="text-xs font-normal text-gray-500">pegawai</span></p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <i class="bi bi-people-fill text-xl text-white"></i>
                        </div>
                        <span class="text-sm font-semibold text-blue-900">Total {{ $type == 'all' ? 'Pegawai' : ($type == 'tpa' ? 'TPA' : 'Dosen') }}</span>
                    </div>
                    <span class="text-3xl font-bold text-blue-900">{{ $totals['total_pegawai'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-100 rounded border border-blue-200">
                    <span class="text-xs font-bold text-blue-800">PERSENTASE S3</span>
                    <span class="text-xl font-bold text-blue-900">{{ number_format($totals['persen_s3'] * 100, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Rekapitulasi Pendidikan {{ $type == 'all' ? 'Pegawai' : ($type == 'tpa' ? 'TPA' : 'Dosen') }} per {{ $type == 'tpa' ? 'Bagian' : 'Unit' }}</h3>
        </div>

        <table id="pendidikanTable" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">No.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">{{ $type == 'tpa' ? 'Bagian' : 'Unit' }}
                    </th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">
                        Total</th>
                    @if($type != 'dosen')
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">D3
                    </th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">S1
                    </th>
                    @endif
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">S2
                    </th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">S3
                    </th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border">% S3
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($prodiStats as $index => $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 text-center border">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-left border">
                            <div class="flex items-center gap-2">
                                <div class="font-medium text-gray-900">{{ $stat['nama_prodi'] }}</div>
                                @if($type == 'all')
                                    <span class="px-1.5 py-0.5 {{ $stat['type'] == 'Program Studi' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-gray-50 text-gray-600 border-gray-200' }} text-[10px] font-semibold rounded border">
                                        {{ $stat['type'] == 'Program Studi' ? 'Prodi' : $stat['type'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">{{ $stat['fakultas'] }}</div>
                        </td>
                        <td class="px-3 py-3 text-center border font-semibold">{{ $stat['total_pegawai'] }}</td>
                        @if($type != 'dosen')
                        <td class="px-2 py-3 text-center border">{{ $stat['d3'] }}</td>
                        <td class="px-2 py-3 text-center border">{{ $stat['s1'] }}</td>
                        @endif
                        <td class="px-2 py-3 text-center border">
                            <span class="px-2 py-1 bg-pink-100 text-pink-800 rounded">{{ $stat['s2'] }}</span>
                        </td>
                        <td class="px-2 py-3 text-center border">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $stat['s3'] }}</span>
                        </td>
                        <td class="px-3 py-3 text-center border">
                            <span
                                class="font-semibold text-gray-700">{{ number_format($stat['persen_s3'] * 100, 2) }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data unit
                        </td>
                    </tr>
                @endforelse

                @if ($prodiStats->count() > 0)
                    <tr class="bg-gray-100 font-bold border-t-2 border-gray-300">
                        <td colspan="2" class="px-4 py-3 text-center border">TOTAL</td>
                        <td class="px-3 py-3 text-center border">{{ $totals['total_pegawai'] }}</td>
                        @if($type != 'dosen')
                        <td class="px-2 py-3 text-center border">{{ $totals['d3'] }}</td>
                        <td class="px-2 py-3 text-center border">{{ $totals['s1'] }}</td>
                        @endif
                        <td class="px-2 py-3 text-center border">{{ $totals['s2'] }}</td>
                        <td class="px-2 py-3 text-center border">{{ $totals['s3'] }}</td>
                        <td class="px-3 py-3 text-center border">{{ number_format($totals['persen_s3'] * 100, 2) }}%</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Keterangan --}}
    <div class="mt-4 p-4 bg-white rounded-lg shadow-sm border border-gray-200">
        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="bi bi-info-circle-fill text-blue-600"></i>
            Keterangan
        </h4>
        <div class="text-xs text-gray-700 space-y-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @if($type != 'dosen')
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <strong>D3:</strong> Diploma III
                </div>
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <strong>S1:</strong> Sarjana / Diploma IV
                </div>
                @endif
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <strong>S2:</strong> Magister
                </div>
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <strong>S3:</strong> Doktor
                </div>
            </div>
            <div class="bg-gray-50 p-2 rounded border border-gray-200 mt-2">
                <strong>% S3:</strong> Persentase pegawai berpendidikan S3 = (S3 ÷ Total Pegawai) × 100%
            </div>
        </div>
    </div>
@endsection

@section('script-base')
    <script>
        // Initialize Pie Chart
        document.addEventListener('DOMContentLoaded', function() {
            const chartCanvas = document.getElementById('pendidikanChart');
            if (!chartCanvas) return;
            
            const ctx = chartCanvas.getContext('2d');
            const totals = @json($totals);
            const type = '{{ $type }}';

            let labels = ['D3', 'S1', 'S2', 'S3'];
            let data = [totals.d3, totals.s1, totals.s2, totals.s3];
            let colors = [
                'rgb(156, 163, 175)', // gray-400
                'rgb(52, 211, 153)', // emerald-400
                'rgb(219, 39, 119)', // pink-600
                'rgb(37, 99, 235)' // blue-600
            ];

            if (type === 'dosen') {
                labels = ['S2', 'S3'];
                data = [totals.s2, totals.s3];
                colors = ['rgb(219, 39, 119)', 'rgb(37, 99, 235)'];
            }

            // Only render if there is data
            const totalData = data.reduce((a, b) => a + b, 0);
            if (totalData === 0) {
                const container = chartCanvas.parentElement;
                container.innerHTML = '<div class="flex items-center justify-center h-[280px] text-gray-400 italic">Data pendidikan belum tersedia</div>';
                return;
            }

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' pegawai';
                                    const percentage = totals.total_pegawai > 0 ?
                                        ((context.parsed / totals.total_pegawai) * 100).toFixed(2) :
                                        0;
                                    label += ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });

        function printTable() {
            var printContents = document.getElementById('pendidikanTable').outerHTML;
            var printWindow = window.open('', '', 'height=600,width=800');

            printWindow.document.write('<html><head><title>Dashboard Pendidikan {{ strtoupper($type) }}</title>');
            printWindow.document.write('<style>table { border-collapse: collapse; width: 100%; font-size: 11px; }');
            printWindow.document.write('th, td { border: 1px solid #000; padding: 6px; text-align: center; }');
            printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }</style></head><body>');
            printWindow.document.write('<h2 style="text-align: center;">Dashboard Pendidikan {{ $type == "all" ? "Pegawai" : ($type == "tpa" ? "TPA" : "Dosen") }}</h2>');
            printWindow.document.write('<h3 style="text-align: center;">Telkom University Surabaya</h3>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.print();
        }

        function exportToCSV() {
            const prodiStats = @json($prodiStats);
            const type = '{{ $type }}';
            let csv = [];

            let header = ['No', 'Unit', 'Fakultas', 'Total'];
            if (type !== 'dosen') {
                header.push('D3', 'S1');
            }
            header.push('S2', 'S3', '%S3');
            csv.push(header.join(','));

            prodiStats.forEach((stat, index) => {
                let row = [
                    index + 1,
                    `"${stat.nama_prodi}"`,
                    `"${stat.fakultas}"`,
                    stat.total_pegawai
                ];
                if (type !== 'dosen') {
                    row.push(stat.d3, stat.s1);
                }
                row.push(stat.s2, stat.s3, (stat.persen_s3 * 100).toFixed(2) + '%');
                csv.push(row.join(','));
            });

            const totals = @json($totals);
            let totalRow = ['TOTAL', '', '', totals.total_pegawai];
            if (type !== 'dosen') {
                totalRow.push(totals.d3, totals.s1);
            }
            totalRow.push(totals.s2, totals.s3, (totals.persen_s3 * 100).toFixed(2) + '%');
            csv.push(totalRow.join(','));

            var csvFile = new Blob([csv.join('\n')], {
                type: 'text/csv;charset=utf-8;'
            });
            var downloadLink = document.createElement('a');
            downloadLink.download = 'dashboard_pendidikan_' + new Date().toISOString().slice(0, 10) + '.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
@endsection
