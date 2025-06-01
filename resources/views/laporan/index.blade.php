<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Laporan Keuangan
        </h1>

        {{-- Form Pilih Periode --}}
        <form action="{{ route('laporan.generate') }}" method="POST" class="space-y-4 mb-6">
            @csrf
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Periode:</label>
                <select name="filter_type" id="filter_type"
                    class="border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    onchange="handleFilterChange(this.value)">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="minggu" @selected(old('filter_type') == 'minggu')>Mingguan</option>
                    <option value="bulan" @selected(old('filter_type') == 'bulan')>Bulanan</option>
                    <option value="tahun" @selected(old('filter_type') == 'tahun')>Tahunan</option>
                </select>
            </div>

            <div id="input-mingguan" class="hidden">
                <label for="filter_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Tanggal (untuk minggu)
                </label>
                <input type="date" name="filter_date" id="filter_date" value="{{ old('filter_date') }}"
                    class="mt-1 block w-1/3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                @error('filter_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="input-bulanan" class="hidden">
                <label for="filter_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Bulan
                </label>
                <input type="month" name="filter_month" id="filter_month" value="{{ old('filter_month') }}"
                    class="mt-1 block w-1/3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                @error('filter_month')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="input-tahunan" class="hidden">
                <label for="filter_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Tahun
                </label>
                <input type="number" name="filter_year" id="filter_year" min="2000" max="2100"
                    value="{{ old('filter_year') }}"
                    class="mt-1 block w-1/4 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white"
                    placeholder="2025" />
                @error('filter_year')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                Generate Laporan
            </button>
        </form>

        {{-- Daftar Laporan (History) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Total Pemasukan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Total Pengeluaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Saldo Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->periode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ number_format($item->total_pemasukan, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ number_format($item->total_pengeluaran, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ number_format($item->saldo_akhir, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->created_at->format('d M Y – H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script untuk menampilkan input sesuai filter_type --}}
    <script>
        function handleFilterChange(value) {
            document.getElementById('input-mingguan').classList.add('hidden');
            document.getElementById('input-bulanan').classList.add('hidden');
            document.getElementById('input-tahunan').classList.add('hidden');

            if (value === 'minggu') {
                document.getElementById('input-mingguan').classList.remove('hidden');
            } else if (value === 'bulan') {
                document.getElementById('input-bulanan').classList.remove('hidden');
            } else if (value === 'tahun') {
                document.getElementById('input-tahunan').classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const oldType = "{{ old('filter_type') }}";
            if (oldType) {
                handleFilterChange(oldType);
            }
        });
    </script>
</x-app-layout>
