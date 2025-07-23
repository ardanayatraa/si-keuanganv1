<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Daftar Aset
        </h1>

        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-2">
                <a href="{{ route('aset.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                    Tambah Aset
                </a>
                <a href="{{ route('aset.total-wealth') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    Total Kekayaan
                </a>
            </div>
            @if (session('success'))
                <div class="px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Nama Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Tanggal Perolehan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Nilai</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Status</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr class="{{ $item->status === 'terjual' ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->nama_aset }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->jenis_aset }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->tanggal_perolehan->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                {{ number_format($item->nilai_aset, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->status === 'aktif' ? 'Aktif' : 'Terjual' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('aset.show', $item->id_aset) }}"
                                    class="inline-block px-3 py-1 mr-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">
                                    Detail
                                </a>
                                <a href="{{ route('aset.edit', $item->id_aset) }}"
                                    class="inline-block px-3 py-1 mr-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                    Edit
                                </a>
                                <a href="{{ route('aset.toggle-status', $item->id_aset) }}"
                                    class="inline-block px-3 py-1 mr-1 {{ $item->status === 'aktif' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-md text-sm">
                                    {{ $item->status === 'aktif' ? 'Tandai Terjual' : 'Aktifkan' }}
                                </a>
                                <form action="{{ route('aset.destroy', $item->id_aset) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus aset ini?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data aset.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->count() > 0)
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Total Nilai Aset Aktif</h3>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
            </div>
        @endif
    </div>
</x-app-layout>
