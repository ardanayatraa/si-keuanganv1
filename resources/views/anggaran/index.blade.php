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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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

        {{-- Filter Periode --}}
        <form action="{{ route('anggaran.index') }}" method="GET" class="mb-4 flex space-x-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Periode Awal ≥
                </label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $start) }}"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Periode Akhir ≤
                </label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $end) }}"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Filter</button>
                <a href="{{ route('anggaran.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Deskripsi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Batas (Rp)</th>
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
