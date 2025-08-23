<x-app-layout>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                color: black !important;
                background: white !important;
                font-size: 12px !important;
            }
            
            .bg-blue-600, .bg-red-600, .bg-green-600 {
                color: black !important;
                border: 1px solid #000 !important;
                background: white !important;
                print-color-adjust: exact !important;
            }
            
            .text-white {
                color: black !important;
            }
            
            .text-blue-100, .text-red-100, .text-green-100 {
                color: #666 !important;
            }
            
            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }
            
            th, td {
                border: 1px solid #000 !important;
                padding: 6px !important;
                font-size: 11px !important;
            }
            
            th {
                background: #f0f0f0 !important;
                font-weight: bold !important;
                text-align: center !important;
            }
            
            .rounded-lg, .rounded-full, .rounded-md {
                border-radius: 0 !important;
            }
            
            .shadow-sm {
                box-shadow: none !important;
            }
            
            /* Header untuk print */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }
            
            .print-date {
                display: block !important;
                text-align: right;
                margin-bottom: 20px;
                font-size: 10px;
                color: #666;
            }
            
            /* Summary cards untuk print */
            .grid {
                display: flex !important;
                justify-content: space-between !important;
                margin-bottom: 20px !important;
            }
            
            .grid > div {
                flex: 1 !important;
                margin: 0 5px !important;
                padding: 10px !important;
                border: 1px solid #000 !important;
                text-align: center !important;
            }
            
            /* Status badges */
            .bg-red-100, .bg-yellow-100, .bg-green-100 {
                border: 1px solid #000 !important;
                background: white !important;
                color: black !important;
            }
            
            /* Progress bars */
            .bg-gray-200 {
                background: #e0e0e0 !important;
                border: 1px solid #666 !important;
            }
            
            .bg-red-600, .bg-yellow-500, .bg-green-500 {
                background: #666 !important;
            }
        }
        
        @page {
            margin: 1.5cm;
            size: A4;
        }
        
        .hidden {
            display: none;
        }
    </style>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        
        {{-- Header khusus untuk print --}}
        <div class="print-header hidden" id="print-header">
            <h1 class="text-3xl font-bold">LAPORAN ANGGARAN</h1>
            <p class="text-lg mt-2">Sistem Keuangan Personal</p>
            <p class="text-md mt-1">
                Periode: {{ $bulan ? 
                    \Carbon\Carbon::createFromFormat('m', $bulan)->format('F') . ' ' . $tahun : 
                    'Tahun ' . $tahun 
                }}
            </p>
        </div>
        
        <div class="print-date hidden" id="print-date">
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</p>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Anggaran</h1>
            <div class="flex space-x-3 no-print">
                <button onclick="printLaporan()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Laporan
                </button>
                <a href="{{ route('anggaran.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Anggaran
                </a>
            </div>
        </div>

        {{-- Filter Tahun dan Bulan --}}
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 no-print">
            <form action="{{ route('anggaran.laporan') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-32">
                    <label for="tahun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tahun
                    </label>
                    <select name="tahun" id="tahun" 
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm">
                        @foreach($tahunList as $year)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-32">
                    <label for="bulan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Bulan
                    </label>
                    <select name="bulan" id="bulan" 
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm">
                        <option value="">Semua Bulan</option>
                        <option value="1" {{ $bulan == '1' ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ $bulan == '2' ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ $bulan == '3' ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ $bulan == '4' ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $bulan == '5' ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ $bulan == '6' ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ $bulan == '7' ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ $bulan == '8' ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ $bulan == '9' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $bulan == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $bulan == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $bulan == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                
                <div>
                    <button type="submit" 
                        class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-600 p-6 rounded-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Anggaran</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-600 p-6 rounded-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Total Terpakai</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-red-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-600 p-6 rounded-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Sisa Anggaran</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Periode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Anggaran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Terpakai
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Sisa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($laporan as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->kategori->nama_kategori ?? 'N/A' }}
                                </div>
                                @if($item->deskripsi)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->deskripsi }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($item->periode_awal)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($item->periode_akhir)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($item->jumlah_batas, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                Rp {{ number_format($item->total_realisasi, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="{{ $item->sisa_anggaran >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    Rp {{ number_format($item->sisa_anggaran, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full {{ $item->persentase_terpakai > 100 ? 'bg-red-600' : ($item->persentase_terpakai > 80 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                             style="width: {{ min($item->persentase_terpakai, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ number_format($item->persentase_terpakai, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->persentase_terpakai > 100)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Over Budget
                                    </span>
                                @elseif($item->persentase_terpakai > 80)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Warning
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Safe
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                Tidak ada data anggaran untuk periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Info --}}
        <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
            <p class="mb-2">
                <strong>Periode Laporan:</strong> 
                {{ $bulan ? 
                    \Carbon\Carbon::createFromFormat('m', $bulan)->format('F') . ' ' . $tahun : 
                    'Semua bulan tahun ' . $tahun 
                }}
            </p>
            <p>
                <strong>Total {{ count($laporan) }} anggaran</strong> ditemukan.
                @if($totalRealisasi > $totalAnggaran)
                    <span class="text-red-600 font-semibold">⚠️ Total pengeluaran melebihi anggaran sebesar Rp {{ number_format($totalRealisasi - $totalAnggaran, 0, ',', '.') }}</span>
                @endif
            </p>
        </div>
    </div>
    
    <script>
        // Tampilkan header print saat print
        window.addEventListener('beforeprint', function() {
            document.getElementById('print-header').classList.remove('hidden');
            document.getElementById('print-date').classList.remove('hidden');
        });
        
        // Sembunyikan header print setelah print
        window.addEventListener('afterprint', function() {
            document.getElementById('print-header').classList.add('hidden');
            document.getElementById('print-date').classList.add('hidden');
        });
        
        // Fungsi untuk print dengan menyesuaikan tampilan
        function printLaporan() {
            // Tampilkan header print
            document.getElementById('print-header').classList.remove('hidden');
            document.getElementById('print-date').classList.remove('hidden');
            
            // Delay sebentar lalu print
            setTimeout(function() {
                window.print();
            }, 100);
        }
    </script>
</x-app-layout>
