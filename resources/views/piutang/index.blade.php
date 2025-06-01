<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Daftar Piutang
        </h1>

        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('piutang.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                Tambah Piutang
            </a>
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
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Rekening
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Sisa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Tanggal Pinjam
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Tanggal Jatuh Tempo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->pengguna->username }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->rekening->nama_rekening }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                {{ number_format($item->jumlah, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                {{ number_format($item->sisa_piutang, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->deskripsi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ ucfirst($item->status) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('piutang.edit', $item->id_piutang) }}"
                                    class="inline-block px-3 py-1 mr-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('piutang.destroy', $item->id_piutang) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Yakin ingin menghapus piutang ini?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data piutang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
