<x-app-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Dashboard</h1>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Saldo</h3>
                <p class="mt-2 text-3xl font-bold text-yellow-600">
                    {{ number_format($totalSaldo, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Pengeluaran</h3>
                <p class="mt-2 text-3xl font-bold text-red-500">
                    {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Pemasukan</h3>
                <p class="mt-2 text-3xl font-bold text-green-500">
                    {{ number_format($totalPemasukan, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Grafik 7 Hari --}}
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Grafik Pengeluaran (7 Hari Terakhir)
                </h3>
                <div id="chart-pengeluaran" class="w-full h-64"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Grafik Pemasukan (7 Hari Terakhir)
                </h3>
                <div id="chart-pemasukan" class="w-full h-64"></div>
            </div>
        </div>

        {{-- Grafik Anggaran vs Terpakai --}}
        <div class="mt-10 bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Perbandingan Anggaran vs Pengeluaran per Kategori
            </h3>
            <div id="chart-anggaran" class="w-full h-64"></div>
        </div>
    </div>

    {{-- Load ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Data 7 hari
        const labels = @json($labels);
        const pemasukanData = @json($pemasukanData);
        const pengeluaranData = @json($pengeluaranData);

        // Grafik Pengeluaran
        new ApexCharts(document.querySelector("#chart-pengeluaran"), {
            chart: {
                type: 'area',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Pengeluaran',
                data: pengeluaranData
            }],
            xaxis: {
                categories: labels,
                title: {
                    text: 'Tanggal'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah (Rp)'
                },
                labels: {
                    formatter: v => v.toLocaleString()
                }
            },
            stroke: {
                curve: 'smooth'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1
                }
            },
            tooltip: {
                y: {
                    formatter: v => v.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    })
                }
            }
        }).render();

        // Grafik Pemasukan
        new ApexCharts(document.querySelector("#chart-pemasukan"), {
            chart: {
                type: 'area',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Pemasukan',
                data: pemasukanData
            }],
            xaxis: {
                categories: labels,
                title: {
                    text: 'Tanggal'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah (Rp)'
                },
                labels: {
                    formatter: v => v.toLocaleString()
                }
            },
            stroke: {
                curve: 'smooth'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1
                }
            },
            tooltip: {
                y: {
                    formatter: v => v.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    })
                }
            }
        }).render();

        // Data Anggaran
        const labelsAnggaran = @json($labelsAnggaran);
        const batasData = @json($batasData);
        const terpakaiData = @json($terpakaiData);

        // Grafik Anggaran vs Terpakai dengan X axis kategori
        new ApexCharts(document.querySelector("#chart-anggaran"), {
            chart: {
                type: 'bar',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: [{
                    name: 'Batas Anggaran',
                    data: batasData
                },
                {
                    name: 'Terpakai',
                    data: terpakaiData
                }
            ],
            xaxis: {
                type: 'category',
                categories: labelsAnggaran,
                title: {
                    text: 'Kategori'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah (Rp)'
                },
                labels: {
                    formatter: v => v.toLocaleString()
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%'
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent', 'transparent']
            },
            tooltip: {
                y: {
                    formatter: v => v.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    })
                }
            }
        }).render();
    </script>
</x-app-layout>
