{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Dashboard</h1>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Saldo</h3>
                <p class="mt-2 text-3xl font-bold text-indigo-600">
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

        {{-- Grafik --}}
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
    </div>

    {{-- Load ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Data dari controller
        const labels = @json($labels);
        const pemasukanData = @json($pemasukanData);
        const pengeluaranData = @json($pengeluaranData);

        // Opsi untuk Grafik Pengeluaran
        const optionsPengeluaran = {
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
                    formatter: val => val.toLocaleString()
                }
            },
            stroke: {
                curve: 'smooth'
            },
            colors: ['#ef4444'],
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
                    formatter: val => val.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    })
                }
            }
        };

        // Render Pengeluaran
        new ApexCharts(document.querySelector("#chart-pengeluaran"), optionsPengeluaran).render();

        // Opsi untuk Grafik Pemasukan
        const optionsPemasukan = {
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
                    formatter: val => val.toLocaleString()
                }
            },
            stroke: {
                curve: 'smooth'
            },
            colors: ['#10b981'],
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
                    formatter: val => val.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    })
                }
            }
        };

        // Render Pemasukan
        new ApexCharts(document.querySelector("#chart-pemasukan"), optionsPemasukan).render();
    </script>
</x-app-layout>
