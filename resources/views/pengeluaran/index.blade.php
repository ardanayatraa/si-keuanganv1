<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Daftar Pengeluaran</h1>
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>{{ session('warning') }}</p>
            </div>
        @endif

        {{-- Konten Utama --}}
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('pengeluaran.create') }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                Tambah Pengeluaran
            </a>
        </div>

        {{-- Filter --}}
        <form action="{{ route('pengeluaran.index') }}" method="GET" class="mb-4 flex space-x-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                    Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $start) }}"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                    Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $end) }}"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Filter</button>
                <a href="{{ route('pengeluaran.index') }}"
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
                            Rekening</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Deskripsi</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Bukti</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4">{{ $item->kategori->nama_kategori }}</td>
                            <td class="px-6 py-4">{{ $item->rekening->nama_rekening }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">{{ $item->deskripsi ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->bukti_transaksi)
                                    <a href="{{ Storage::url($item->bukti_transaksi) }}" target="_blank"
                                        class="text-blue-600 hover:underline text-sm">Lihat</a>
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pengeluaran.edit', $item->id_pengeluaran) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm mr-1">Edit</a>
                                <form action="{{ route('pengeluaran.destroy', $item->id_pengeluaran) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus data ini?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada
                                data pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
