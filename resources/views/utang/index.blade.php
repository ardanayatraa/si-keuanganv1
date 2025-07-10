<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Daftar Utang</h1>

        <div class="flex justify-between items-center mb-4">
            <div class="space-x-2">
                <a href="{{ route('utang.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                    Tambah Utang
                </a>
                <a href="{{ route('utang.pembayaran.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">
                    Pembayaran Utang
                </a>
            </div>
            @if (session('success'))
                <div class="px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        {{-- Filter Tanggal Pinjam --}}
        <form action="{{ route('utang.index') }}" method="GET" class="mb-4 flex space-x-4">
            <div>

                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $start) }}"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            </div>
            <div>

                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $end) }}"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Filter</button>
                <a href="{{ route('utang.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Rekening</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Sisa Hutang</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Deskripsi</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->rekening->nama_rekening }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">Rp
                                {{ number_format($item->jumlah, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">Rp
                                {{ number_format($item->sisa_hutang, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($item->status == 'belum lunas')
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        Belum Lunas
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->deskripsi ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('utang.edit', $item->id_utang) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm mr-1">Edit</a>
                                @if ($item->status !== 'lunas')
                                    <a href="{{ route('utang.pembayaran.create') }}?id_piutang={{ $item->id_piutang }}"
                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">Bayar</a>
                                @endif
                                <form action="{{ route('utang.destroy', $item->id_utang) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus utang ini?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data utang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
