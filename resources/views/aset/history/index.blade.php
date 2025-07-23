<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Riwayat Nilai Aset: {{ $aset->nama_aset }}
        </h1>

        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-2">
                <a href="{{ route('aset.show', $aset->id_aset) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                    Kembali ke Detail Aset
                </a>
                <a href="{{ route('aset.history.create', $aset->id_aset) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    Tambah Riwayat Baru
                </a>
            </div>
            @if (session('success'))
                <div class="px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Informasi Aset</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Nama Aset:</span>
                    <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $aset->nama_aset }}</span>
                </div>

                <div>
                    <span class="text-gray-500 dark:text-gray-400">Jenis Aset:</span>
                    <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $aset->jenis_aset }}</span>
                </div>

                <div>
                    <span class="text-gray-500 dark:text-gray-400">Nilai Aset Saat Ini:</span>
                    <span class="ml-2 text-gray-900 dark:text-white font-medium">Rp
                        {{ number_format($aset->nilai_aset, 0, ',', '.') }}</span>
                </div>

                <div>
                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                    <span
                        class="ml-2 px-2 py-1 rounded-full text-xs {{ $aset->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $aset->status === 'aktif' ? 'Aktif' : 'Terjual' }}
                    </span>
                </div>
            </div>
        </div>

        @if ($history->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Tanggal Perubahan</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Nilai Lama</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Nilai Baru</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Selisih</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Keterangan</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($history as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ $item->tanggal_perubahan->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                    {{ number_format($item->nilai_lama, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                    {{ number_format($item->nilai_baru, 0, ',', '.') }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right {{ $item->nilai_baru > $item->nilai_lama ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item->nilai_baru > $item->nilai_lama ? '+' : '' }}{{ number_format($item->nilai_baru - $item->nilai_lama, 0, ',', '.') }}
                                    <span class="text-xs">
                                        ({{ $item->nilai_baru > $item->nilai_lama ? '+' : '' }}{{ number_format((($item->nilai_baru - $item->nilai_lama) / max(1, $item->nilai_lama)) * 100, 2) }}%)
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100 max-w-xs truncate">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('aset.history.show', [$aset->id_aset, $item->id_history]) }}"
                                        class="inline-block px-3 py-1 mr-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">
                                        Detail
                                    </a>
                                    <a href="{{ route('aset.history.edit', [$aset->id_aset, $item->id_history]) }}"
                                        class="inline-block px-3 py-1 mr-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                        Edit
                                    </a>
                                    <form
                                        action="{{ route('aset.history.destroy', [$aset->id_aset, $item->id_history]) }}"
                                        method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus riwayat ini?')"
                                            class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-700 p-8 rounded-lg text-center">
                <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat perubahan nilai untuk aset ini.</p>
                <a href="{{ route('aset.history.create', $aset->id_aset) }}"
                    class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    Tambah Riwayat Nilai
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
