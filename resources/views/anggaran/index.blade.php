<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Daftar Anggaran</h1>

        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-3">
                <a href="{{ route('anggaran.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Anggaran
                </a>
                <a href="{{ route('anggaran.laporan') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Laporan Anggaran
                </a>
            </div>
            @if (session('success'))
                <div class="px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="px-4 py-2 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-md">
                    {{ session('warning') }}
                </div>
            @endif
        </div>

        {{-- Filter Tahun dan Bulan --}}
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
            <form action="{{ route('anggaran.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-32">
                    <label for="tahun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tahun
                    </label>
                    <select name="tahun" id="tahun"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm">
                        @foreach ($tahunList as $year)
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Deskripsi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jumlah Anggaran (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Periode Awal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Periode Akhir</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->kategori->nama_kategori }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->deskripsi ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">Rp
                                {{ number_format($item->jumlah_batas, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->periode_awal->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->periode_akhir->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('anggaran.edit', $item->id_anggaran) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm mr-1">Edit</a>
                                <form action="{{ route('anggaran.destroy', $item->id_anggaran) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus anggaran ini?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data anggaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
